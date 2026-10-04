<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Internal;

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\ConflictException;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Exceptions\ServerException;
use Aybarsm\Kong\AdminApi\Exceptions\TransportException;
use Aybarsm\Kong\AdminApi\Exceptions\UnauthorizedException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Exceptions\ValidationException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Throwable;

/**
 * The single class that talks HTTP: builds PSR-7 requests, sends them through PSR-18, decodes
 * JSON and maps error statuses to package exceptions. Resources never touch HTTP directly.
 *
 * @internal
 */
final readonly class Transport
{
    public const string METHOD_GET = 'GET';
    public const string METHOD_POST = 'POST';
    public const string METHOD_PUT = 'PUT';
    public const string METHOD_PATCH = 'PATCH';
    public const string METHOD_DELETE = 'DELETE';
    public const string METHOD_HEAD = 'HEAD';
    public const string METHOD_OPTIONS = 'OPTIONS';

    /** Header name from the spec's `adminToken` security scheme. */
    public const string HEADER_ADMIN_TOKEN = 'Kong-Admin-Token';

    public const string CONTENT_TYPE_JSON = 'application/json';

    /** Default for the spec's `Workspace` path parameter. */
    public const string DEFAULT_WORKSPACE = 'default';

    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface&StreamFactoryInterface $factory,
        private ClientConfig $config,
    ) {
    }

    /**
     * Builds an encoded request path, adding the `/{workspace}` prefix as the operation's scope requires.
     *
     * @throws InvalidArgumentException when a segment is empty
     */
    public function path(OperationScope $scope, string ...$segments): string
    {
        $workspace = match ($scope) {
            OperationScope::GlobalOnly => null,
            OperationScope::Both => $this->config->workspace,
            OperationScope::WorkspaceOnly => $this->config->workspace ?? self::DEFAULT_WORKSPACE,
        };
        if ($workspace !== null) {
            array_unshift($segments, $workspace);
        }

        $encoded = [];
        foreach ($segments as $segment) {
            if ($segment === '') {
                throw new InvalidArgumentException('Path segments (IDs, names) must not be empty.');
            }
            $encoded[] = rawurlencode($segment);
        }

        return '/' . implode('/', $encoded);
    }

    /**
     * Sends a request and returns the decoded JSON payload of a 2xx response.
     *
     * @param array<string, string|int|bool|null> $query
     * @param array<string, mixed>|null           $body
     *
     * @return array<array-key, mixed>
     *
     * @throws KongApiException
     */
    public function json(string $method, string $path, array $query = [], ?array $body = null): array
    {
        return $this->decode($this->send($method, $path, $query, $body), $method, $path);
    }

    /**
     * @return array<array-key, mixed>
     *
     * @throws UnexpectedResponseException
     */
    private function decode(ResponseInterface $response, string $method, string $path): array
    {
        $raw = (string) $response->getBody();

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new UnexpectedResponseException(
                sprintf('%s %s returned a body that is not valid JSON.', $method, $path),
                $response->getStatusCode(),
                method: $method,
                path: $path,
                previous: $e,
            );
        }

        if (!is_array($decoded)) {
            throw new UnexpectedResponseException(
                sprintf('%s %s returned JSON that is not an object or array.', $method, $path),
                $response->getStatusCode(),
                method: $method,
                path: $path,
            );
        }

        return $decoded;
    }

    /**
     * Sends a request whose successful response carries no body the caller needs.
     *
     * @param array<string, string|int|bool|null> $query
     * @param array<string, mixed>|null           $body
     *
     * @throws KongApiException
     */
    public function none(string $method, string $path, array $query = [], ?array $body = null): void
    {
        $this->send($method, $path, $query, $body);
    }

    /**
     * Sends a request expecting a 2xx or 404 without a body (e.g. HEAD): true for 2xx, false for 404.
     *
     * @throws KongApiException for any other error status or a transport failure
     */
    public function exists(string $method, string $path): bool
    {
        try {
            $this->send($method, $path, [], null);
        } catch (NotFoundException) {
            return false;
        }

        return true;
    }

    /**
     * Sends a request and returns the comma-separated values of one response header (e.g. `Allow`).
     *
     * @return list<string>
     *
     * @throws KongApiException
     */
    public function headerValues(string $method, string $path, string $header): array
    {
        $values = [];
        foreach ($this->send($method, $path, [], null)->getHeader($header) as $line) {
            foreach (explode(',', $line) as $value) {
                $value = trim($value);
                if ($value !== '') {
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * Sends a `multipart/form-data` request (for operations whose spec body is multipart only) and returns
     * the decoded JSON payload of a 2xx response.
     *
     * @param array<string, string> $fields form field name => contents
     *
     * @return array<array-key, mixed>
     *
     * @throws KongApiException
     */
    public function multipart(string $method, string $path, array $fields): array
    {
        $parts = [];
        foreach ($fields as $name => $contents) {
            $parts[] = ['name' => $name, 'contents' => $contents, 'filename' => $name];
        }
        $stream = new MultipartStream($parts);

        $request = $this->request($method, $path, [], null)
            ->withHeader('Content-Type', 'multipart/form-data; boundary=' . $stream->getBoundary())
            ->withBody($stream);

        return $this->decode($this->dispatch($request, $method, $path), $method, $path);
    }

    /**
     * @param array<string, string|int|bool|null> $query
     * @param array<string, mixed>|null           $body
     *
     * @throws KongApiException
     */
    private function send(string $method, string $path, array $query, ?array $body): ResponseInterface
    {
        return $this->dispatch($this->request($method, $path, $query, $body), $method, $path);
    }

    /**
     * Sends a built request and maps transport failures and error statuses to package exceptions.
     *
     * @throws KongApiException
     */
    private function dispatch(RequestInterface $request, string $method, string $path): ResponseInterface
    {
        try {
            $response = $this->client->sendRequest($request);
        } catch (RequestException $e) {
            // Guzzle clients or middleware configured with `http_errors` throw on 4xx/5xx.
            $response = $e->getResponse() ?? throw $this->transportFailure($method, $path, $e);
        } catch (ClientExceptionInterface $e) {
            throw $this->transportFailure($method, $path, $e);
        }

        $status = $response->getStatusCode();
        if ($status >= 400) {
            throw $this->httpError($status, $method, $path, (string) $response->getBody());
        }
        if ($status < 200 || $status >= 300) {
            throw new UnexpectedResponseException(
                sprintf('%s %s returned unexpected HTTP %d.', $method, $path, $status),
                $status,
                method: $method,
                path: $path,
            );
        }

        return $response;
    }

    /**
     * @param array<string, string|int|bool|null> $query
     * @param array<string, mixed>|null           $body
     *
     * @throws InvalidArgumentException when the body cannot be JSON-encoded
     */
    private function request(string $method, string $path, array $query, ?array $body): RequestInterface
    {
        $uri = rtrim($this->config->baseUri, '/') . $path;
        $queryString = $this->queryString($query);
        if ($queryString !== '') {
            $uri .= '?' . $queryString;
        }

        $request = $this->factory->createRequest($method, $uri);
        foreach ($this->config->headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        $request = $request->withHeader('Accept', self::CONTENT_TYPE_JSON);
        if ($this->config->adminToken !== null) {
            $request = $request->withHeader(self::HEADER_ADMIN_TOKEN, $this->config->adminToken);
        }

        if ($body === null) {
            return $request;
        }

        try {
            $json = $body === [] ? '{}' : json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(sprintf('Request body for %s %s is not JSON-encodable.', $method, $path), 0, $e);
        }

        return $request
            ->withHeader('Content-Type', self::CONTENT_TYPE_JSON)
            ->withBody($this->factory->createStream($json));
    }

    /**
     * @param array<string, string|int|bool|null> $query
     */
    private function queryString(array $query): string
    {
        $pairs = [];
        foreach ($query as $name => $value) {
            if ($value === null) {
                continue;
            }
            $pairs[$name] = match (true) {
                is_bool($value) => $value ? 'true' : 'false',
                default => (string) $value,
            };
        }

        return http_build_query($pairs, '', '&', PHP_QUERY_RFC3986);
    }

    private function transportFailure(string $method, string $path, Throwable $previous): TransportException
    {
        return new TransportException(
            sprintf('%s %s failed before a response was received: %s', $method, $path, $previous->getMessage()),
            method: $method,
            path: $path,
            previous: $previous,
        );
    }

    private function httpError(int $status, string $method, string $path, string $rawBody): KongApiException
    {
        $details = $this->errorDetails($rawBody);
        $kongMessage = isset($details['message']) && is_string($details['message']) ? $details['message'] : null;
        $message = sprintf('%s %s failed with HTTP %d', $method, $path, $status)
            . ($kongMessage !== null ? ': ' . $kongMessage : '.');

        return match (true) {
            $status === 400 => new ValidationException($message, $status, $kongMessage, $details, $method, $path),
            $status === 401 => new UnauthorizedException($message, $status, $kongMessage, $details, $method, $path),
            $status === 404 => new NotFoundException($message, $status, $kongMessage, $details, $method, $path),
            $status === 409 => new ConflictException($message, $status, $kongMessage, $details, $method, $path),
            $status >= 500 => new ServerException($message, $status, $kongMessage, $details, $method, $path),
            default => new KongApiException($message, $status, $kongMessage, $details, $method, $path),
        };
    }

    /**
     * Decodes an error body leniently; non-JSON or non-object bodies yield no details.
     *
     * @return array<string, mixed>
     */
    private function errorDetails(string $rawBody): array
    {
        if ($rawBody === '') {
            return [];
        }

        try {
            $decoded = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
        if (!is_array($decoded)) {
            return [];
        }

        $details = [];
        foreach ($decoded as $key => $value) {
            if (is_string($key)) {
                $details[$key] = $value;
            }
        }

        return $details;
    }
}

<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Tests\Support;

use ArrayObject;
use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\KongClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

/**
 * A KongClient wired to Guzzle's MockHandler plus history middleware. No network is ever used:
 * an exhausted queue fails the test.
 */
final class MockKong
{
    public const string BASE_URI = 'http://kong.test/';

    public readonly KongClient $client;

    /** @var ArrayObject<int, mixed> Guzzle history entries: {request, response, error, options} */
    private ArrayObject $history;

    private function __construct(ClientConfig $config, ResponseInterface|Throwable ...$queue)
    {
        $history = new ArrayObject();
        $stack = HandlerStack::create(new MockHandler(array_values($queue)));
        $stack->push(Middleware::history($history));
        if (!$history instanceof ArrayObject) {
            throw new RuntimeException('Guzzle replaced the history container');
        }

        $this->history = $history;
        $this->client = new KongClient($config, new Client(['handler' => $stack]));
    }

    public static function queue(ResponseInterface|Throwable ...$queue): self
    {
        return new self(new ClientConfig(self::BASE_URI), ...$queue);
    }

    public static function withConfig(ClientConfig $config, ResponseInterface|Throwable ...$queue): self
    {
        return new self($config, ...$queue);
    }

    /**
     * @param array<array-key, mixed> $body
     */
    public static function json(int $status, array $body): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($body, JSON_THROW_ON_ERROR));
    }

    public static function raw(int $status, string $body = ''): Response
    {
        return new Response($status, [], $body);
    }

    public function requestCount(): int
    {
        return count($this->history);
    }

    public function requestAt(int $index): RequestInterface
    {
        $entry = $this->history[$index] ?? null;
        $request = is_array($entry) ? ($entry['request'] ?? null) : null;

        return $request instanceof RequestInterface ? $request : throw new RuntimeException('No request #' . $index);
    }

    public function lastRequest(): RequestInterface
    {
        return $this->requestAt($this->requestCount() - 1);
    }

    /**
     * @return array<string, string>
     */
    public function queryAt(int $index): array
    {
        parse_str($this->requestAt($index)->getUri()->getQuery(), $query);

        $out = [];
        foreach ($query as $name => $value) {
            if (!is_string($value)) {
                throw new RuntimeException('Unexpected array query parameter ' . $name);
            }
            $out[(string) $name] = $value;
        }

        return $out;
    }

    /**
     * @return array<string, string>
     */
    public function lastQuery(): array
    {
        return $this->queryAt($this->requestCount() - 1);
    }

    /**
     * @return array<string, mixed>
     */
    public function lastJsonBody(): array
    {
        $decoded = json_decode((string) $this->lastRequest()->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new RuntimeException('Request body is not a JSON object');
        }

        return Spec::stringKeys($decoded, 'request body');
    }
}

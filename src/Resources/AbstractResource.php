<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * Shared plumbing for resource classes. Resources build paths and map payloads; all HTTP goes
 * through Transport.
 *
 * @internal
 */
abstract readonly class AbstractResource
{
    /**
     * @param list<string> $parent path segments of the parent entity for nested resources (unencoded)
     */
    public function __construct(
        protected Transport $transport,
        protected array $parent = [],
    ) {
    }

    /**
     * Encoded path below this resource's parent, with the `/{workspace}` prefix the scope requires.
     */
    protected function path(OperationScope $scope, string ...$segments): string
    {
        return $this->transport->path($scope, ...$this->parent, ...$segments);
    }

    /**
     * Sends a request expecting a single JSON object.
     *
     * @param array<string, string|int|bool|null>  $query
     * @param Input|array<string, mixed>|null      $body
     *
     * @return array<string, mixed>
     *
     * @throws KongApiException
     */
    protected function object(string $method, string $path, array $query = [], Input|array|null $body = null): array
    {
        return $this->asObject($this->transport->json($method, $path, $query, $this->body($body)), $method, $path);
    }

    /**
     * Ensures a decoded payload is a JSON object (an empty `{}` decodes to `[]` and is accepted).
     *
     * @param array<array-key, mixed> $payload
     *
     * @return array<string, mixed>
     *
     * @throws UnexpectedResponseException
     */
    protected function asObject(array $payload, string $method, string $path): array
    {
        if ($payload !== [] && array_is_list($payload)) {
            throw new UnexpectedResponseException(
                sprintf('%s %s returned a JSON array where an object was expected.', $method, $path),
                method: $method,
                path: $path,
            );
        }

        return Data::asMap($payload, $method . ' ' . $path);
    }

    /**
     * Sends a request expecting a bare JSON array of objects (no `data` envelope).
     *
     * @param array<string, string|int|bool|null> $query
     * @param Input|array<string, mixed>|null     $body
     *
     * @return list<array<string, mixed>>
     *
     * @throws KongApiException
     */
    protected function items(string $method, string $path, array $query = [], Input|array|null $body = null): array
    {
        $payload = $this->transport->json($method, $path, $query, $this->body($body));
        if (!array_is_list($payload)) {
            throw new UnexpectedResponseException(
                sprintf('%s %s returned a JSON object where an array was expected.', $method, $path),
                method: $method,
                path: $path,
            );
        }

        return Data::listOfMaps(['items' => $payload], 'items');
    }

    /**
     * Guards a required, non-path argument (e.g. a query value) against the empty string.
     *
     * @throws InvalidArgumentException
     */
    protected function required(string $value, string $label): string
    {
        if ($value === '') {
            throw new InvalidArgumentException($label . ' must not be empty.');
        }

        return $value;
    }

    /**
     * Sends a request whose response body is not used (e.g. 204 No Content).
     *
     * @param array<string, string|int|bool|null> $query
     * @param Input|array<string, mixed>|null     $body
     *
     * @throws KongApiException
     */
    protected function none(string $method, string $path, array $query = [], Input|array|null $body = null): void
    {
        $this->transport->none($method, $path, $query, $this->body($body));
    }

    /**
     * Fetches one page of a paginated list.
     *
     * @template T
     *
     * @param callable(array<string, mixed>): T      $map
     * @param array<string, string|int|bool|null>    $query extra, operation-specific query parameters
     *
     * @return Page<T>
     *
     * @throws KongApiException
     */
    protected function page(string $path, callable $map, ?ListOptions $options = null, array $query = []): Page
    {
        $payload = $this->object(Transport::METHOD_GET, $path, [...($options?->toQuery() ?? []), ...$query]);

        return Page::fromArray($payload, $map);
    }

    /**
     * Lazily walks every page, following `offset` until Kong stops returning one.
     *
     * @template T
     *
     * @param callable(array<string, mixed>): T      $map
     * @param array<string, string|int|bool|null>    $query extra, operation-specific query parameters
     *
     * @return Generator<int, T>
     *
     * @throws KongApiException
     */
    protected function walk(string $path, callable $map, ?ListOptions $options = null, array $query = []): Generator
    {
        $options ??= new ListOptions();
        $seen = [];
        $index = 0;

        do {
            $page = $this->page($path, $map, $options, $query);
            foreach ($page->data as $item) {
                yield $index++ => $item;
            }

            $offset = $page->offset;
            if ($offset !== null) {
                if (isset($seen[$offset])) {
                    throw new UnexpectedResponseException(
                        sprintf('GET %s returned offset "%s" twice; stopping to avoid an endless loop.', $path, $offset),
                        method: Transport::METHOD_GET,
                        path: $path,
                    );
                }
                $seen[$offset] = true;
                $options = $options->withOffset($offset);
            }
        } while ($offset !== null);
    }

    /**
     * @param Input|array<string, mixed>|null $body
     *
     * @return array<string, mixed>|null
     */
    private function body(Input|array|null $body): ?array
    {
        return $body instanceof Input ? $body->toArray() : $body;
    }
}

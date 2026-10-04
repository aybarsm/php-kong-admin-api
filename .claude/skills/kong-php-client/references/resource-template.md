# Resource template

**The reference implementation is `src/Resources/Services.php`. Copy it**, then adapt the segment,
the DTOs, the operationIds and the scope. A condensed version follows.

```php
<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Service;
use Aybarsm\Kong\AdminApi\Models\ServiceInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * Services (spec tag "Services"): `/services` and `/services/{ServiceIdOrName}`.
 */
final readonly class Services extends AbstractResource
{
    private const string SEGMENT = 'services';

    /**
     * List one page of Services (operationId `list-service`).
     *
     * @return Page<Service>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services', 'list-service', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Service::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Service across all pages (operationId `list-service`).
     *
     * @return Generator<int, Service>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services', 'list-service', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Service::fromArray(...), $options);
    }

    /**
     * Get a Service by ID or name (operationId `get-service`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}', 'get-service', OperationScope::Both)]
    public function get(string $idOrName): Service
    {
        return Service::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::Both, self::SEGMENT, $idOrName)));
    }

    /**
     * Create a Service (operationId `create-service`).
     *
     * @param ServiceInput|array<string, mixed> $service
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/services', 'create-service', OperationScope::Both)]
    public function create(ServiceInput|array $service): Service
    {
        return Service::fromArray($this->object(Transport::METHOD_POST, $this->path(OperationScope::Both, self::SEGMENT), body: $service));
    }

    // update(): PATCH, upsert(): PUT, delete(): DELETE → $this->none(...). See Services.php.
}
```

## AbstractResource helpers (`src/Resources/AbstractResource.php`)
| Helper | Use |
|---|---|
| `path(OperationScope $scope, string ...$segments): string` | Encodes the segments, prepends the parent segments (nested resources) and the `/{workspace}` prefix the scope requires. Throws `InvalidArgumentException` on an empty segment. |
| `object($method, $path, $query = [], $body = null): array<string, mixed>` | Sends a request that returns a JSON object. Map the result with `X::fromArray()`. |
| `none($method, $path, $query = [], $body = null): void` | For 204s or responses without a schema. |
| `page($path, X::fromArray(...), ?ListOptions, $extraQuery = []): Page<X>` | One page of a paginated list. |
| `walk(...)` (same arguments as `page`): `Generator<int, X>` | Lazy walk over every page. |

The `$body` argument accepts an `Input` DTO or a raw `array<string, mixed>`. Extra query values
(`string|int|bool|null`) are sent only when non-null; booleans are sent as `true`/`false`.

## Rules
- One `#[Operation]` per spec path served. Use the spec path template **verbatim** and its real
  operationId (OperationConformanceTest checks both, plus the scope).
- The scope is `Both` when the `/{workspace}` twin exists, `GlobalOnly` when it doesn't, and
  `WorkspaceOnly` for `/{workspace}`-only paths. A `WorkspaceOnly` attribute's path includes the
  `/{workspace}` prefix.
- Only implement methods the spec defines. For example, Event-hooks has no `get()` or `update()`.
- Nested resources live in `Resources\Nested\`. They are built by an accessor on the parent resource,
  `new ServiceRoutes($this->transport, [...$this->parent, 'services', $serviceIdOrName])`, and the
  accessor needs no `#[Operation]`.
- Add the top-level accessor to `KongClient` with a one-line docblock naming the path.

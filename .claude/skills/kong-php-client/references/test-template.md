# Pest test template (no network, ever)

**The reference is `tests/Feature/Resources/ServicesTest.php`.** Copy its structure. Every resource
method needs tests asserting the **HTTP method, path (global and, for `Both`, workspace-scoped),
query string and JSON body**, plus the mapped DTO.

```php
<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Models\Service;
use Aybarsm\Kong\AdminApi\Models\ServiceInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Services::class, Service::class, ServiceInput::class);

it('lists one page with size, offset and a tag filter', function (): void {
    $kong = MockKong::queue(MockKong::json(200, [
        'data' => [Fixture::get('service')],
        'offset' => 'b2Zm',
        'next' => '/services?offset=b2Zm',
    ]));

    $page = $kong->client->services()->list(new ListOptions(size: 10, tags: TagFilter::allOf('a', 'b')));

    expect($kong->lastRequest()->getMethod())->toBe('GET')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services')
        ->and($kong->lastQuery())->toBe(['size' => '10', 'tags' => 'a,b'])
        ->and($page->data[0])->toBeInstanceOf(Service::class)
        ->and($page->offset)->toBe('b2Zm');
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $kong->client->inWorkspace('team a')->services()->get('my svc');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team%20a/services/my%20svc');
});

it('creates from an input DTO without sending nulls', function (): void {
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('service')));

    $kong->client->services()->create(new ServiceInput(host: 'example.internal'));

    expect($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastJsonBody())->toBe(['host' => 'example.internal']);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Service => $kong->client->services()->get('missing'))->toThrow(NotFoundException::class);
});
```

## Support classes (typed; no `$this` in tests, so PHPStan level 9 stays clean)
- `MockKong::queue(ResponseInterface|Throwable ...$queue)` and `MockKong::withConfig(ClientConfig, ...$queue)`
  build a `KongClient` over Guzzle `MockHandler` plus history middleware. An exhausted queue fails the test.
- `MockKong::json(int $status, array $body)` and `MockKong::raw(int $status, string $body = '')` build responses.
- `$kong->lastRequest()`, `requestAt($i)`, `lastQuery()`, `queryAt($i)`, `lastJsonBody()` and
  `requestCount()` read back what was sent.
- `Fixture::get('service')` loads `tests/Fixtures/service.json`. `Fixture::specExample('Service')`
  loads the spec's own `example`.
- `Spec::operations()`, `Spec::schema($name)` and `Spec::document()` read the canonical spec (used by
  the conformance tests).

## Rules
- Fixtures are shaped after the spec schema, with every property present and enum values taken
  from the spec. Add a round-trip test (`fromArray(fixture)->toArray() == fixture`).
- Use datasets (`->with([...])`) for status codes, enum cases and boundaries.
- Exception tests assert the class **and** the message or status.
- Avoid global helper functions with generic names. Pest files share the global namespace.
- PHPStan analyses tests at level 9: type closures (`fn (): Service => …`) and make `match`
  expressions exhaustive.

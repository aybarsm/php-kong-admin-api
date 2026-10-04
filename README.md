# Kong Admin API client for PHP

[![CI](https://github.com/aybarsm/php-kong-admin-api/actions/workflows/ci.yml/badge.svg)](https://github.com/aybarsm/php-kong-admin-api/actions/workflows/ci.yml)
[![Mutation](https://github.com/aybarsm/php-kong-admin-api/actions/workflows/mutation.yml/badge.svg)](https://github.com/aybarsm/php-kong-admin-api/actions/workflows/mutation.yml)

A framework-agnostic, strictly typed PHP client for the [Kong Gateway](https://github.com/Kong/kong) Admin API.

- **Generated from the spec.** Every endpoint and model comes from Kong's own OpenAPI spec (Kong Gateway **3.16.0**). Tests check every public method and every model against that spec.
- **Typed throughout.** Responses come back as `readonly` DTOs and enums, never raw arrays or PSR-7 responses. Write methods accept a typed input DTO or a plain array.
- **Bring your own HTTP client.** It works with any PSR-18 client and PSR-17 factories, with Guzzle as the default.
- **Complete.** It covers all 657 operations in the spec, including Enterprise features (RBAC, workspaces, partials, keyring, licenses, …). The [exceptions](#spec-coverage) are listed below.

Requires PHP 8.3 or newer.

## Contents

- [Installation](#installation)
- [Quick start](#quick-start)
- [Configuration](#configuration)
- [Workspaces](#workspaces)
- [Resources](#resources)
- [Pagination and tag filters](#pagination-and-tag-filters)
- [Input DTOs and arrays](#input-dtos-and-arrays)
- [Consumers and credentials](#consumers-and-credentials)
- [Polymorphic entities](#polymorphic-entities)
- [Typed plugins](#typed-plugins)
- [Error handling](#error-handling)
- [Operational endpoints](#operational-endpoints)
- [Spec coverage](#spec-coverage)
- [Kong version support](#kong-version-support)
- [Development](#development)
- [License](#license)

## Installation

```bash
composer require aybarsm/kong-admin-api
```

## Quick start

```php
use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\PluginInput;
use Aybarsm\Kong\AdminApi\Models\RouteJsonInput;
use Aybarsm\Kong\AdminApi\Models\ServiceInput;

$kong = new KongClient(new ClientConfig('http://localhost:8001/'));

$service = $kong->services()->create(new ServiceInput(
    name: 'billing',
    url: 'http://billing.internal:8080',
));

$route = $kong->services()->routes('billing')->create(new RouteJsonInput(
    name: 'billing-api',
    paths: ['/billing'],
    protocols: [Protocol::Https],
));

$kong->services()->plugins('billing')->create(new PluginInput(
    name: 'rate-limiting',
    config: ['minute' => 100, 'policy' => 'local'],
));
```

`KongClient` only hands out resources. It never calls the API itself, and every resource method maps to exactly one spec operation.

## Configuration

```php
$token = getenv('KONG_ADMIN_TOKEN');

$kong = new KongClient(new ClientConfig(
    baseUri: 'https://kong-admin.internal:8444/',
    adminToken: $token === false ? null : $token,
    workspace: null,
    timeout: 10.0,
    connectTimeout: 2.0,
    headers: ['X-Request-Source' => 'deploy-bot'],
));
```

| Option | Default | Notes |
|---|---|---|
| `baseUri` | `http://localhost:8001/` | Absolute `http`/`https` URI. A base path such as `https://host/kong/` is kept. |
| `adminToken` | `null` | Sent as the `Kong-Admin-Token` header. Never included in exception messages or debug output. |
| `workspace` | `null` | See [Workspaces](#workspaces). |
| `timeout`, `connectTimeout` | `null` | Seconds. These apply to the default Guzzle client only. |
| `headers` | `[]` | Extra headers sent with every request. |

### Bring your own PSR-18 client

```php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

$http = new Client([
    'handler' => $handler,   // e.g. HandlerStack::create() with your own middleware
    'timeout' => 5,
    'verify' => '/etc/ssl/kong-ca.pem',
]);

$kong = new KongClient(
    new ClientConfig('https://kong-admin.internal:8444/'),
    $http,              // any Psr\Http\Client\ClientInterface
    new HttpFactory(),  // any PSR-17 request + stream factory
);
```

When you inject a client, configure its timeouts, TLS and retries on that client yourself.

## Workspaces

```php
$payments = $kong->inWorkspace('team-payments');
$payments->services()->list();      // GET /team-payments/services
$payments->workspaces()->list();    // GET /workspaces (global-only path, never prefixed)
```

`inWorkspace()` returns a new client and leaves the original unchanged; `withoutWorkspace()` removes the prefix again. The prefix is added only to operations that the spec defines under `/{workspace}`; global-only paths such as `/workspaces`, `/admins` or `/licenses` are never prefixed.

RBAC users and roles exist twice in the spec, so the client exposes both families:

- `rbacUsers()` and `rbacRoles()` address the global `/rbac_users` and `/rbac_roles` paths.
- `workspaceRbacUsers()` and `workspaceRbacRoles()` address `/{workspace}/rbac/users` and `/{workspace}/rbac/roles`. These exist only under a workspace; without one, the spec default `default` is used.

`workspaceGroups()` is a separate Enterprise resource. It is not RBAC Groups (`groups()`) and not Consumer Groups:

> Enterprise-only. Paths are taken verbatim from the Gateway Admin EE 3.16 spec, including the literal prefix `/workspace_/groups` (not `/workspaces/{workspace}/groups` and not `/groups`). The rendered spec and its curl examples use that string. It is not in Kong open-source and has not been verified against a running Kong Enterprise node. If a future spec revision changes the path, follow the spec; do not keep a local rewrite.

## Resources

Every Kong entity is a resource on `KongClient`. Most have `list()`, `all()`, `get()`, `create()`, `update()` (PATCH), `upsert()` (PUT) and `delete()`. A resource only has the methods its spec paths define. Lookups take whatever the spec allows: an ID, an ID or name, an ID or username, and so on.

| Area | Accessors |
|---|---|
| Core gateway | `services()`, `routes()`, `consumers()`, `plugins()`, `upstreams()`, `certificates()`, `snis()`, `caCertificates()`, `vaults()`, `keys()`, `keySets()`, `workspaces()`, `tags()` |
| Credentials | `acls()`, `keyAuths()`, `basicAuths()`, `hmacAuths()`, `jwts()`, `mtlsAuths()` |
| Consumer groups | `consumerGroups()` |
| Enterprise | `admins()`, `groups()`, `groupRbacRoles()`, `rbacUsers()`, `rbacRoles()`, `rbacRoleEndpoints()`, `rbacRoleEntities()`, `rbacUserGroups()`, `rbacUserRoles()`, `workspaceRbacUsers()`, `workspaceRbacRoles()`, `workspaceGroups()`, `licenses()`, `eventHooks()`, `partials()`, `clonedPlugins()`, `customPlugins()`, `degraphqlRoutes()`, `graphqlCostDecorations()`, `oidcJwks()` |
| Operations | `information()`, `debug()`, `clustering()`, `declarativeConfig()`, `cache()`, `keyring()`, `auditLogs()`, `schemas()` |

Nested endpoints follow the spec paths:

```php
$resources = [
    $kong->services()->routes('billing'),              // /services/{ServiceIdOrName}/routes
    $kong->services()->plugins('billing'),             // /services/{ServiceIdOrName}/plugins
    $kong->routes()->plugins('billing-api'),           // /routes/{RouteIdOrName}/plugins
    $kong->consumers()->plugins($consumerId),          // /consumers/{ConsumerIdForNestedEntities}/plugins
    $kong->consumerGroups()->consumers('gold'),        // /consumer_groups/{ConsumerGroupId}/consumers
    $kong->upstreams()->targets($upstreamId),          // /upstreams/{UpstreamIdForTarget}/targets
    $kong->certificates()->snis($certificateId),       // /certificates/{CertificateId}/snis
    $kong->keySets()->keys('jwks'),                    // /key-sets/{KeySetIdOrName}/keys
    $kong->partials()->links($partialId),              // /partials/{PartialId}/links
];
```

## Pagination and tag filters

```php
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;

$options = new ListOptions(size: 100, tags: TagFilter::allOf('production', 'billing'));

$page = $kong->services()->list($options);
foreach ($page->data as $service) {
    $seen[] = $service->name;
}
$more = $page->hasMore();                               // true when Kong returned an `offset`
$next = $kong->services()->nextPage($page, $options);   // same filters; null after the last page

foreach ($kong->services()->all(new ListOptions(tags: TagFilter::anyOf('team-a', 'team-b'))) as $service) {
    $seen[] = $service->name;   // fetched page by page, lazily
}
```

- `list()` returns one `Page`, with `data`, `offset` and `next`.
- `nextPage($page, $options)` repeats the request with the page's `offset` and returns `null` after the last page. Kong's `next` link is exposed but not followed, because it drops filters on nested lists.
- `all()` returns a lazy generator. It follows `offset` until Kong stops returning one, and stops with an exception if Kong repeats an offset.
- `size` must be between 1 and 1000, the spec's bounds.
- `TagFilter::allOf()` joins tags with `,` to mean AND; `TagFilter::anyOf()` joins them with `/` to mean OR.

## Input DTOs and arrays

```php
// Typed input: null fields are not sent.
$kong->services()->update('billing', new ServiceInput(retries: 3));          // {"retries":3}

// Arrays are sent as given, including explicit nulls.
$kong->services()->update('billing', ['ca_certificates' => null]);          // {"ca_certificates":null}
```

- **Every write method accepts either form.** Input DTOs make every field optional, because PATCH reuses the same schema. Fields the spec requires on create are marked in each DTO's docblock.
- **Response DTOs round-trip.** Each one has `fromArray()` and `toArray()`, using the spec's snake_case property names.
- **Secrets stay hidden.** Sensitive properties are redacted from `var_dump()`/`print_r()`; see [Sensitive fields](#sensitive-fields).

## Consumers and credentials

```php
$consumer = $kong->consumers()->create(new ConsumerInput(username: 'alice', customId: 'crm-42'));
$kong->consumers()->keyAuths((string) $consumer->id)->create(new KeyAuthInput(ttl: 3600));
```

Every credential type is available both top-level (`$kong->keyAuths()`) and per consumer (`$kong->consumers()->keyAuths($consumerId)`). Group memberships are managed from both sides:

- `$kong->consumerGroups()->consumers($group)` covers the consumers in a group.
- `$kong->consumers()->consumerGroups($consumer)` covers the groups a consumer belongs to.

## Polymorphic entities

Some spec schemas are a `oneOf`. Responses are mapped to the matching class:

```php
$route = $kong->routes()->get('billing-api');

$match = match (true) {
    $route instanceof RouteExpression => (string) $route->expression,
    $route instanceof RouteJson => implode(', ', $route->paths ?? []),
    default => '',
};
```

- **Routes:** a payload with a non-null `expression` becomes a `RouteExpression`; any other payload becomes a `RouteJson`.
- **Partials:** `PartialFactory` chooses the variant from the `type` discriminator (`PartialRedisCe`, `PartialRedisEe`, `PartialVectordb`, `PartialEmbeddings`, `PartialModel`). Each variant's input DTO sends its own `type` by default.

## Typed plugins

Plugins that have a configuration doc in `resources/kong-admin-api/plugins/` get typed classes under `Aybarsm\Kong\AdminApi\Plugins\{Category}\{Plugin}`. Each plugin has a request body that sends its own `name`, a typed `config` for requests and responses, and the config's nested objects and enums.

```php
use Aybarsm\Kong\AdminApi\Plugins\PluginRegistry;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\Policy;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingConfig;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingConfigInput;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingInput;

$plugin = $kong->services()->plugins('billing')->create(new RateLimitingInput(
    config: new RateLimitingConfigInput(minute: 100, policy: Policy::Local),
    tags: ['edge'],
));

$limits = RateLimitingConfig::fromPlugin($plugin);   // typed config of a rate-limiting plugin
$minute = $limits->minute;

// Typed configs of any listed plugins; null for a plugin without a doc.
$configs = array_map(PluginRegistry::config(...), $kong->plugins()->list()->data);
```

- **Bodies:** `{Plugin}Input` works with `create()`, `update()` and `upsert()` of `plugins()` and of the plugin resources nested under Services, Routes, Consumers and Consumer Groups. Its constructor offers only the scopes the plugin's doc allows; ACME, for example, has no `service` or `route`.
- **Responses:** responses stay the generic `Plugin`. `{Plugin}Config::fromPlugin()` reads the typed config and throws `InvalidArgumentException` for another plugin. `PluginRegistry::config()` works on any plugin.
- **Inputs:** like other input DTOs, config inputs omit nulls. Pass `config` as an array to send explicit nulls, or a `{vault://…}` reference for a non-string field.
- **Docs and gaps:** each class's docblock names its doc, defaults, supported Partials and minimum Kong version. Plugins without a doc use the generic `PluginInput` with an array `config`. [`docs/plugin-notes.md`](docs/plugin-notes.md) records the decisions on doc quirks.
- **Types and names:** a doc `number` is `int|float`, so integers stay integers. Nested classes and enums are named after their config path within the plugin's namespace, for example `RateLimiting\RedisCloudAuthentication` and `RateLimiting\Policy`.

<!-- plugins:start -->
| Category | Plugin (`name`) | Namespace `Aybarsm\Kong\AdminApi\Plugins\…` |
|---|---|---|
| AI | AI Prompt Decorator (`ai-prompt-decorator`) | `AI\AiPromptDecorator\AiPromptDecoratorInput`, `…Config`, `…ConfigInput` |
| AI | AI Prompt Guard (`ai-prompt-guard`) | `AI\AiPromptGuard\AiPromptGuardInput`, `…Config`, `…ConfigInput` |
| AI | AI Prompt Template (`ai-prompt-template`) | `AI\AiPromptTemplate\AiPromptTemplateInput`, `…Config`, `…ConfigInput` |
| AI | AI Proxy (`ai-proxy`) | `AI\AiProxy\AiProxyInput`, `…Config`, `…ConfigInput` |
| AI | AI Request Transformer (`ai-request-transformer`) | `AI\AiRequestTransformer\AiRequestTransformerInput`, `…Config`, `…ConfigInput` |
| AI | AI Response Transformer (`ai-response-transformer`) | `AI\AiResponseTransformer\AiResponseTransformerInput`, `…Config`, `…ConfigInput` |
| Authentication | Basic Auth (`basic-auth`) | `Authentication\BasicAuth\BasicAuthInput`, `…Config`, `…ConfigInput` |
| Authentication | HMAC Auth (`hmac-auth`) | `Authentication\HmacAuth\HmacAuthInput`, `…Config`, `…ConfigInput` |
| Authentication | JWT (`jwt`) | `Authentication\Jwt\JwtInput`, `…Config`, `…ConfigInput` |
| Authentication | Key Auth (`key-auth`) | `Authentication\KeyAuth\KeyAuthInput`, `…Config`, `…ConfigInput` |
| Authentication | LDAP Authentication (`ldap-auth`) | `Authentication\LdapAuth\LdapAuthInput`, `…Config`, `…ConfigInput` |
| Authentication | OAuth 2.0 Authentication (`oauth2`) | `Authentication\Oauth2\Oauth2Input`, `…Config`, `…ConfigInput` |
| Authentication | Session (`session`) | `Authentication\Session\SessionInput`, `…Config`, `…ConfigInput` |
| Logging | File Log (`file-log`) | `Logging\FileLog\FileLogInput`, `…Config`, `…ConfigInput` |
| Logging | HTTP Log (`http-log`) | `Logging\HttpLog\HttpLogInput`, `…Config`, `…ConfigInput` |
| Logging | Loggly (`loggly`) | `Logging\Loggly\LogglyInput`, `…Config`, `…ConfigInput` |
| Logging | Syslog (`syslog`) | `Logging\Syslog\SyslogInput`, `…Config`, `…ConfigInput` |
| Logging | TCP Log (`tcp-log`) | `Logging\TcpLog\TcpLogInput`, `…Config`, `…ConfigInput` |
| Logging | UDP Log (`udp-log`) | `Logging\UdpLog\UdpLogInput`, `…Config`, `…ConfigInput` |
| Monitoring | Datadog (`datadog`) | `Monitoring\Datadog\DatadogInput`, `…Config`, `…ConfigInput` |
| Monitoring | OpenTelemetry (`opentelemetry`) | `Monitoring\Opentelemetry\OpentelemetryInput`, `…Config`, `…ConfigInput` |
| Monitoring | Prometheus (`prometheus`) | `Monitoring\Prometheus\PrometheusInput`, `…Config`, `…ConfigInput` |
| Monitoring | StatsD (`statsd`) | `Monitoring\Statsd\StatsdInput`, `…Config`, `…ConfigInput` |
| Monitoring | Zipkin (`zipkin`) | `Monitoring\Zipkin\ZipkinInput`, `…Config`, `…ConfigInput` |
| Security | ACME (`acme`) | `Security\Acme\AcmeInput`, `…Config`, `…ConfigInput` |
| Security | Bot Detection (`bot-detection`) | `Security\BotDetection\BotDetectionInput`, `…Config`, `…ConfigInput` |
| Security | CORS (`cors`) | `Security\Cors\CorsInput`, `…Config`, `…ConfigInput` |
| Security | IP Restriction (`ip-restriction`) | `Security\IpRestriction\IpRestrictionInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Access Control Enforcement (`ace`) | `TrafficControl\AccessControlEnforcement\AccessControlEnforcementInput`, `…Config`, `…ConfigInput` |
| TrafficControl | ACL (`acl`) | `TrafficControl\Acl\AclInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Proxy Cache (`proxy-cache`) | `TrafficControl\ProxyCache\ProxyCacheInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Rate Limiting (`rate-limiting`) | `TrafficControl\RateLimiting\RateLimitingInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Redirect (`redirect`) | `TrafficControl\Redirect\RedirectInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Request Size Limiting (`request-size-limiting`) | `TrafficControl\RequestSizeLimiting\RequestSizeLimitingInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Request Termination (`request-termination`) | `TrafficControl\RequestTermination\RequestTerminationInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Response Rate Limiting (`response-ratelimiting`) | `TrafficControl\ResponseRateLimiting\ResponseRateLimitingInput`, `…Config`, `…ConfigInput` |
| TrafficControl | Standard Webhooks (`standard-webhooks`) | `TrafficControl\StandardWebhooks\StandardWebhooksInput`, `…Config`, `…ConfigInput` |
| Transformation | Correlation ID (`correlation-id`) | `Transformation\CorrelationId\CorrelationIdInput`, `…Config`, `…ConfigInput` |
| Transformation | gRPC-Gateway (`grpc-gateway`) | `Transformation\GrpcGateway\GrpcGatewayInput`, `…Config`, `…ConfigInput` |
| Transformation | gRPC-Web (`grpc-web`) | `Transformation\GrpcWeb\GrpcWebInput`, `…Config`, `…ConfigInput` |
| Transformation | Request Transformer (`request-transformer`) | `Transformation\RequestTransformer\RequestTransformerInput`, `…Config`, `…ConfigInput` |
| Transformation | Response Transformer (`response-transformer`) | `Transformation\ResponseTransformer\ResponseTransformerInput`, `…Config`, `…ConfigInput` |
<!-- plugins:end -->

## Error handling

```php
try {
    $kong->services()->get('missing');
} catch (NotFoundException $e) {
    $log[] = $e->statusCode;                    // 404
}

try {
    $kong->services()->create(['name' => 'no-host']);
} catch (ValidationException $e) {
    $log[] = $e->kongMessage;                   // "schema violation (host: required field missing)"
    $log[] = $e->errorName;                     // "schema violation" (Kong error code 2)
    $log[] = $e->errorFields;                   // ['host' => 'required field missing']
}

try {
    $kong->services()->list();
} catch (TransportException $e) {
    $log[] = $e->getPrevious()?->getMessage();  // the PSR-18 client's exception
} catch (KongApiException $e) {
    // any other Admin API failure
}
```

| Exception | When |
|---|---|
| `ValidationException` | HTTP 400 |
| `UnauthorizedException` | HTTP 401 |
| `ForbiddenException` | HTTP 403, e.g. an RBAC permission failure (not defined by the spec; standard HTTP semantics) |
| `NotFoundException` | HTTP 404. Deletes return 204 even when the entity doesn't exist, as the spec defines. |
| `ConflictException` | HTTP 409 |
| `ServerException` | HTTP 5xx |
| `KongApiException` | Any other HTTP error (e.g. 405), and the base class of all of the above |
| `TransportException` | No response at all: network, DNS or TLS failures, wrapping the PSR-18 exception |
| `UnexpectedResponseException` | A response that isn't valid JSON or doesn't match the spec schema |
| `InvalidArgumentException` | Caller errors caught before sending (empty ID, page size out of range, invalid base URI) |

- **Common interface:** every exception implements `KongExceptionInterface`.
- **HTTP errors:** `KongApiException` exposes `statusCode`, `kongMessage` (the spec's `message` field), `details` (the decoded error body), `method` and `path`.
- **Kong error table:** for database and validation errors, Kong returns `{code, name, message, fields}`. These are exposed as `errorCode` (e.g. `2` schema violation, `5` unique constraint violation), `errorName` and `errorFields` (per-field messages, possibly nested). They are `null`/empty when Kong sends only a `message`. The spec defines just `{message, status}`; the rest follows Kong's core error handling.
- **Guzzle compatibility:** Guzzle's `RequestException`s are mapped by their response status too, so a client configured with `http_errors` behaves the same.

## Operational endpoints

```php
$version = $kong->information()->info()->version;
$hasVaults = $kong->information()->endpointExists('vaults');
$check = $kong->schemas()->validate('services', ['host' => 'billing.internal']);
```

- `information()` covers `/`, `/status`, `/status/dns`, `/endpoints`, `/timers` and `/fips-status`, plus `HEAD`/`OPTIONS /{endpoint}`.
- `debug()` gets and sets log levels.
- `clustering()` covers hybrid-mode data planes.
- `declarativeConfig()` reads `/config` and applies a configuration with `apply(array)` (JSON) or `applyYaml(string)` (`application/yaml`, e.g. the contents of `kong.yaml`).
- `cache()`, `keyring()`, `auditLogs()` and `schemas()` complete the operational set.

## Spec coverage

The client implements all **657** operations in `resources/kong-admin-api/v3.16.json` except four. Those four live under `/{workspace}/rbac/roles/{id}/endpoints/{workspace}{RBACRoleEndpointId}`, a path template the spec gets wrong. Use `rbacRoleEndpoints()` for those items instead.

Where the spec is ambiguous or inconsistent, the client follows the spec literally. Each decision is recorded in [`docs/spec-notes.md`](docs/spec-notes.md).

### Spec quirks

These are implemented exactly as the 3.16 spec defines them. Some may look surprising:

- **Licenses:** `licenses()->list()` returns a **single** `License`; the spec defines no list envelope for `GET /licenses`.
- **Event hooks:** `create()`, `ping()` and `test()` return a `Page` of event hooks (the spec's list envelope), like `list()`.
- **Admin workspaces:** `admins()->workspaces($admin)` returns a **single** `Workspace`.
- **Tags, Admins and Event-hooks lists:** `list()` takes no options and has no `all()`, because the spec declares no `size`/`offset`/`tags` parameters for them.
- **Rate-limiting override:** `consumerGroups()->rateLimitingAdvancedOverride($group)->upsert()` sends the spec's literal dotted keys (`config.limit`, `config.window_size`, …). Pass an array to send another shape.
- **Workspace groups:** `workspaceGroups()` uses the literal `/workspace_/groups` path (see [Workspaces](#workspaces)).
- **Timestamps:** `Target` timestamps are floats; the spec types them as `number`.
- **Schemaless responses:** operations without a response schema return `void`, except `admins()->roles()`, which returns the decoded JSON as an array.
- **Free-form fields:** Partial `config` and event-hook sources `data` are plain arrays.

### Sensitive fields

Properties the spec or a plugin doc marks `x-encrypted` are redacted from `var_dump()`/`print_r()`. So are a reviewed set of other secrets the spec doesn't mark: key-auth keys, JWT secrets, RBAC user tokens, admin passwords and tokens, keyring material, webhook secrets and the license key. Their input DTO parameters are also `#[SensitiveParameter]`.

## Kong version support

`KongSpec::VERSION` (`3.16.0`) records the Kong Gateway version this release was built from. The client only implements what that spec defines. Upgrading to a newer Kong spec is a deliberate, reviewed change: the new spec is diffed against the current one, breaking changes are reported, and the version constant is bumped. The plugin docs carry no Kong version. They are treated as matching `KongSpec::VERSION` and are refreshed with each upgrade.

## Development

```bash
composer install
composer ci            # code style, PHPStan level 9 + 100% type coverage, tests with ≥ 90% line coverage
composer test:mutate   # mutation testing (≥ 80%)
```

- **Tests:**
  - Pest, using Guzzle's `MockHandler`; no network access.
  - Conformance tests check every resource method, DTO and enum against the spec.
  - Every example in this README is mirrored in `tests/Feature/ReadmeExamplesTest.php`.
- **Generated code:** the uniform CRUD resources and most DTOs are generated from the spec by `tools/generator/` and committed; see its README. Every plugin class is generated from its doc by `tools/generator/plugins.py`.
- **Continuous integration:**
  - CI runs PHP 8.3, 8.4 and 8.5 with lowest and highest dependencies.
  - Mutation testing runs on pushes to `main` and weekly.

## License

MIT. See [LICENSE](LICENSE).

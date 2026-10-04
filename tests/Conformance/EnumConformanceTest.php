<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\DeploymentType;
use Aybarsm\Kong\AdminApi\Enums\HealthcheckType;
use Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode;
use Aybarsm\Kong\AdminApi\Enums\JwtAlgorithm;
use Aybarsm\Kong\AdminApi\Enums\LogLevel;
use Aybarsm\Kong\AdminApi\Enums\PartialType;
use Aybarsm\Kong\AdminApi\Enums\PathHandling;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Enums\RbacRoleSource;
use Aybarsm\Kong\AdminApi\Enums\UpstreamAlgorithm;
use Aybarsm\Kong\AdminApi\Enums\UpstreamHashOn;
use Aybarsm\Kong\AdminApi\Tests\Support\Spec;

/**
 * Walks the spec document along a key path.
 *
 * @param list<string> $path
 *
 * @return array<string, mixed>
 */
function specNode(array $path): array
{
    $node = Spec::document();
    foreach ($path as $key) {
        $next = array_is_list($node) ? ($node[(int) $key] ?? null) : ($node[$key] ?? null);
        if (!is_array($next)) {
            throw new RuntimeException('Spec node not found: ' . implode('.', $path));
        }
        $node = $next;
    }

    return Spec::stringKeys($node, implode('.', $path));
}

it('matches the spec enum values', function (string $enum, array $path): void {
    /** @var class-string<BackedEnum> $enum */
    /** @var list<string> $path */
    $values = array_map(static fn (BackedEnum $case): int|string => $case->value, $enum::cases());
    $specValues = specNode($path)['enum'] ?? null;

    expect($specValues)->toBeArray()
        ->and($values)->toEqualCanonicalizing($specValues);
})->with([
    'JwtAlgorithm @ components.schemas.JWT.algorithm' => [JwtAlgorithm::class, ['components', 'schemas', 'JWT', 'properties', 'algorithm']],
    'JwtAlgorithm @ components.schemas.JWTWithoutParents.algorithm' => [JwtAlgorithm::class, ['components', 'schemas', 'JWTWithoutParents', 'properties', 'algorithm']],
    'UpstreamAlgorithm @ components.schemas.Upstream.algorithm' => [UpstreamAlgorithm::class, ['components', 'schemas', 'Upstream', 'properties', 'algorithm']],
    'UpstreamHashOn @ components.schemas.Upstream.hash_on' => [UpstreamHashOn::class, ['components', 'schemas', 'Upstream', 'properties', 'hash_on']],
    'UpstreamHashOn @ components.schemas.Upstream.hash_fallback' => [UpstreamHashOn::class, ['components', 'schemas', 'Upstream', 'properties', 'hash_fallback']],
    'HealthcheckType @ components.schemas.Upstream.healthchecks.active.type' => [HealthcheckType::class, ['components', 'schemas', 'Upstream', 'properties', 'healthchecks', 'properties', 'active', 'properties', 'type']],
    'HealthcheckType @ components.schemas.Upstream.healthchecks.passive.type' => [HealthcheckType::class, ['components', 'schemas', 'Upstream', 'properties', 'healthchecks', 'properties', 'passive', 'properties', 'type']],
    'HttpsRedirectStatusCode @ components.schemas.RouteJson.https_redirect_status_code' => [HttpsRedirectStatusCode::class, ['components', 'schemas', 'RouteJson', 'properties', 'https_redirect_status_code']],
    'HttpsRedirectStatusCode @ components.schemas.RouteExpression.https_redirect_status_code' => [HttpsRedirectStatusCode::class, ['components', 'schemas', 'RouteExpression', 'properties', 'https_redirect_status_code']],
    'PathHandling @ components.schemas.RouteJson.path_handling' => [PathHandling::class, ['components', 'schemas', 'RouteJson', 'properties', 'path_handling']],
    'PathHandling @ components.schemas.RouteExpression.path_handling' => [PathHandling::class, ['components', 'schemas', 'RouteExpression', 'properties', 'path_handling']],
    'RbacRoleSource @ components.schemas.RBACUserRole.role_source' => [RbacRoleSource::class, ['components', 'schemas', 'RBACUserRole', 'properties', 'role_source']],
    'LogLevel @ paths./debug/node/log-level/{logLevel}.parameters.0.schema' => [LogLevel::class, ['paths', '/debug/node/log-level/{logLevel}', 'parameters', '0', 'schema']],
    'LogLevel @ paths./debug/cluster/log-level/{logLevel}.parameters.0.schema' => [LogLevel::class, ['paths', '/debug/cluster/log-level/{logLevel}', 'parameters', '0', 'schema']],
    'LogLevel @ paths./debug/cluster/control-planes-nodes/log-level/{logLevel}.parameters.0.schema' => [LogLevel::class, ['paths', '/debug/cluster/control-planes-nodes/log-level/{logLevel}', 'parameters', '0', 'schema']],
    'DeploymentType @ components.responses.ReportResponse.content.application/json.schema.deployment_info.type' => [DeploymentType::class, ['components', 'responses', 'ReportResponse', 'content', 'application/json', 'schema', 'properties', 'deployment_info', 'properties', 'type']],
    'Protocol @ components.schemas.Service.protocol' => [Protocol::class, ['components', 'schemas', 'Service', 'properties', 'protocol']],
    'Protocol @ components.schemas.Plugin.protocols.items' => [Protocol::class, ['components', 'schemas', 'Plugin', 'properties', 'protocols', 'items']],
    'Protocol @ components.schemas.PluginWithoutParents.protocols.items' => [Protocol::class, ['components', 'schemas', 'PluginWithoutParents', 'properties', 'protocols', 'items']],
    'Protocol @ components.schemas.RouteJson.protocols.items' => [Protocol::class, ['components', 'schemas', 'RouteJson', 'properties', 'protocols', 'items']],
    'Protocol @ components.schemas.RouteExpression.protocols.items' => [Protocol::class, ['components', 'schemas', 'RouteExpression', 'properties', 'protocols', 'items']],
]);

it('matches the Partial discriminator mapping and each variant const', function (): void {
    $mapping = specNode(['components', 'schemas', 'Partial', 'discriminator', 'mapping']);
    $values = array_map(static fn (PartialType $case): string => $case->value, PartialType::cases());

    expect($values)->toEqualCanonicalizing(array_keys($mapping));

    foreach ($mapping as $type => $ref) {
        expect($ref)->toBeString();
        /** @var string $ref */
        $schema = Spec::schema(substr($ref, strlen('#/components/schemas/')));
        $typeProperty = specNode(['components', 'schemas', substr($ref, strlen('#/components/schemas/')), 'properties', 'type']);

        expect($schema)->not->toBeEmpty()
            ->and($typeProperty['const'] ?? null)->toBe($type);
    }
});

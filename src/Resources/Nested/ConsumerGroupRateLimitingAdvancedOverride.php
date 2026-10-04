<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RateLimitingAdvancedOverride;
use Aybarsm\Kong\AdminApi\Models\RateLimitingAdvancedOverrideInput;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;

/**
 * The rate-limiting-advanced override of one Consumer Group:
 * `/consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced`.
 *
 * Obtain it with `$client->consumerGroups()->rateLimitingAdvancedOverride($groupId)`.
 */
final readonly class ConsumerGroupRateLimitingAdvancedOverride extends AbstractResource
{
    private const string PATH = '/consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced';

    /**
     * Set the override (operationId `update-consumer_groups-group_name_or_id-overrides-plugins-rate-limiting-advanced`, PUT).
     *
     * The spec's request body uses literal dotted keys (`config.limit`, …); see RateLimitingAdvancedOverrideInput
     * and spec-notes Q10.
     *
     * @param RateLimitingAdvancedOverrideInput|array<string, mixed> $override
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_PUT, self::PATH, 'update-consumer_groups-group_name_or_id-overrides-plugins-rate-limiting-advanced', OperationScope::Both)]
    public function upsert(RateLimitingAdvancedOverrideInput|array $override): RateLimitingAdvancedOverride
    {
        return RateLimitingAdvancedOverride::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, 'overrides', 'plugins', 'rate-limiting-advanced'),
            body: $override,
        ));
    }

    /**
     * Remove the override (operationId `delete-consumer_groups-group_name_or_id-overrides-plugins-rate-limiting-advanced`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_DELETE, self::PATH, 'delete-consumer_groups-group_name_or_id-overrides-plugins-rate-limiting-advanced', OperationScope::Both)]
    public function delete(): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, 'overrides', 'plugins', 'rate-limiting-advanced'));
    }
}

<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\StandardWebhooks;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\PluginOrdering;
use Aybarsm\Kong\AdminApi\Models\PluginPartial;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Plugins\TypedPluginInput;
use Override;

/**
 * Request body for a `standard-webhooks` plugin (Standard Webhooks; doc `TrafficControl/standard-webhooks.md`).
 *
 * Validate that incoming webhooks adhere to the Standard Webhooks specification
 *
 * Use it with `create()`, `update()` or `upsert()` of `plugins()` or of the plugin resources nested under
 * Services, Routes, Consumers and Consumer Groups. `name` is sent automatically; null fields are not sent.
 * Scopes the doc allows: `consumer_group`, `route`, `service`.
 * Available from Kong Gateway 3.8 (doc `min_version`).
 */
#[PluginSchema('TrafficControl/standard-webhooks.md', '#')]
final readonly class StandardWebhooksInput implements TypedPluginInput
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'standard-webhooks';

    /**
     * @param StandardWebhooksConfigInput|array<string, mixed>|null $config        The plugin configuration; an array is sent as-is (explicit nulls, vault references). Required by the plugin doc.
     * @param string|null                                           $condition     An expression used for conditional control over plugin execution.
     * @param ForeignKey|string|null                                $consumerGroup If set, the plugin will activate only for requests where the specified group has been authenticated.
     * @param int|null                                              $createdAt     Unix epoch when the resource was created.
     * @param bool|null                                             $enabled       Whether the plugin is applied.
     * @param array<array-key, mixed>|null                          $expressions   CEL expressions driving individual expressible fields in config.
     * @param string|null                                           $id            A string representing a UUID (universally unique identifier).
     * @param string|null                                           $instanceName  A unique string representing a UTF-8 encoded name.
     * @param PluginOrdering|null                                   $ordering
     * @param list<PluginPartial>|null                              $partials      A list of partials to be used by the plugin.
     * @param list<Protocol>|null                                   $protocols     A list of the request protocols that will trigger this plugin.
     * @param ForeignKey|string|null                                $route         If set, the plugin will only activate when receiving requests via the specified route.
     * @param ForeignKey|string|null                                $service       If set, the plugin will only activate when receiving requests via one of the routes belonging to the specifie…
     * @param list<string>|null                                     $tags          An optional set of strings associated with the Plugin for grouping and filtering.
     * @param int|null                                              $updatedAt     Unix epoch when the resource was last updated.
     */
    public function __construct(
        public StandardWebhooksConfigInput|array|null $config = null,
        public ?string $condition = null,
        public ForeignKey|string|null $consumerGroup = null,
        public ?int $createdAt = null,
        public ?bool $enabled = null,
        public ?array $expressions = null,
        public ?string $id = null,
        public ?string $instanceName = null,
        public ?PluginOrdering $ordering = null,
        public ?array $partials = null,
        public ?array $protocols = null,
        public ForeignKey|string|null $route = null,
        public ForeignKey|string|null $service = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'name' => self::NAME,
            'config' => $this->config instanceof StandardWebhooksConfigInput ? $this->config->toArray() : $this->config,
            'condition' => $this->condition,
            'consumer_group' => $this->consumerGroup === null ? null : ForeignKey::of($this->consumerGroup)->toArray(),
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
            'expressions' => $this->expressions,
            'id' => $this->id,
            'instance_name' => $this->instanceName,
            'ordering' => $this->ordering?->toArray(),
            'partials' => Data::toArrays($this->partials),
            'protocols' => Data::enumValues($this->protocols),
            'route' => $this->route === null ? null : ForeignKey::of($this->route)->toArray(),
            'service' => $this->service === null ? null : ForeignKey::of($this->service)->toArray(),
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}

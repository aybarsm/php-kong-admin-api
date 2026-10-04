<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\PluginOrdering;
use Aybarsm\Kong\AdminApi\Models\PluginPartial;
use Aybarsm\Kong\AdminApi\Plugins\TypedPluginInput;
use Override;

/**
 * Request body for a `acme` plugin (ACME; doc `Security/acme.md`).
 *
 * Let's Encrypt and ACMEv2 integration with Kong Gateway
 *
 * Use it with `create()`, `update()` or `upsert()` of `plugins()` or of the plugin resources nested under
 * Services, Routes, Consumers and Consumer Groups. `name` is sent automatically; null fields are not sent.
 * Scopes the doc allows: none (global only).
 * Supports Partials: `redis-ce` for `config.storage_config.redis`.
 * Available from Kong Gateway 2.0 (doc `min_version`).
 */
#[PluginSchema('Security/acme.md', '#')]
final readonly class AcmeInput implements TypedPluginInput
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'acme';

    /**
     * @param AcmeConfigInput|array<string, mixed>|null $config       The plugin configuration; an array is sent as-is (explicit nulls, vault references). Required by the plugin doc.
     * @param string|null                               $condition    An expression used for conditional control over plugin execution.
     * @param int|null                                  $createdAt    Unix epoch when the resource was created.
     * @param bool|null                                 $enabled      Whether the plugin is applied.
     * @param array<array-key, mixed>|null              $expressions  CEL expressions driving individual expressible fields in config.
     * @param string|null                               $id           A string representing a UUID (universally unique identifier).
     * @param string|null                               $instanceName A unique string representing a UTF-8 encoded name.
     * @param PluginOrdering|null                       $ordering
     * @param list<PluginPartial>|null                  $partials     A list of partials to be used by the plugin.
     * @param list<Protocol>|null                       $protocols    A list of the request protocols that will trigger this plugin.
     * @param list<string>|null                         $tags         An optional set of strings associated with the Plugin for grouping and filtering.
     * @param int|null                                  $updatedAt    Unix epoch when the resource was last updated.
     */
    public function __construct(
        public AcmeConfigInput|array|null $config = null,
        public ?string $condition = null,
        public ?int $createdAt = null,
        public ?bool $enabled = null,
        public ?array $expressions = null,
        public ?string $id = null,
        public ?string $instanceName = null,
        public ?PluginOrdering $ordering = null,
        public ?array $partials = null,
        public ?array $protocols = null,
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
            'config' => $this->config instanceof AcmeConfigInput ? $this->config->toArray() : $this->config,
            'condition' => $this->condition,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
            'expressions' => $this->expressions,
            'id' => $this->id,
            'instance_name' => $this->instanceName,
            'ordering' => $this->ordering?->toArray(),
            'partials' => Data::toArrays($this->partials),
            'protocols' => Data::enumValues($this->protocols),
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}

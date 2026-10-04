<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\HmacAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\PluginOrdering;
use Aybarsm\Kong\AdminApi\Models\PluginPartial;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Plugins\TypedPluginInput;
use Override;

/**
 * Request body for a `hmac-auth` plugin (HMAC Auth; doc `Authentication/hmac-auth.md`).
 *
 * Add HMAC Authentication to your Gateway Services
 *
 * Use it with `create()`, `update()` or `upsert()` of `plugins()` or of the plugin resources nested under
 * Services, Routes, Consumers and Consumer Groups. `name` is sent automatically; null fields are not sent.
 * Scopes the doc allows: `route`, `service`.
 * Available from Kong Gateway 1.0 (doc `min_version`).
 */
#[PluginSchema('Authentication/hmac-auth.md', '#')]
final readonly class HmacAuthInput implements TypedPluginInput
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'hmac-auth';

    /**
     * @param HmacAuthConfigInput|array<string, mixed>|null $config       The plugin configuration; an array is sent as-is (explicit nulls, vault references).
     * @param string|null                                   $condition    An expression used for conditional control over plugin execution.
     * @param int|null                                      $createdAt    Unix epoch when the resource was created.
     * @param bool|null                                     $enabled      Whether the plugin is applied.
     * @param array<array-key, mixed>|null                  $expressions  CEL expressions driving individual expressible fields in config.
     * @param string|null                                   $id           A string representing a UUID (universally unique identifier).
     * @param string|null                                   $instanceName A unique string representing a UTF-8 encoded name.
     * @param PluginOrdering|null                           $ordering
     * @param list<PluginPartial>|null                      $partials     A list of partials to be used by the plugin.
     * @param list<Protocol>|null                           $protocols    A list of the request protocols that will trigger this plugin.
     * @param ForeignKey|string|null                        $route        If set, the plugin will only activate when receiving requests via the specified route.
     * @param ForeignKey|string|null                        $service      If set, the plugin will only activate when receiving requests via one of the routes belonging to the specifie…
     * @param list<string>|null                             $tags         An optional set of strings associated with the Plugin for grouping and filtering.
     * @param int|null                                      $updatedAt    Unix epoch when the resource was last updated.
     */
    public function __construct(
        public HmacAuthConfigInput|array|null $config = null,
        public ?string $condition = null,
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
            'config' => $this->config instanceof HmacAuthConfigInput ? $this->config->toArray() : $this->config,
            'condition' => $this->condition,
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

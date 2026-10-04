<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A Plugin as returned by the Admin API (spec schema `Plugin`).
 *
 * Nested POST/PUT bodies use `PluginWithoutParents`, which has the same properties.
 */
#[Schema('Plugin')]
final readonly class Plugin implements Model
{
    /**
     * @param string                       $name          The name of the Plugin that's going to be added.
     * @param string|null                  $condition     An expression used for conditional control over plugin execution.
     * @param array<array-key, mixed>|null $config        The configuration properties for the Plugin which can be found on the plugins documentation page in the [Kong…
     * @param ForeignKey|null              $consumer      If set, the plugin will activate only for requests where the specified has been authenticated.
     * @param ForeignKey|null              $consumerGroup If set, the plugin will activate only for requests where the specified group has been authenticated.
     * @param int|null                     $createdAt     Unix epoch when the resource was created.
     * @param bool|null                    $enabled       Whether the plugin is applied.
     * @param array<array-key, mixed>|null $expressions   CEL expressions driving individual expressible fields in config.
     * @param string|null                  $id            A string representing a UUID (universally unique identifier).
     * @param string|null                  $instanceName  A unique string representing a UTF-8 encoded name.
     * @param PluginOrdering|null          $ordering
     * @param list<PluginPartial>|null     $partials      A list of partials to be used by the plugin.
     * @param list<Protocol>|null          $protocols     A list of the request protocols that will trigger this plugin.
     * @param ForeignKey|null              $route         If set, the plugin will only activate when receiving requests via the specified route.
     * @param ForeignKey|null              $service       If set, the plugin will only activate when receiving requests via one of the routes belonging to the specifie…
     * @param list<string>|null            $tags          An optional set of strings associated with the Plugin for grouping and filtering.
     * @param int|null                     $updatedAt     Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $name,
        public ?string $condition = null,
        public ?array $config = null,
        public ?ForeignKey $consumer = null,
        public ?ForeignKey $consumerGroup = null,
        public ?int $createdAt = null,
        public ?bool $enabled = null,
        public ?array $expressions = null,
        public ?string $id = null,
        public ?string $instanceName = null,
        public ?PluginOrdering $ordering = null,
        public ?array $partials = null,
        public ?array $protocols = null,
        public ?ForeignKey $route = null,
        public ?ForeignKey $service = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consumer = Data::mapOrNull($data, 'consumer');
        $consumerGroup = Data::mapOrNull($data, 'consumer_group');
        $ordering = Data::mapOrNull($data, 'ordering');
        $partials = Data::listOfMapsOrNull($data, 'partials');
        $route = Data::mapOrNull($data, 'route');
        $service = Data::mapOrNull($data, 'service');

        return new self(
            name: Data::string($data, 'name'),
            condition: Data::stringOrNull($data, 'condition'),
            config: Data::freeFormOrNull($data, 'config'),
            consumer: $consumer === null ? null : ForeignKey::fromArray($consumer),
            consumerGroup: $consumerGroup === null ? null : ForeignKey::fromArray($consumerGroup),
            createdAt: Data::intOrNull($data, 'created_at'),
            enabled: Data::boolOrNull($data, 'enabled'),
            expressions: Data::freeFormOrNull($data, 'expressions'),
            id: Data::stringOrNull($data, 'id'),
            instanceName: Data::stringOrNull($data, 'instance_name'),
            ordering: $ordering === null ? null : PluginOrdering::fromArray($ordering),
            partials: $partials === null ? null : array_map(PluginPartial::fromArray(...), $partials),
            protocols: Data::enumListOrNull($data, 'protocols', Protocol::class),
            route: $route === null ? null : ForeignKey::fromArray($route),
            service: $service === null ? null : ForeignKey::fromArray($service),
            tags: Data::stringListOrNull($data, 'tags'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'name' => $this->name,
            'condition' => $this->condition,
            'config' => $this->config,
            'consumer' => $this->consumer?->toArray(),
            'consumer_group' => $this->consumerGroup?->toArray(),
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
            'expressions' => $this->expressions,
            'id' => $this->id,
            'instance_name' => $this->instanceName,
            'ordering' => $this->ordering?->toArray(),
            'partials' => Data::toArrays($this->partials),
            'protocols' => Data::enumValues($this->protocols),
            'route' => $this->route?->toArray(),
            'service' => $this->service?->toArray(),
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}

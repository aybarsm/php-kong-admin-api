<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * An Event Hook as listed by the Admin API (items of spec schema `Event-Hooks`).
 */
#[Schema('#/components/schemas/Event-Hooks/properties/data')]
final readonly class EventHook implements Model
{
    /**
     * @param EventHookConfig|null $config    Configuration for the event hook
     * @param int|null             $createdAt
     * @param string|null          $event
     * @param string|null          $handler
     * @param string|null          $id
     * @param string|null          $onChange
     * @param int|null             $snooze
     * @param string|null          $source
     */
    public function __construct(
        public ?EventHookConfig $config = null,
        public ?int $createdAt = null,
        public ?string $event = null,
        public ?string $handler = null,
        public ?string $id = null,
        public ?string $onChange = null,
        public ?int $snooze = null,
        public ?string $source = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $config = Data::mapOrNull($data, 'config');

        return new self(
            config: $config === null ? null : EventHookConfig::fromArray($config),
            createdAt: Data::intOrNull($data, 'created_at'),
            event: Data::stringOrNull($data, 'event'),
            handler: Data::stringOrNull($data, 'handler'),
            id: Data::stringOrNull($data, 'id'),
            onChange: Data::stringOrNull($data, 'on_change'),
            snooze: Data::intOrNull($data, 'snooze'),
            source: Data::stringOrNull($data, 'source'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'config' => $this->config?->toArray(),
            'created_at' => $this->createdAt,
            'event' => $this->event,
            'handler' => $this->handler,
            'id' => $this->id,
            'on_change' => $this->onChange,
            'snooze' => $this->snooze,
            'source' => $this->source,
        ]);
    }
}

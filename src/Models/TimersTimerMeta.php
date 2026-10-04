<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `stats.timers.meta` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.stats.timers.meta`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/stats/properties/timers/additionalProperties/properties/meta')]
final readonly class TimersTimerMeta implements Model
{
    /**
     * @param string|null $callstack Program callstack of created timers.
     * @param string|null $name      The name of the timer's metadata.
     */
    public function __construct(
        public ?string $callstack = null,
        public ?string $name = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            callstack: Data::stringOrNull($data, 'callstack'),
            name: Data::stringOrNull($data, 'name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'callstack' => $this->callstack,
            'name' => $this->name,
        ]);
    }
}

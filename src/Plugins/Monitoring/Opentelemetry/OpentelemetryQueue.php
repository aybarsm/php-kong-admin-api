<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.queue` object of the OpenTelemetry plugin (doc `Monitoring/opentelemetry.md`).
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config/properties/queue')]
final readonly class OpentelemetryQueue implements Model
{
    /**
     * @param float|null                              $breakerCooldown    Time in seconds the circuit breaker stays open (fast-shedding entries) before it allows a single batch throug… Default: `60`.
     * @param OpentelemetryQueueConcurrencyLimit|null $concurrencyLimit   The number of of queue delivery timers. Default: `1`.
     * @param int|null                                $failureThreshold   Number of consecutive failed batches after which the queue opens its circuit breaker and drops entries instea… Default: `0`.
     * @param float|null                              $initialRetryDelay  Time in seconds before the initial retry is made for a failing batch. Default: `0.01`.
     * @param int|null                                $maxBatchSize       Maximum number of entries that can be processed at a time. Default: `200`.
     * @param int|null                                $maxBytes           Maximum number of bytes that can be waiting on a queue, requires string content.
     * @param float|null                              $maxCoalescingDelay Maximum number of (fractional) seconds to elapse after the first entry was queued before the queue starts cal… Default: `1`.
     * @param int|null                                $maxEntries         Maximum number of entries that can be waiting on the queue. Default: `10000`.
     * @param float|null                              $maxRetryDelay      Maximum time in seconds between retries, caps exponential backoff. Default: `60`.
     * @param float|null                              $maxRetryTime       Time in seconds before the queue gives up calling a failed handler for a batch. Default: `60`.
     */
    public function __construct(
        public ?float $breakerCooldown = null,
        public ?OpentelemetryQueueConcurrencyLimit $concurrencyLimit = null,
        public ?int $failureThreshold = null,
        public ?float $initialRetryDelay = null,
        public ?int $maxBatchSize = null,
        public ?int $maxBytes = null,
        public ?float $maxCoalescingDelay = null,
        public ?int $maxEntries = null,
        public ?float $maxRetryDelay = null,
        public ?float $maxRetryTime = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            breakerCooldown: Data::floatOrNull($data, 'breaker_cooldown'),
            concurrencyLimit: Data::enumOrNull($data, 'concurrency_limit', OpentelemetryQueueConcurrencyLimit::class),
            failureThreshold: Data::intOrNull($data, 'failure_threshold'),
            initialRetryDelay: Data::floatOrNull($data, 'initial_retry_delay'),
            maxBatchSize: Data::intOrNull($data, 'max_batch_size'),
            maxBytes: Data::intOrNull($data, 'max_bytes'),
            maxCoalescingDelay: Data::floatOrNull($data, 'max_coalescing_delay'),
            maxEntries: Data::intOrNull($data, 'max_entries'),
            maxRetryDelay: Data::floatOrNull($data, 'max_retry_delay'),
            maxRetryTime: Data::floatOrNull($data, 'max_retry_time'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'breaker_cooldown' => $this->breakerCooldown,
            'concurrency_limit' => $this->concurrencyLimit?->value,
            'failure_threshold' => $this->failureThreshold,
            'initial_retry_delay' => $this->initialRetryDelay,
            'max_batch_size' => $this->maxBatchSize,
            'max_bytes' => $this->maxBytes,
            'max_coalescing_delay' => $this->maxCoalescingDelay,
            'max_entries' => $this->maxEntries,
            'max_retry_delay' => $this->maxRetryDelay,
            'max_retry_time' => $this->maxRetryTime,
        ]);
    }
}

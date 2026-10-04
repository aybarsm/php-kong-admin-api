<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\StandardWebhooks;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Typed `config` request body of a `standard-webhooks` plugin (Standard Webhooks; doc `TrafficControl/standard-webhooks.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/standard-webhooks.md', '#/properties/config')]
final readonly class StandardWebhooksConfigInput implements Input
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['secretV1'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'standard-webhooks';

    /**
     * @param string|null $secretV1        Webhook secret This field is [referenceable](/gateway/entities/vault/#how-do-i-reference-secrets-stored-in-a-… Required by the plugin doc.
     * @param int|null    $toleranceSecond Tolerance of the webhook timestamp in seconds. Default: `300`.
     */
    public function __construct(
        #[SensitiveParameter]
        public ?string $secretV1 = null,
        public ?int $toleranceSecond = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'secret_v1' => $this->secretV1,
            'tolerance_second' => $this->toleranceSecond,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}

<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\StandardWebhooks;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `standard-webhooks` plugin (Standard Webhooks; doc `TrafficControl/standard-webhooks.md`).
 *
 * Read it from a returned Plugin with `StandardWebhooksConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/standard-webhooks.md', '#/properties/config')]
final readonly class StandardWebhooksConfig implements PluginConfig
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['secretV1'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'standard-webhooks';

    /**
     * @param string   $secretV1        Webhook secret This field is [referenceable](/gateway/entities/vault/#how-do-i-reference-secrets-stored-in-a-…
     * @param int|null $toleranceSecond Tolerance of the webhook timestamp in seconds. Default: `300`.
     */
    public function __construct(
        public string $secretV1,
        public ?int $toleranceSecond = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            secretV1: Data::string($data, 'secret_v1'),
            toleranceSecond: Data::intOrNull($data, 'tolerance_second'),
        );
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

    /**
     * Reads the typed configuration of a `standard-webhooks` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `standard-webhooks` plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    #[Override]
    public static function fromPlugin(Plugin $plugin): static
    {
        if ($plugin->name !== self::NAME) {
            throw new InvalidArgumentException(sprintf('Expected a "%s" plugin, got "%s".', self::NAME, $plugin->name));
        }

        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));
    }
}

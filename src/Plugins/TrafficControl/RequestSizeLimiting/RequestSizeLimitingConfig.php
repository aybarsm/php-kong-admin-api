<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RequestSizeLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `request-size-limiting` plugin (Request Size Limiting; doc `TrafficControl/request-size-limiting.md`).
 *
 * Read it from a returned Plugin with `RequestSizeLimitingConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/request-size-limiting.md', '#/properties/config')]
final readonly class RequestSizeLimitingConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'request-size-limiting';

    /**
     * @param int|null      $allowedPayloadSize   Allowed request payload size in megabytes. Default: `128`.
     * @param bool|null     $requireContentLength Set to `true` to ensure a valid `Content-Length` header exists before reading the request body. Default: `false`.
     * @param SizeUnit|null $sizeUnit             Size unit can be set either in `bytes`, `kilobytes`, or `megabytes` (default). Default: `megabytes`.
     */
    public function __construct(
        public ?int $allowedPayloadSize = null,
        public ?bool $requireContentLength = null,
        public ?SizeUnit $sizeUnit = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            allowedPayloadSize: Data::intOrNull($data, 'allowed_payload_size'),
            requireContentLength: Data::boolOrNull($data, 'require_content_length'),
            sizeUnit: Data::enumOrNull($data, 'size_unit', SizeUnit::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allowed_payload_size' => $this->allowedPayloadSize,
            'require_content_length' => $this->requireContentLength,
            'size_unit' => $this->sizeUnit?->value,
        ]);
    }

    /**
     * Reads the typed configuration of a `request-size-limiting` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `request-size-limiting` plugin
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

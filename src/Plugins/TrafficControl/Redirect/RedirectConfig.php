<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\Redirect;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `redirect` plugin (Redirect; doc `TrafficControl/redirect.md`).
 *
 * Read it from a returned Plugin with `RedirectConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/redirect.md', '#/properties/config')]
final readonly class RedirectConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'redirect';

    /**
     * @param string    $location         The URL to redirect to
     * @param bool|null $keepIncomingPath Use the incoming request's path and query string in the redirect URL Default: `false`.
     * @param int|null  $statusCode       The response code to send. Default: `301`.
     */
    public function __construct(
        public string $location,
        public ?bool $keepIncomingPath = null,
        public ?int $statusCode = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            location: Data::string($data, 'location'),
            keepIncomingPath: Data::boolOrNull($data, 'keep_incoming_path'),
            statusCode: Data::intOrNull($data, 'status_code'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'location' => $this->location,
            'keep_incoming_path' => $this->keepIncomingPath,
            'status_code' => $this->statusCode,
        ]);
    }

    /**
     * Reads the typed configuration of a `redirect` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `redirect` plugin
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

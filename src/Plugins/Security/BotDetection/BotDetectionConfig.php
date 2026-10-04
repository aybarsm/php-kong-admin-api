<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\BotDetection;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `bot-detection` plugin (Bot Detection; doc `Security/bot-detection.md`).
 *
 * Read it from a returned Plugin with `BotDetectionConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Security/bot-detection.md', '#/properties/config')]
final readonly class BotDetectionConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'bot-detection';

    /**
     * @param list<string>|null $allow An array of regular expressions that should be allowed. Default: `[]`.
     * @param list<string>|null $deny  An array of regular expressions that should be denied. Default: `[]`.
     */
    public function __construct(
        public ?array $allow = null,
        public ?array $deny = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            allow: Data::stringListOrNull($data, 'allow'),
            deny: Data::stringListOrNull($data, 'deny'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow' => $this->allow,
            'deny' => $this->deny,
        ]);
    }

    /**
     * Reads the typed configuration of a `bot-detection` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `bot-detection` plugin
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

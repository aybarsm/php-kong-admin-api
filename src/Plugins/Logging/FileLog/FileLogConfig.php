<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\FileLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `file-log` plugin (File Log; doc `Logging/file-log.md`).
 *
 * Read it from a returned Plugin with `FileLogConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Logging/file-log.md', '#/properties/config')]
final readonly class FileLogConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'file-log';

    /**
     * @param string                        $path              The file path of the output log file.
     * @param array<array-key, string>|null $customFieldsByLua Lua code as a key-value map
     * @param bool|null                     $reopen            Determines whether the log file is closed and reopened on every request. Default: `false`.
     */
    public function __construct(
        public string $path,
        public ?array $customFieldsByLua = null,
        public ?bool $reopen = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            path: Data::string($data, 'path'),
            customFieldsByLua: Data::stringMapOrNull($data, 'custom_fields_by_lua'),
            reopen: Data::boolOrNull($data, 'reopen'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'path' => $this->path,
            'custom_fields_by_lua' => $this->customFieldsByLua,
            'reopen' => $this->reopen,
        ]);
    }

    /**
     * Reads the typed configuration of a `file-log` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `file-log` plugin
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

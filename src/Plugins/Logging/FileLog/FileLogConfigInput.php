<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\FileLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `file-log` plugin (File Log; doc `Logging/file-log.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Logging/file-log.md', '#/properties/config')]
final readonly class FileLogConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'file-log';

    /**
     * @param string|null                   $path              The file path of the output log file. Required by the plugin doc.
     * @param array<array-key, string>|null $customFieldsByLua Lua code as a key-value map
     * @param bool|null                     $reopen            Determines whether the log file is closed and reopened on every request. Default: `false`.
     */
    public function __construct(
        public ?string $path = null,
        public ?array $customFieldsByLua = null,
        public ?bool $reopen = null,
    ) {
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
}

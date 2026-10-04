<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins;

use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Models\Plugin;

/**
 * The typed `config` of a documented plugin (`Plugins\{Category}\{Plugin}\{Plugin}Config`).
 *
 * Every implementation declares `public const string NAME`, the plugin's wire name.
 */
interface PluginConfig extends Model
{
    /**
     * Reads the typed configuration of a Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is a different plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    public static function fromPlugin(Plugin $plugin): static;
}

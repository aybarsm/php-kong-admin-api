<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins;

use Aybarsm\Kong\AdminApi\Contracts\Input;

/**
 * A plugin request body bound to one documented plugin (`Plugins\{Category}\{Plugin}\{Plugin}Input`).
 *
 * `toArray()` always includes the plugin's wire `name`, so the generic `PluginInput::$name` isn't needed.
 * Every implementation declares `public const string NAME`.
 */
interface TypedPluginInput extends Input
{
}

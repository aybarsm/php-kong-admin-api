<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\ResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.add.json_types[]` in the Response Transformer Plugin doc.
 */
#[PluginSchema('Transformation/response-transformer.md', '#/properties/config/properties/add/properties/json_types/items')]
enum ResponseTransformerAddJsonTypes: string
{
    case Boolean = 'boolean';
    case Number = 'number';
    case String = 'string';
}

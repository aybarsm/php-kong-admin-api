<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.response_streaming` in the AI Proxy Plugin doc.
 *
 * Whether to 'optionally allow', 'deny', or 'always' (force) the streaming of answers via server sent events.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/response_streaming')]
enum ResponseStreaming: string
{
    case Allow = 'allow';
    case Always = 'always';
    case Deny = 'deny';
}

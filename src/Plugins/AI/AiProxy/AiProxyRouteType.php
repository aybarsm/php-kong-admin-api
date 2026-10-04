<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.route_type` in the AI Proxy Plugin doc.
 *
 * The model's operation implementation, for this provider.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/route_type')]
enum AiProxyRouteType: string
{
    case AudioV1AudioSpeech = 'audio/v1/audio/speech';
    case AudioV1AudioTranscriptions = 'audio/v1/audio/transcriptions';
    case AudioV1AudioTranslations = 'audio/v1/audio/translations';
    case ImageV1ImagesEdits = 'image/v1/images/edits';
    case ImageV1ImagesGenerations = 'image/v1/images/generations';
    case LlmV1Assistants = 'llm/v1/assistants';
    case LlmV1Batches = 'llm/v1/batches';
    case LlmV1Chat = 'llm/v1/chat';
    case LlmV1Completions = 'llm/v1/completions';
    case LlmV1Embeddings = 'llm/v1/embeddings';
    case LlmV1Files = 'llm/v1/files';
    case LlmV1Responses = 'llm/v1/responses';
    case Preserve = 'preserve';
    case RealtimeV1Realtime = 'realtime/v1/realtime';
    case VideoV1VideosGenerations = 'video/v1/videos/generations';
}

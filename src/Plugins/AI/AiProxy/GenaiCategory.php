<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.genai_category` in the AI Proxy Plugin doc.
 *
 * Generative AI category of the request
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/genai_category')]
enum GenaiCategory: string
{
    case AudioSpeech = 'audio/speech';
    case AudioTranscription = 'audio/transcription';
    case ImageGeneration = 'image/generation';
    case TextEmbeddings = 'text/embeddings';
    case TextGeneration = 'text/generation';
    case VideoGeneration = 'video/generation';
}

<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptGuard;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.genai_category` in the AI Prompt Guard Plugin doc.
 *
 * Generative AI category of the request
 */
#[PluginSchema('AI/ai-prompt-guard.md', '#/properties/config/properties/genai_category')]
enum AiPromptGuardGenaiCategory: string
{
    case AudioSpeech = 'audio/speech';
    case AudioTranscription = 'audio/transcription';
    case ImageGeneration = 'image/generation';
    case RealtimeGeneration = 'realtime/generation';
    case TextEmbeddings = 'text/embeddings';
    case TextGeneration = 'text/generation';
    case VideoGeneration = 'video/generation';
}

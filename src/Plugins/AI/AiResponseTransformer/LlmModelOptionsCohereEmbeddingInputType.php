<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.llm.model.options.cohere.embedding_input_type` in the AI Response Transformer Plugin doc.
 *
 * The purpose of the input text to calculate embedding vectors.
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/cohere/properties/embedding_input_type')]
enum LlmModelOptionsCohereEmbeddingInputType: string
{
    case Classification = 'classification';
    case Clustering = 'clustering';
    case Image = 'image';
    case SearchDocument = 'search_document';
    case SearchQuery = 'search_query';
}

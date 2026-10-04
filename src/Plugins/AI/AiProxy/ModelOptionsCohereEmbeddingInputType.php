<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.model.options.cohere.embedding_input_type` in the AI Proxy Plugin doc.
 *
 * The purpose of the input text to calculate embedding vectors.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/cohere/properties/embedding_input_type')]
enum ModelOptionsCohereEmbeddingInputType: string
{
    case Classification = 'classification';
    case Clustering = 'clustering';
    case Image = 'image';
    case SearchDocument = 'search_document';
    case SearchQuery = 'search_query';
}

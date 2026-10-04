<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.model.options.bedrock` object of the AI Request Transformer plugin (doc `AI/ai-request-transformer.md`).
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/bedrock')]
final readonly class LlmModelOptionsBedrock implements Model
{
    /**
     * @param string|null $awsAssumeRoleArn         If using AWS providers (Bedrock) you can assume a different role after authentication with the current IAM co…
     * @param string|null $awsRegion                If using AWS providers (Bedrock) you can override the `AWS_REGION` environment variable by setting this optio…
     * @param string|null $awsRoleSessionName       If using AWS providers (Bedrock), set the identifier of the assumed role session.
     * @param string|null $awsStsEndpointUrl        If using AWS providers (Bedrock), override the STS endpoint URL when assuming a different role.
     * @param string|null $batchBucketPrefix        S3 URI prefix (s3://bucket/prefix/) where Bedrock will get input files from and store results to for native b…
     * @param string|null $batchRoleArn             AWS role arn used for calling batch API.
     * @param bool|null   $embeddingsNormalize      If using AWS providers (Bedrock), set to true to normalize the embeddings. Default: `false`.
     * @param string|null $performanceConfigLatency Force the client's performance configuration 'latency' for all requests.
     * @param string|null $videoOutputS3Uri         S3 URI (s3://bucket/prefix) where Bedrock will store generated video files.
     */
    public function __construct(
        public ?string $awsAssumeRoleArn = null,
        public ?string $awsRegion = null,
        public ?string $awsRoleSessionName = null,
        public ?string $awsStsEndpointUrl = null,
        public ?string $batchBucketPrefix = null,
        public ?string $batchRoleArn = null,
        public ?bool $embeddingsNormalize = null,
        public ?string $performanceConfigLatency = null,
        public ?string $videoOutputS3Uri = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            awsAssumeRoleArn: Data::stringOrNull($data, 'aws_assume_role_arn'),
            awsRegion: Data::stringOrNull($data, 'aws_region'),
            awsRoleSessionName: Data::stringOrNull($data, 'aws_role_session_name'),
            awsStsEndpointUrl: Data::stringOrNull($data, 'aws_sts_endpoint_url'),
            batchBucketPrefix: Data::stringOrNull($data, 'batch_bucket_prefix'),
            batchRoleArn: Data::stringOrNull($data, 'batch_role_arn'),
            embeddingsNormalize: Data::boolOrNull($data, 'embeddings_normalize'),
            performanceConfigLatency: Data::stringOrNull($data, 'performance_config_latency'),
            videoOutputS3Uri: Data::stringOrNull($data, 'video_output_s3_uri'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'aws_assume_role_arn' => $this->awsAssumeRoleArn,
            'aws_region' => $this->awsRegion,
            'aws_role_session_name' => $this->awsRoleSessionName,
            'aws_sts_endpoint_url' => $this->awsStsEndpointUrl,
            'batch_bucket_prefix' => $this->batchBucketPrefix,
            'batch_role_arn' => $this->batchRoleArn,
            'embeddings_normalize' => $this->embeddingsNormalize,
            'performance_config_latency' => $this->performanceConfigLatency,
            'video_output_s3_uri' => $this->videoOutputS3Uri,
        ]);
    }
}

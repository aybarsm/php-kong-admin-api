<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.brute_force_protection.redis.cloud_authentication` object of the Basic Auth plugin (doc `Authentication/basic-auth.md`).
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config/properties/brute_force_protection/properties/redis/properties/cloud_authentication')]
final readonly class BruteForceProtectionRedisCloudAuthentication implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['awsAccessKeyId', 'awsAssumeRoleArn', 'awsRoleSessionName', 'awsSecretAccessKey', 'azureClientId', 'azureClientSecret', 'azureTenantId', 'gcpServiceAccountJson'];

    /**
     * @param BruteForceProtectionRedisCloudAuthenticationAuthProvider|null $authProvider          Auth providers to be used to authenticate to a Cloud Provider's Redis instance.
     * @param string|null                                                   $awsAccessKeyId        AWS Access Key ID to be used for authentication when `auth_provider` is set to `aws`.
     * @param string|null                                                   $awsAssumeRoleArn      The ARN of the IAM role to assume for generating ElastiCache IAM authentication tokens.
     * @param string|null                                                   $awsCacheName          The name of the AWS Elasticache cluster when `auth_provider` is set to `aws`.
     * @param bool|null                                                     $awsIsServerless       This flag specifies whether the cluster is serverless when auth_provider is set to `aws`. Default: `true`.
     * @param string|null                                                   $awsRegion             The region of the AWS ElastiCache cluster when `auth_provider` is set to `aws`.
     * @param string|null                                                   $awsRoleSessionName    The session name for the temporary credentials when assuming the IAM role.
     * @param string|null                                                   $awsSecretAccessKey    AWS Secret Access Key to be used for authentication when `auth_provider` is set to `aws`.
     * @param string|null                                                   $azureClientId         Azure Client ID to be used for authentication when `auth_provider` is set to `azure`.
     * @param string|null                                                   $azureClientSecret     Azure Client Secret to be used for authentication when `auth_provider` is set to `azure`.
     * @param string|null                                                   $azureTenantId         Azure Tenant ID to be used for authentication when `auth_provider` is set to `azure`.
     * @param string|null                                                   $gcpServiceAccountJson GCP Service Account JSON to be used for authentication when `auth_provider` is set to `gcp`.
     * @param BruteForceProtectionRedisCloudAuthenticationOauth|null        $oauth                 OAuth 2.0 client configuration used to authenticate to Redis when `auth_provider` is set to `oauth`.
     */
    public function __construct(
        public ?BruteForceProtectionRedisCloudAuthenticationAuthProvider $authProvider = null,
        public ?string $awsAccessKeyId = null,
        public ?string $awsAssumeRoleArn = null,
        public ?string $awsCacheName = null,
        public ?bool $awsIsServerless = null,
        public ?string $awsRegion = null,
        public ?string $awsRoleSessionName = null,
        public ?string $awsSecretAccessKey = null,
        public ?string $azureClientId = null,
        public ?string $azureClientSecret = null,
        public ?string $azureTenantId = null,
        public ?string $gcpServiceAccountJson = null,
        public ?BruteForceProtectionRedisCloudAuthenticationOauth $oauth = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $oauth = Data::mapOrNull($data, 'oauth');

        return new self(
            authProvider: Data::enumOrNull($data, 'auth_provider', BruteForceProtectionRedisCloudAuthenticationAuthProvider::class),
            awsAccessKeyId: Data::stringOrNull($data, 'aws_access_key_id'),
            awsAssumeRoleArn: Data::stringOrNull($data, 'aws_assume_role_arn'),
            awsCacheName: Data::stringOrNull($data, 'aws_cache_name'),
            awsIsServerless: Data::boolOrNull($data, 'aws_is_serverless'),
            awsRegion: Data::stringOrNull($data, 'aws_region'),
            awsRoleSessionName: Data::stringOrNull($data, 'aws_role_session_name'),
            awsSecretAccessKey: Data::stringOrNull($data, 'aws_secret_access_key'),
            azureClientId: Data::stringOrNull($data, 'azure_client_id'),
            azureClientSecret: Data::stringOrNull($data, 'azure_client_secret'),
            azureTenantId: Data::stringOrNull($data, 'azure_tenant_id'),
            gcpServiceAccountJson: Data::stringOrNull($data, 'gcp_service_account_json'),
            oauth: $oauth === null ? null : BruteForceProtectionRedisCloudAuthenticationOauth::fromArray($oauth),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'auth_provider' => $this->authProvider?->value,
            'aws_access_key_id' => $this->awsAccessKeyId,
            'aws_assume_role_arn' => $this->awsAssumeRoleArn,
            'aws_cache_name' => $this->awsCacheName,
            'aws_is_serverless' => $this->awsIsServerless,
            'aws_region' => $this->awsRegion,
            'aws_role_session_name' => $this->awsRoleSessionName,
            'aws_secret_access_key' => $this->awsSecretAccessKey,
            'azure_client_id' => $this->azureClientId,
            'azure_client_secret' => $this->azureClientSecret,
            'azure_tenant_id' => $this->azureTenantId,
            'gcp_service_account_json' => $this->gcpServiceAccountJson,
            'oauth' => $this->oauth?->toArray(),
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}

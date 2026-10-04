<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.auth` object of the AI Request Transformer plugin (doc `AI/ai-request-transformer.md`).
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config/properties/llm/properties/auth')]
final readonly class LlmAuth implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['awsAccessKeyId', 'awsSecretAccessKey', 'azureClientSecret', 'gcpServiceAccountJson', 'headerValue', 'paramValue'];

    /**
     * @param bool|null                 $allowOverride           If enabled, the authorization header or parameter can be overridden in the request by the value configured in… Default: `false`.
     * @param string|null               $awsAccessKeyId          Set this if you are using an AWS provider (Bedrock) and you are authenticating using static IAM User credenti…
     * @param string|null               $awsSecretAccessKey      Set this if you are using an AWS provider (Bedrock) and you are authenticating using static IAM User credenti…
     * @param string|null               $azureClientId           If azure_use_managed_identity is set to true, and you need to use a different user-assigned identity for this…
     * @param string|null               $azureClientSecret       If azure_use_managed_identity is set to true, and you need to use a different user-assigned identity for this…
     * @param string|null               $azureTenantId           If azure_use_managed_identity is set to true, and you need to use a different user-assigned identity for this…
     * @param bool|null                 $azureUseManagedIdentity Set true to use the Azure Cloud Managed Identity (or user-assigned identity) to authenticate with Azure-provi… Default: `false`.
     * @param string|null               $gcpMetadataUrl          Custom metadata URL for GCP authentication.
     * @param string|null               $gcpOauthTokenUrl        Custom OAuth token URL for GCP authentication.
     * @param string|null               $gcpServiceAccountJson   Set this field to the full JSON of the GCP service account to authenticate, if required.
     * @param bool|null                 $gcpUseServiceAccount    Use service account auth for GCP-based providers and models. Default: `false`.
     * @param string|null               $headerName              If AI model requires authentication via Authorization or API key header, specify its name here.
     * @param string|null               $headerValue             Specify the full auth header value for 'header_name', for example 'Bearer key' or just 'key'.
     * @param LlmAuthParamLocation|null $paramLocation           Specify whether the 'param_name' and 'param_value' options go in a query string, or the POST form/JSON body.
     * @param string|null               $paramName               If AI model requires authentication via query parameter, specify its name here.
     * @param string|null               $paramValue              Specify the full parameter value for 'param_name'.
     */
    public function __construct(
        public ?bool $allowOverride = null,
        public ?string $awsAccessKeyId = null,
        public ?string $awsSecretAccessKey = null,
        public ?string $azureClientId = null,
        public ?string $azureClientSecret = null,
        public ?string $azureTenantId = null,
        public ?bool $azureUseManagedIdentity = null,
        public ?string $gcpMetadataUrl = null,
        public ?string $gcpOauthTokenUrl = null,
        public ?string $gcpServiceAccountJson = null,
        public ?bool $gcpUseServiceAccount = null,
        public ?string $headerName = null,
        public ?string $headerValue = null,
        public ?LlmAuthParamLocation $paramLocation = null,
        public ?string $paramName = null,
        public ?string $paramValue = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            allowOverride: Data::boolOrNull($data, 'allow_override'),
            awsAccessKeyId: Data::stringOrNull($data, 'aws_access_key_id'),
            awsSecretAccessKey: Data::stringOrNull($data, 'aws_secret_access_key'),
            azureClientId: Data::stringOrNull($data, 'azure_client_id'),
            azureClientSecret: Data::stringOrNull($data, 'azure_client_secret'),
            azureTenantId: Data::stringOrNull($data, 'azure_tenant_id'),
            azureUseManagedIdentity: Data::boolOrNull($data, 'azure_use_managed_identity'),
            gcpMetadataUrl: Data::stringOrNull($data, 'gcp_metadata_url'),
            gcpOauthTokenUrl: Data::stringOrNull($data, 'gcp_oauth_token_url'),
            gcpServiceAccountJson: Data::stringOrNull($data, 'gcp_service_account_json'),
            gcpUseServiceAccount: Data::boolOrNull($data, 'gcp_use_service_account'),
            headerName: Data::stringOrNull($data, 'header_name'),
            headerValue: Data::stringOrNull($data, 'header_value'),
            paramLocation: Data::enumOrNull($data, 'param_location', LlmAuthParamLocation::class),
            paramName: Data::stringOrNull($data, 'param_name'),
            paramValue: Data::stringOrNull($data, 'param_value'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow_override' => $this->allowOverride,
            'aws_access_key_id' => $this->awsAccessKeyId,
            'aws_secret_access_key' => $this->awsSecretAccessKey,
            'azure_client_id' => $this->azureClientId,
            'azure_client_secret' => $this->azureClientSecret,
            'azure_tenant_id' => $this->azureTenantId,
            'azure_use_managed_identity' => $this->azureUseManagedIdentity,
            'gcp_metadata_url' => $this->gcpMetadataUrl,
            'gcp_oauth_token_url' => $this->gcpOauthTokenUrl,
            'gcp_service_account_json' => $this->gcpServiceAccountJson,
            'gcp_use_service_account' => $this->gcpUseServiceAccount,
            'header_name' => $this->headerName,
            'header_value' => $this->headerValue,
            'param_location' => $this->paramLocation?->value,
            'param_name' => $this->paramName,
            'param_value' => $this->paramValue,
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

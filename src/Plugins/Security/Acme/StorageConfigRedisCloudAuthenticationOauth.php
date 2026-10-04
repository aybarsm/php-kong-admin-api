<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.storage_config.redis.cloud_authentication.oauth` object of the ACME plugin (doc `Security/acme.md`).
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/redis/properties/cloud_authentication/properties/oauth')]
final readonly class StorageConfigRedisCloudAuthenticationOauth implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['clientSecret', 'password'];

    /**
     * @param StorageConfigRedisCloudAuthenticationOauthAuthMethod|null         $authMethod         Client authentication method used against the token endpoint. Default: `client_secret_post`.
     * @param string|null                                                       $clientId           OAuth 2.0 client ID.
     * @param string|null                                                       $clientSecret       OAuth 2.0 client secret.
     * @param StorageConfigRedisCloudAuthenticationOauthClientSecretJwtAlg|null $clientSecretJwtAlg Signing algorithm used for `client_secret_jwt` client authentication. Default: `HS512`.
     * @param StorageConfigRedisCloudAuthenticationOauthGrantType|null          $grantType          OAuth 2.0 grant type used to request access tokens. Default: `client_credentials`.
     * @param string|null                                                       $password           Resource owner password, used with the `password` grant type.
     * @param string|null                                                       $redisUsername      Static Redis ACL username sent with `AUTH <username> <token>`.
     * @param string|null                                                       $redisUsernameClaim JWT claim in the access token used to derive the Redis ACL username (for example, `oid` for Microsoft Entra I…
     * @param list<string>|null                                                 $scopes             OAuth 2.0 scopes to request. Default: `[]`.
     * @param bool|null                                                         $sslVerify          Whether to verify the TLS certificate of the token endpoint. Default: `true`.
     * @param int|float|null                                                    $timeout            Timeout, in milliseconds, for requests to the token endpoint. Default: `10000`.
     * @param string|null                                                       $tokenEndpoint      OAuth 2.0 token endpoint URL used to request access tokens.
     * @param array<array-key, string>|null                                     $tokenHeaders       Additional HTTP headers to send with the token request.
     * @param array<array-key, string>|null                                     $tokenPostArgs      Additional POST body arguments to send with the token request.
     * @param string|null                                                       $username           Resource owner username, used with the `password` grant type.
     */
    public function __construct(
        public ?StorageConfigRedisCloudAuthenticationOauthAuthMethod $authMethod = null,
        public ?string $clientId = null,
        public ?string $clientSecret = null,
        public ?StorageConfigRedisCloudAuthenticationOauthClientSecretJwtAlg $clientSecretJwtAlg = null,
        public ?StorageConfigRedisCloudAuthenticationOauthGrantType $grantType = null,
        public ?string $password = null,
        public ?string $redisUsername = null,
        public ?string $redisUsernameClaim = null,
        public ?array $scopes = null,
        public ?bool $sslVerify = null,
        public int|float|null $timeout = null,
        public ?string $tokenEndpoint = null,
        public ?array $tokenHeaders = null,
        public ?array $tokenPostArgs = null,
        public ?string $username = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            authMethod: Data::enumOrNull($data, 'auth_method', StorageConfigRedisCloudAuthenticationOauthAuthMethod::class),
            clientId: Data::stringOrNull($data, 'client_id'),
            clientSecret: Data::stringOrNull($data, 'client_secret'),
            clientSecretJwtAlg: Data::enumOrNull($data, 'client_secret_jwt_alg', StorageConfigRedisCloudAuthenticationOauthClientSecretJwtAlg::class),
            grantType: Data::enumOrNull($data, 'grant_type', StorageConfigRedisCloudAuthenticationOauthGrantType::class),
            password: Data::stringOrNull($data, 'password'),
            redisUsername: Data::stringOrNull($data, 'redis_username'),
            redisUsernameClaim: Data::stringOrNull($data, 'redis_username_claim'),
            scopes: Data::stringListOrNull($data, 'scopes'),
            sslVerify: Data::boolOrNull($data, 'ssl_verify'),
            timeout: Data::numberOrNull($data, 'timeout'),
            tokenEndpoint: Data::stringOrNull($data, 'token_endpoint'),
            tokenHeaders: Data::stringMapOrNull($data, 'token_headers'),
            tokenPostArgs: Data::stringMapOrNull($data, 'token_post_args'),
            username: Data::stringOrNull($data, 'username'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'auth_method' => $this->authMethod?->value,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'client_secret_jwt_alg' => $this->clientSecretJwtAlg?->value,
            'grant_type' => $this->grantType?->value,
            'password' => $this->password,
            'redis_username' => $this->redisUsername,
            'redis_username_claim' => $this->redisUsernameClaim,
            'scopes' => $this->scopes,
            'ssl_verify' => $this->sslVerify,
            'timeout' => $this->timeout,
            'token_endpoint' => $this->tokenEndpoint,
            'token_headers' => $this->tokenHeaders,
            'token_post_args' => $this->tokenPostArgs,
            'username' => $this->username,
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

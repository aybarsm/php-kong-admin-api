<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `acme` plugin (ACME; doc `Security/acme.md`).
 *
 * Read it from a returned Plugin with `AcmeConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Security/acme.md', '#/properties/config')]
final readonly class AcmeConfig implements PluginConfig
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['accountEmail', 'eabHmacKey', 'eabKid'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'acme';

    /**
     * @param string                 $accountEmail         The account identifier.
     * @param AcmeAccountKey|null    $accountKey           The private key associated with the account.
     * @param bool|null              $allowAnyDomain       If set to `true`, the plugin allows all domains and ignores any values in the `domains` list. Default: `false`.
     * @param string|null            $apiUri               A string representing a URL, such as https://example.com/path/to/resource?q=search. Default: `https://acme-v02.api.letsencrypt.org/directory`.
     * @param AcmeCertType|null      $certType             The certificate type to create. Default: `rsa`.
     * @param list<string>|null      $domains              An array of strings representing hosts.
     * @param string|null            $eabHmacKey           External account binding (EAB) base64-encoded URL string of the HMAC key.
     * @param string|null            $eabKid               External account binding (EAB) key id.
     * @param bool|null              $enableIpv4CommonName A boolean value that controls whether to include the IPv4 address in the common name field of generated certi… Default: `true`.
     * @param float|null             $failBackoffMinutes   Minutes to wait for each domain that fails to create a certificate. Default: `5`.
     * @param string|null            $preferredChain       A string value that specifies the preferred certificate chain to use when generating certificates.
     * @param float|null             $renewThresholdDays   Days remaining to renew the certificate before it expires. Default: `14`.
     * @param AcmeRsaKeySize|null    $rsaKeySize           RSA private key size for the certificate. Default: `4096`.
     * @param AcmeStorage|null       $storage              The backend storage type to use. Default: `shm`.
     * @param AcmeStorageConfig|null $storageConfig
     * @param bool|null              $tosAccepted          If you are using Let's Encrypt, you must set this to `true` to agree the terms of service. Default: `false`.
     */
    public function __construct(
        public string $accountEmail,
        public ?AcmeAccountKey $accountKey = null,
        public ?bool $allowAnyDomain = null,
        public ?string $apiUri = null,
        public ?AcmeCertType $certType = null,
        public ?array $domains = null,
        public ?string $eabHmacKey = null,
        public ?string $eabKid = null,
        public ?bool $enableIpv4CommonName = null,
        public ?float $failBackoffMinutes = null,
        public ?string $preferredChain = null,
        public ?float $renewThresholdDays = null,
        public ?AcmeRsaKeySize $rsaKeySize = null,
        public ?AcmeStorage $storage = null,
        public ?AcmeStorageConfig $storageConfig = null,
        public ?bool $tosAccepted = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $accountKey = Data::mapOrNull($data, 'account_key');
        $storageConfig = Data::mapOrNull($data, 'storage_config');

        return new self(
            accountEmail: Data::string($data, 'account_email'),
            accountKey: $accountKey === null ? null : AcmeAccountKey::fromArray($accountKey),
            allowAnyDomain: Data::boolOrNull($data, 'allow_any_domain'),
            apiUri: Data::stringOrNull($data, 'api_uri'),
            certType: Data::enumOrNull($data, 'cert_type', AcmeCertType::class),
            domains: Data::stringListOrNull($data, 'domains'),
            eabHmacKey: Data::stringOrNull($data, 'eab_hmac_key'),
            eabKid: Data::stringOrNull($data, 'eab_kid'),
            enableIpv4CommonName: Data::boolOrNull($data, 'enable_ipv4_common_name'),
            failBackoffMinutes: Data::floatOrNull($data, 'fail_backoff_minutes'),
            preferredChain: Data::stringOrNull($data, 'preferred_chain'),
            renewThresholdDays: Data::floatOrNull($data, 'renew_threshold_days'),
            rsaKeySize: Data::enumOrNull($data, 'rsa_key_size', AcmeRsaKeySize::class),
            storage: Data::enumOrNull($data, 'storage', AcmeStorage::class),
            storageConfig: $storageConfig === null ? null : AcmeStorageConfig::fromArray($storageConfig),
            tosAccepted: Data::boolOrNull($data, 'tos_accepted'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'account_email' => $this->accountEmail,
            'account_key' => $this->accountKey?->toArray(),
            'allow_any_domain' => $this->allowAnyDomain,
            'api_uri' => $this->apiUri,
            'cert_type' => $this->certType?->value,
            'domains' => $this->domains,
            'eab_hmac_key' => $this->eabHmacKey,
            'eab_kid' => $this->eabKid,
            'enable_ipv4_common_name' => $this->enableIpv4CommonName,
            'fail_backoff_minutes' => $this->failBackoffMinutes,
            'preferred_chain' => $this->preferredChain,
            'renew_threshold_days' => $this->renewThresholdDays,
            'rsa_key_size' => $this->rsaKeySize?->value,
            'storage' => $this->storage?->value,
            'storage_config' => $this->storageConfig?->toArray(),
            'tos_accepted' => $this->tosAccepted,
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

    /**
     * Reads the typed configuration of a `acme` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `acme` plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    #[Override]
    public static function fromPlugin(Plugin $plugin): static
    {
        if ($plugin->name !== self::NAME) {
            throw new InvalidArgumentException(sprintf('Expected a "%s" plugin, got "%s".', self::NAME, $plugin->name));
        }

        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));
    }
}

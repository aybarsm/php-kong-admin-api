<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Typed `config` request body of a `acme` plugin (ACME; doc `Security/acme.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Security/acme.md', '#/properties/config')]
final readonly class AcmeConfigInput implements Input
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['accountEmail', 'eabHmacKey', 'eabKid'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'acme';

    /**
     * @param string|null        $accountEmail         The account identifier. Required by the plugin doc.
     * @param AccountKey|null    $accountKey           The private key associated with the account.
     * @param bool|null          $allowAnyDomain       If set to `true`, the plugin allows all domains and ignores any values in the `domains` list. Default: `false`.
     * @param string|null        $apiUri               A string representing a URL, such as https://example.com/path/to/resource?q=search. Default: `https://acme-v02.api.letsencrypt.org/directory`.
     * @param CertType|null      $certType             The certificate type to create. Default: `rsa`.
     * @param list<string>|null  $domains              An array of strings representing hosts.
     * @param string|null        $eabHmacKey           External account binding (EAB) base64-encoded URL string of the HMAC key.
     * @param string|null        $eabKid               External account binding (EAB) key id.
     * @param bool|null          $enableIpv4CommonName A boolean value that controls whether to include the IPv4 address in the common name field of generated certi… Default: `true`.
     * @param int|float|null     $failBackoffMinutes   Minutes to wait for each domain that fails to create a certificate. Default: `5`.
     * @param string|null        $preferredChain       A string value that specifies the preferred certificate chain to use when generating certificates.
     * @param int|float|null     $renewThresholdDays   Days remaining to renew the certificate before it expires. Default: `14`.
     * @param RsaKeySize|null    $rsaKeySize           RSA private key size for the certificate. Default: `4096`.
     * @param Storage|null       $storage              The backend storage type to use. Default: `shm`.
     * @param StorageConfig|null $storageConfig
     * @param bool|null          $tosAccepted          If you are using Let's Encrypt, you must set this to `true` to agree the terms of service. Default: `false`.
     */
    public function __construct(
        #[SensitiveParameter]
        public ?string $accountEmail = null,
        public ?AccountKey $accountKey = null,
        public ?bool $allowAnyDomain = null,
        public ?string $apiUri = null,
        public ?CertType $certType = null,
        public ?array $domains = null,
        #[SensitiveParameter]
        public ?string $eabHmacKey = null,
        #[SensitiveParameter]
        public ?string $eabKid = null,
        public ?bool $enableIpv4CommonName = null,
        public int|float|null $failBackoffMinutes = null,
        public ?string $preferredChain = null,
        public int|float|null $renewThresholdDays = null,
        public ?RsaKeySize $rsaKeySize = null,
        public ?Storage $storage = null,
        public ?StorageConfig $storageConfig = null,
        public ?bool $tosAccepted = null,
    ) {
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
}

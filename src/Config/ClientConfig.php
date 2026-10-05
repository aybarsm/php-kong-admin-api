<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Config;

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use SensitiveParameter;

/**
 * Connection settings for the Admin API.
 *
 * Defaults come from the spec's `servers` entry (`http://localhost:8001/`). Timeouts and TLS settings
 * (client certificate for mutual TLS, server verification) apply only to the default Guzzle client; an
 * injected PSR-18 client is configured by the caller.
 */
final readonly class ClientConfig
{
    /** Spec `servers[0]` defaults: protocol http, hostname localhost, port 8001, path `/`. */
    public const string DEFAULT_BASE_URI = 'http://localhost:8001/';

    /** Protocols allowed by the spec's `servers[0].variables.protocol` enum. */
    public const array ALLOWED_SCHEMES = ['http', 'https'];

    /**
     * @param string                $baseUri        absolute Admin API URI, optionally with a base path
     * @param string|null           $adminToken     sent as the `Kong-Admin-Token` header (spec security scheme `adminToken`)
     * @param string|null           $workspace      workspace prefixed as `/{workspace}` on operations the spec scopes to workspaces
     * @param float|null            $timeout        total request timeout in seconds (default client only)
     * @param float|null            $connectTimeout connection timeout in seconds (default client only)
     * @param array<string, string> $headers        extra headers sent with every request
     * @param string|null           $clientCert     PEM client certificate file presented for mutual TLS (default client only)
     * @param string|null           $clientKey      PEM private key file for $clientCert, unless the key is inside that file
     * @param string|null           $clientKeyPassphrase passphrase of the private key (in $clientKey or $clientCert); never dumped
     * @param bool|string           $verify         verify the server certificate: true (system CAs), a CA bundle file or
     *                                              directory, or false to disable verification (insecure; default client only)
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public string $baseUri = self::DEFAULT_BASE_URI,
        #[SensitiveParameter]
        public ?string $adminToken = null,
        public ?string $workspace = null,
        public ?float $timeout = null,
        public ?float $connectTimeout = null,
        public array $headers = [],
        public ?string $clientCert = null,
        public ?string $clientKey = null,
        #[SensitiveParameter]
        public ?string $clientKeyPassphrase = null,
        public bool|string $verify = true,
    ) {
        $parts = parse_url($baseUri);
        $scheme = is_array($parts) ? strtolower($parts['scheme'] ?? '') : '';
        if (!in_array($scheme, self::ALLOWED_SCHEMES, true) || !isset($parts['host']) || $parts['host'] === '') {
            throw new InvalidArgumentException('baseUri must be an absolute http or https URI.');
        }
        if (isset($parts['query']) || isset($parts['fragment'])) {
            throw new InvalidArgumentException('baseUri must not contain a query string or fragment.');
        }
        if ($adminToken === '') {
            throw new InvalidArgumentException('adminToken must not be empty; pass null to send no token.');
        }
        if ($workspace === '') {
            throw new InvalidArgumentException('workspace must not be empty; pass null for no workspace prefix.');
        }
        if (($timeout !== null && $timeout < 0) || ($connectTimeout !== null && $connectTimeout < 0)) {
            throw new InvalidArgumentException('Timeouts must be zero or greater.');
        }
        foreach ($headers as $name => $value) {
            if (preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name) !== 1) {
                throw new InvalidArgumentException(sprintf('Invalid header name "%s".', $name));
            }
        }
        if ($clientCert === null && ($clientKey !== null || $clientKeyPassphrase !== null)) {
            throw new InvalidArgumentException('clientKey and clientKeyPassphrase require clientCert.');
        }
        if ($clientKeyPassphrase === '') {
            throw new InvalidArgumentException('clientKeyPassphrase must not be empty; pass null for an unencrypted key.');
        }
        foreach (['clientCert' => $clientCert, 'clientKey' => $clientKey] as $option => $path) {
            if ($path !== null && !(is_file($path) && is_readable($path))) {
                throw new InvalidArgumentException(sprintf('%s must be a readable file: "%s".', $option, $path));
            }
        }
        if (is_string($verify) && !((is_file($verify) || is_dir($verify)) && is_readable($verify))) {
            throw new InvalidArgumentException(sprintf('verify must be a boolean or a readable CA bundle file or directory: "%s".', $verify));
        }
    }

    /**
     * A copy of this configuration scoped to another workspace (or none).
     *
     * @throws InvalidArgumentException
     */
    public function withWorkspace(?string $workspace): self
    {
        return new self(
            baseUri: $this->baseUri,
            adminToken: $this->adminToken,
            workspace: $workspace,
            timeout: $this->timeout,
            connectTimeout: $this->connectTimeout,
            headers: $this->headers,
            clientCert: $this->clientCert,
            clientKey: $this->clientKey,
            clientKeyPassphrase: $this->clientKeyPassphrase,
            verify: $this->verify,
        );
    }

    /**
     * Never expose the admin token, header values or the key passphrase in dumps.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'baseUri' => $this->baseUri,
            'adminToken' => $this->adminToken === null ? null : '***',
            'workspace' => $this->workspace,
            'timeout' => $this->timeout,
            'connectTimeout' => $this->connectTimeout,
            'headers' => array_keys($this->headers),
            'clientCert' => $this->clientCert,
            'clientKey' => $this->clientKey,
            'clientKeyPassphrase' => $this->clientKeyPassphrase === null ? null : '***',
            'verify' => $this->verify,
        ];
    }
}

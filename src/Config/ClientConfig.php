<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Config;

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use SensitiveParameter;

/**
 * Connection settings for the Admin API.
 *
 * Defaults come from the spec's `servers` entry (`http://localhost:8001/`). Timeouts apply only to
 * the default Guzzle client; an injected PSR-18 client is configured by the caller.
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
    }

    /**
     * A copy of this configuration scoped to another workspace (or none).
     *
     * @throws InvalidArgumentException
     */
    public function withWorkspace(?string $workspace): self
    {
        return new self(
            $this->baseUri,
            $this->adminToken,
            $workspace,
            $this->timeout,
            $this->connectTimeout,
            $this->headers,
        );
    }

    /**
     * Never expose the admin token in dumps.
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
        ];
    }
}

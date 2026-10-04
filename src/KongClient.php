<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi;

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Resources\Acls;
use Aybarsm\Kong\AdminApi\Resources\BasicAuths;
use Aybarsm\Kong\AdminApi\Resources\CaCertificates;
use Aybarsm\Kong\AdminApi\Resources\Certificates;
use Aybarsm\Kong\AdminApi\Resources\ConsumerGroups;
use Aybarsm\Kong\AdminApi\Resources\Consumers;
use Aybarsm\Kong\AdminApi\Resources\HmacAuths;
use Aybarsm\Kong\AdminApi\Resources\Jwts;
use Aybarsm\Kong\AdminApi\Resources\KeyAuths;
use Aybarsm\Kong\AdminApi\Resources\Keys;
use Aybarsm\Kong\AdminApi\Resources\KeySets;
use Aybarsm\Kong\AdminApi\Resources\MtlsAuths;
use Aybarsm\Kong\AdminApi\Resources\Plugins;
use Aybarsm\Kong\AdminApi\Resources\Routes;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Resources\Snis;
use Aybarsm\Kong\AdminApi\Resources\Tags;
use Aybarsm\Kong\AdminApi\Resources\Upstreams;
use Aybarsm\Kong\AdminApi\Resources\Vaults;
use Aybarsm\Kong\AdminApi\Resources\Workspaces;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\RequestOptions;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Entry point: a factory for Admin API resources. It never calls the API itself.
 *
 * ```php
 * $kong = new KongClient(new ClientConfig('http://localhost:8001/', adminToken: $token));
 * $service = $kong->services()->get('my-service');
 * $plugins = $kong->inWorkspace('team-a')->plugins()->list();
 * ```
 */
final readonly class KongClient
{
    private ClientInterface $httpClient;

    private RequestFactoryInterface&StreamFactoryInterface $httpFactory;

    private Transport $transport;

    /**
     * @param ClientConfig                                         $config      connection settings
     * @param ClientInterface|null                                 $httpClient  any PSR-18 client; defaults to Guzzle built from $config
     * @param (RequestFactoryInterface&StreamFactoryInterface)|null $httpFactory PSR-17 request and stream factory; defaults to Guzzle's HttpFactory
     */
    public function __construct(
        private ClientConfig $config = new ClientConfig(),
        ?ClientInterface $httpClient = null,
        (RequestFactoryInterface&StreamFactoryInterface)|null $httpFactory = null,
    ) {
        $this->httpClient = $httpClient ?? self::defaultHttpClient($config);
        $this->httpFactory = $httpFactory ?? new HttpFactory();
        $this->transport = new Transport($this->httpClient, $this->httpFactory, $config);
    }

    /**
     * The active configuration.
     */
    public function config(): ClientConfig
    {
        return $this->config;
    }

    /**
     * A client whose workspace-scoped operations are sent under `/{workspace}`.
     *
     * @throws InvalidArgumentException when the workspace name is empty
     */
    public function inWorkspace(string $workspace): self
    {
        return new self($this->config->withWorkspace($workspace), $this->httpClient, $this->httpFactory);
    }

    /**
     * A client without a workspace prefix.
     */
    public function withoutWorkspace(): self
    {
        return new self($this->config->withWorkspace(null), $this->httpClient, $this->httpFactory);
    }

    /**
     * CaCertificates: `/ca_certificates`.
     */
    public function caCertificates(): CaCertificates
    {
        return new CaCertificates($this->transport);
    }

    /**
     * Certificates: `/certificates`.
     */
    public function certificates(): Certificates
    {
        return new Certificates($this->transport);
    }

    /**
     * Consumers: `/consumers`.
     */
    public function consumers(): Consumers
    {
        return new Consumers($this->transport);
    }

    /**
     * KeySets: `/key-sets`.
     */
    public function keySets(): KeySets
    {
        return new KeySets($this->transport);
    }

    /**
     * Keys: `/keys`.
     */
    public function keys(): Keys
    {
        return new Keys($this->transport);
    }

    /**
     * Plugins: `/plugins`.
     */
    public function plugins(): Plugins
    {
        return new Plugins($this->transport);
    }

    /**
     * SNIs: `/snis`.
     */
    public function snis(): Snis
    {
        return new Snis($this->transport);
    }

    /**
     * Tags: `/tags`.
     */
    public function tags(): Tags
    {
        return new Tags($this->transport);
    }

    /**
     * Upstreams: `/upstreams`.
     */
    public function upstreams(): Upstreams
    {
        return new Upstreams($this->transport);
    }

    /**
     * Vaults: `/vaults`.
     */
    public function vaults(): Vaults
    {
        return new Vaults($this->transport);
    }

    /**
     * Workspaces: `/workspaces`.
     */
    public function workspaces(): Workspaces
    {
        return new Workspaces($this->transport);
    }

    /**
     * ACLs: `/acls`.
     */
    public function acls(): Acls
    {
        return new Acls($this->transport);
    }

    /**
     * Basic-auth credentials: `/basic-auths`.
     */
    public function basicAuths(): BasicAuths
    {
        return new BasicAuths($this->transport);
    }

    /**
     * Consumer Groups: `/consumer_groups`.
     */
    public function consumerGroups(): ConsumerGroups
    {
        return new ConsumerGroups($this->transport);
    }

    /**
     * HMAC-auth credentials: `/hmac-auths`.
     */
    public function hmacAuths(): HmacAuths
    {
        return new HmacAuths($this->transport);
    }

    /**
     * JWTs: `/jwts`.
     */
    public function jwts(): Jwts
    {
        return new Jwts($this->transport);
    }

    /**
     * API keys (key-auth credentials): `/key-auths`.
     */
    public function keyAuths(): KeyAuths
    {
        return new KeyAuths($this->transport);
    }

    /**
     * MTLS-auth credentials: `/mtls-auths`.
     */
    public function mtlsAuths(): MtlsAuths
    {
        return new MtlsAuths($this->transport);
    }

    /**
     * Services: `/services`.
     */
    public function services(): Services
    {
        return new Services($this->transport);
    }

    /**
     * Routes: `/routes`.
     */
    public function routes(): Routes
    {
        return new Routes($this->transport);
    }

    private static function defaultHttpClient(ClientConfig $config): ClientInterface
    {
        $options = [RequestOptions::HTTP_ERRORS => false];
        if ($config->timeout !== null) {
            $options[RequestOptions::TIMEOUT] = $config->timeout;
        }
        if ($config->connectTimeout !== null) {
            $options[RequestOptions::CONNECT_TIMEOUT] = $config->connectTimeout;
        }

        return new GuzzleClient($options);
    }
}

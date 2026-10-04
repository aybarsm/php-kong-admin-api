<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi;

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Resources\Routes;
use Aybarsm\Kong\AdminApi\Resources\Services;
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

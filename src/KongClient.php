<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi;

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Resources\Acls;
use Aybarsm\Kong\AdminApi\Resources\Admins;
use Aybarsm\Kong\AdminApi\Resources\AuditLogs;
use Aybarsm\Kong\AdminApi\Resources\BasicAuths;
use Aybarsm\Kong\AdminApi\Resources\CaCertificates;
use Aybarsm\Kong\AdminApi\Resources\Cache;
use Aybarsm\Kong\AdminApi\Resources\Certificates;
use Aybarsm\Kong\AdminApi\Resources\ClonedPlugins;
use Aybarsm\Kong\AdminApi\Resources\Clustering;
use Aybarsm\Kong\AdminApi\Resources\ConsumerGroups;
use Aybarsm\Kong\AdminApi\Resources\Consumers;
use Aybarsm\Kong\AdminApi\Resources\CustomPlugins;
use Aybarsm\Kong\AdminApi\Resources\Debug;
use Aybarsm\Kong\AdminApi\Resources\DeclarativeConfig;
use Aybarsm\Kong\AdminApi\Resources\DegraphqlRoutes;
use Aybarsm\Kong\AdminApi\Resources\EventHooks;
use Aybarsm\Kong\AdminApi\Resources\GraphqlCostDecorations;
use Aybarsm\Kong\AdminApi\Resources\GroupRbacRoles;
use Aybarsm\Kong\AdminApi\Resources\Groups;
use Aybarsm\Kong\AdminApi\Resources\HmacAuths;
use Aybarsm\Kong\AdminApi\Resources\Information;
use Aybarsm\Kong\AdminApi\Resources\Jwts;
use Aybarsm\Kong\AdminApi\Resources\KeyAuths;
use Aybarsm\Kong\AdminApi\Resources\Keyring;
use Aybarsm\Kong\AdminApi\Resources\Keys;
use Aybarsm\Kong\AdminApi\Resources\KeySets;
use Aybarsm\Kong\AdminApi\Resources\Licenses;
use Aybarsm\Kong\AdminApi\Resources\MtlsAuths;
use Aybarsm\Kong\AdminApi\Resources\OidcJwks;
use Aybarsm\Kong\AdminApi\Resources\Partials;
use Aybarsm\Kong\AdminApi\Resources\Plugins;
use Aybarsm\Kong\AdminApi\Resources\RbacRoleEndpoints;
use Aybarsm\Kong\AdminApi\Resources\RbacRoleEntities;
use Aybarsm\Kong\AdminApi\Resources\RbacRoles;
use Aybarsm\Kong\AdminApi\Resources\RbacUserGroups;
use Aybarsm\Kong\AdminApi\Resources\RbacUserRoles;
use Aybarsm\Kong\AdminApi\Resources\RbacUsers;
use Aybarsm\Kong\AdminApi\Resources\Routes;
use Aybarsm\Kong\AdminApi\Resources\Schemas;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Resources\Snis;
use Aybarsm\Kong\AdminApi\Resources\Tags;
use Aybarsm\Kong\AdminApi\Resources\Upstreams;
use Aybarsm\Kong\AdminApi\Resources\Vaults;
use Aybarsm\Kong\AdminApi\Resources\WorkspaceGroups;
use Aybarsm\Kong\AdminApi\Resources\WorkspaceRbacRoles;
use Aybarsm\Kong\AdminApi\Resources\WorkspaceRbacUsers;
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
     * Admins: `/admins`.
     */
    public function admins(): Admins
    {
        return new Admins($this->transport);
    }

    /**
     * Cloned Plugins: `/cloned-plugins`.
     */
    public function clonedPlugins(): ClonedPlugins
    {
        return new ClonedPlugins($this->transport);
    }

    /**
     * Custom Plugins: `/custom-plugins`.
     */
    public function customPlugins(): CustomPlugins
    {
        return new CustomPlugins($this->transport);
    }

    /**
     * DeGraphQL routes: `/degraphql_routes`.
     */
    public function degraphqlRoutes(): DegraphqlRoutes
    {
        return new DegraphqlRoutes($this->transport);
    }

    /**
     * Event hooks: `/event-hooks`.
     */
    public function eventHooks(): EventHooks
    {
        return new EventHooks($this->transport);
    }

    /**
     * GraphQL cost decorations: `/graphql-rate-limiting-advanced/costs`.
     */
    public function graphqlCostDecorations(): GraphqlCostDecorations
    {
        return new GraphqlCostDecorations($this->transport);
    }

    /**
     * Group RBAC roles: `/group_rbac_roles`.
     */
    public function groupRbacRoles(): GroupRbacRoles
    {
        return new GroupRbacRoles($this->transport);
    }

    /**
     * Groups: `/groups`.
     */
    public function groups(): Groups
    {
        return new Groups($this->transport);
    }

    /**
     * Licenses: `/licenses`.
     */
    public function licenses(): Licenses
    {
        return new Licenses($this->transport);
    }

    /**
     * OpenID Connect JWK sets: `/oic_jwks`.
     */
    public function oidcJwks(): OidcJwks
    {
        return new OidcJwks($this->transport);
    }

    /**
     * Partials: `/partials`.
     */
    public function partials(): Partials
    {
        return new Partials($this->transport);
    }

    /**
     * RBAC role endpoints (global): `/rbac_role_endpoints`.
     */
    public function rbacRoleEndpoints(): RbacRoleEndpoints
    {
        return new RbacRoleEndpoints($this->transport);
    }

    /**
     * RBAC role entities (global): `/rbac_role_entities`.
     */
    public function rbacRoleEntities(): RbacRoleEntities
    {
        return new RbacRoleEntities($this->transport);
    }

    /**
     * RBAC roles (global): `/rbac_roles`.
     */
    public function rbacRoles(): RbacRoles
    {
        return new RbacRoles($this->transport);
    }

    /**
     * RBAC user groups (global): `/rbac_user_groups`.
     */
    public function rbacUserGroups(): RbacUserGroups
    {
        return new RbacUserGroups($this->transport);
    }

    /**
     * RBAC user roles (global): `/rbac_user_roles`.
     */
    public function rbacUserRoles(): RbacUserRoles
    {
        return new RbacUserRoles($this->transport);
    }

    /**
     * RBAC users (global): `/rbac_users`.
     */
    public function rbacUsers(): RbacUsers
    {
        return new RbacUsers($this->transport);
    }

    /**
     * Workspace groups: `/workspace_/groups`.
     */
    public function workspaceGroups(): WorkspaceGroups
    {
        return new WorkspaceGroups($this->transport);
    }

    /**
     * RBAC roles of the current workspace: `/{workspace}/rbac/roles`.
     */
    public function workspaceRbacRoles(): WorkspaceRbacRoles
    {
        return new WorkspaceRbacRoles($this->transport);
    }

    /**
     * RBAC users of the current workspace: `/{workspace}/rbac/users`.
     */
    public function workspaceRbacUsers(): WorkspaceRbacUsers
    {
        return new WorkspaceRbacUsers($this->transport);
    }

    /**
     * Audit logs: `/audit/{objects,requests}`.
     */
    public function auditLogs(): AuditLogs
    {
        return new AuditLogs($this->transport);
    }

    /**
     * The node cache: `/cache`.
     */
    public function cache(): Cache
    {
        return new Cache($this->transport);
    }

    /**
     * Hybrid-mode clustering: `/clustering/{data-planes,status}`.
     */
    public function clustering(): Clustering
    {
        return new Clustering($this->transport);
    }

    /**
     * Log levels: `/debug/…/log-level`.
     */
    public function debug(): Debug
    {
        return new Debug($this->transport);
    }

    /**
     * Declarative configuration: `/config`.
     */
    public function declarativeConfig(): DeclarativeConfig
    {
        return new DeclarativeConfig($this->transport);
    }

    /**
     * Node information: `/, /status, /endpoints, …`.
     */
    public function information(): Information
    {
        return new Information($this->transport);
    }

    /**
     * The keyring: `/keyring`.
     */
    public function keyring(): Keyring
    {
        return new Keyring($this->transport);
    }

    /**
     * Entity, Partial and plugin schemas: `/schemas`.
     */
    public function schemas(): Schemas
    {
        return new Schemas($this->transport);
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

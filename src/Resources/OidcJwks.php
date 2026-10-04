<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\OidcJwk;
use Aybarsm\Kong\AdminApi\Models\OidcJwkInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * OIDC JWK sets (spec tag "OIDC JWKs"): `/oic_jwks` and `/oic_jwks/{OidcJwkId}`.
 */
final readonly class OidcJwks extends AbstractResource
{
    private const string SEGMENT = 'oic_jwks';

    /**
     * List one page of OIDC JWK sets (operationId `list-oic_jwk`).
     *
     * @return Page<OidcJwk>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/oic_jwks', 'list-oic_jwk', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), OidcJwk::fromArray(...), $options);
    }

    /**
     * Lazily iterate every OIDC JWK set across all pages (operationId `list-oic_jwk`).
     *
     * @return Generator<int, OidcJwk>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/oic_jwks', 'list-oic_jwk', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), OidcJwk::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-oic_jwk`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<OidcJwk> $page
     *
     * @return Page<OidcJwk>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/oic_jwks', 'list-oic_jwk', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an OIDC JWK set by ID (operationId `get-oic_jwk`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/oic_jwks/{OidcJwkId}', 'get-oic_jwk', OperationScope::Both)]
    public function get(string $id): OidcJwk
    {
        return OidcJwk::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an OIDC JWK set (operationId `create-oic_jwk`, body `OidcJwk`).
     *
     * @param OidcJwkInput|array<string, mixed> $oidcJwk
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/oic_jwks', 'create-oic_jwk', OperationScope::Both)]
    public function create(OidcJwkInput|array $oidcJwk): OidcJwk
    {
        return OidcJwk::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $oidcJwk,
        ));
    }

    /**
     * Update fields of an OIDC JWK set (operationId `update-oic_jwk`, PATCH, body `OidcJwk`).
     *
     * @param OidcJwkInput|array<string, mixed> $oidcJwk only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/oic_jwks/{OidcJwkId}', 'update-oic_jwk', OperationScope::Both)]
    public function update(string $id, OidcJwkInput|array $oidcJwk): OidcJwk
    {
        return OidcJwk::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $oidcJwk,
        ));
    }

    /**
     * Create or replace an OIDC JWK set by ID (operationId `upsert-oic_jwk`, PUT, body `OidcJwk`).
     *
     * @param OidcJwkInput|array<string, mixed> $oidcJwk
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/oic_jwks/{OidcJwkId}', 'upsert-oic_jwk', OperationScope::Both)]
    public function upsert(string $id, OidcJwkInput|array $oidcJwk): OidcJwk
    {
        return OidcJwk::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $oidcJwk,
        ));
    }

    /**
     * Delete an OIDC JWK set (operationId `delete-oic_jwk`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/oic_jwks/{OidcJwkId}', 'delete-oic_jwk', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}

<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\ConflictException;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Admin;
use Aybarsm\Kong\AdminApi\Models\AdminCreationInput;
use Aybarsm\Kong\AdminApi\Models\AdminInput;
use Aybarsm\Kong\AdminApi\Models\AdminPasswordResetInput;
use Aybarsm\Kong\AdminApi\Models\AdminPasswordResetRequestInput;
use Aybarsm\Kong\AdminApi\Models\AdminRegistrationInput;
use Aybarsm\Kong\AdminApi\Models\AdminRoles;
use Aybarsm\Kong\AdminApi\Models\AdminRolesInput;
use Aybarsm\Kong\AdminApi\Models\AdminSummary;
use Aybarsm\Kong\AdminApi\Models\Workspace;
use Aybarsm\Kong\AdminApi\Pagination\Page;

/**
 * Admins (spec tag "Admins"): `/admins`, `/admins/{AdminId}`, `/admins/{adminNameOrId}/…`,
 * `/admins/register` and `/admins/password_resets`. All paths are global (no `/{workspace}` twin).
 */
final readonly class Admins extends AbstractResource
{
    private const string SEGMENT = 'admins';

    /**
     * List Admins (operationId `get-admins`). The spec declares no pagination parameters and returns only
     * `next`, so there is no `all()` walker (spec-notes Q7).
     *
     * @return Page<AdminSummary>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/admins', 'get-admins', OperationScope::GlobalOnly)]
    public function list(): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), AdminSummary::fromArray(...));
    }

    /**
     * Create an Admin (operationId `create-admins`, body `AdminCreationRequest`). The spec defines no response
     * body (spec-notes Q15).
     *
     * @param AdminCreationInput|array<string, mixed> $admin
     *
     * @throws ConflictException when the Admin already exists (HTTP 409)
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/admins', 'create-admins', OperationScope::GlobalOnly)]
    public function create(AdminCreationInput|array $admin): void
    {
        $this->none(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT), body: $admin);
    }

    /**
     * Get an Admin by ID (operationId `get-admin`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/admins/{AdminId}', 'get-admin', OperationScope::GlobalOnly)]
    public function get(string $id): Admin
    {
        return Admin::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id)));
    }

    /**
     * Update fields of an Admin (operationId `update-admin`, PATCH, body `Admin`).
     *
     * @param AdminInput|array<string, mixed> $admin only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/admins/{AdminId}', 'update-admin', OperationScope::GlobalOnly)]
    public function update(string $id, AdminInput|array $admin): Admin
    {
        return Admin::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $admin,
        ));
    }

    /**
     * Create or replace an Admin by ID (operationId `upsert-admin`, PUT, body `Admin`).
     *
     * @param AdminInput|array<string, mixed> $admin
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/admins/{AdminId}', 'upsert-admin', OperationScope::GlobalOnly)]
    public function upsert(string $id, AdminInput|array $admin): Admin
    {
        return Admin::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $admin,
        ));
    }

    /**
     * Delete an Admin (operationId `delete-admin`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/admins/{AdminId}', 'delete-admin', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }

    /**
     * Register an Admin's credentials (operationId `create-admins-credentials`, body
     * `AdminCredentialRegistrationRequest`). The spec defines no response body (spec-notes Q15).
     *
     * @param AdminRegistrationInput|array<string, mixed> $registration
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/admins/register', 'create-admins-credentials', OperationScope::GlobalOnly)]
    public function register(AdminRegistrationInput|array $registration): void
    {
        $this->none(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'register'), body: $registration);
    }

    /**
     * Request a password-reset email (operationId `get-admins-password-resets`, POST, body
     * `AdminPasswordResetRequest`). The spec defines no response body (spec-notes Q15).
     *
     * @param AdminPasswordResetRequestInput|array<string, mixed> $request
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/admins/password_resets', 'get-admins-password-resets', OperationScope::GlobalOnly)]
    public function requestPasswordReset(AdminPasswordResetRequestInput|array $request): void
    {
        $this->none(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'password_resets'), body: $request);
    }

    /**
     * Reset a password with a reset token (operationId `update-admins-password-resets`, PATCH, body
     * `AdminPasswordResetConfirmationRequest`). The spec defines no response body (spec-notes Q15).
     *
     * @param AdminPasswordResetInput|array<string, mixed> $reset
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_PATCH, '/admins/password_resets', 'update-admins-password-resets', OperationScope::GlobalOnly)]
    public function resetPassword(AdminPasswordResetInput|array $reset): void
    {
        $this->none(Transport::METHOD_PATCH, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'password_resets'), body: $reset);
    }

    /**
     * Get an Admin's roles (operationId `get-admins-name_or_id-roles`). The spec declares the 200 response
     * without a schema, so the decoded JSON object is returned as is (spec-notes Q15).
     *
     * @return array<string, mixed>
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $nameOrId is empty
     */
    #[Operation(Transport::METHOD_GET, '/admins/{adminNameOrId}/roles', 'get-admins-name_or_id-roles', OperationScope::GlobalOnly)]
    public function roles(string $nameOrId): array
    {
        return $this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $nameOrId, 'roles'));
    }

    /**
     * Assign roles to an Admin (operationId `create-admins-name_or_id-roles`, body `AdminRoleUpdateRequest`).
     *
     * @param AdminRolesInput|array<string, mixed> $roles
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $nameOrId is empty
     */
    #[Operation(Transport::METHOD_POST, '/admins/{adminNameOrId}/roles', 'create-admins-name_or_id-roles', OperationScope::GlobalOnly)]
    public function addRoles(string $nameOrId, AdminRolesInput|array $roles): AdminRoles
    {
        return AdminRoles::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $nameOrId, 'roles'),
            body: $roles,
        ));
    }

    /**
     * Remove an Admin's roles (operationId `delete-admins-name_or_id-roles`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $nameOrId is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/admins/{adminNameOrId}/roles', 'delete-admins-name_or_id-roles', OperationScope::GlobalOnly)]
    public function removeRoles(string $nameOrId): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $nameOrId, 'roles'));
    }

    /**
     * The workspaces of an Admin (operationId `get-admins-name_or_id-workspaces`). The spec's response is a
     * single `Workspace` (`ListWorkspaceResponse`), implemented literally (spec-notes Q8).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $nameOrId is empty
     */
    #[Operation(Transport::METHOD_GET, '/admins/{adminNameOrId}/workspaces', 'get-admins-name_or_id-workspaces', OperationScope::GlobalOnly)]
    public function workspaces(string $nameOrId): Workspace
    {
        return Workspace::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $nameOrId, 'workspaces'),
        ));
    }

    /**
     * Update an Admin within one workspace (operationId
     * `update-admins-name_or_id-workspaces-workspace_name_or_id`, PATCH). The spec defines no request body.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when an argument is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/admins/{adminNameOrId}/workspaces/{workspaceNameOrId}', 'update-admins-name_or_id-workspaces-workspace_name_or_id', OperationScope::GlobalOnly)]
    public function updateWorkspace(string $nameOrId, string $workspaceNameOrId): AdminSummary
    {
        return AdminSummary::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $nameOrId, 'workspaces', $workspaceNameOrId),
        ));
    }
}

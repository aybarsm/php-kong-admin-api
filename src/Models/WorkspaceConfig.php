<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config` object of Workspace (spec `Workspace.config`).
 */
#[Schema('#/components/schemas/Workspace/properties/config')]
final readonly class WorkspaceConfig implements Model
{
    /**
     * @param array<array-key, string>|null $meta
     * @param bool|null                     $portal
     * @param bool|null                     $portalAccessRequestEmail
     * @param bool|null                     $portalApplicationRequestEmail
     * @param bool|null                     $portalApplicationStatusEmail
     * @param bool|null                     $portalApprovedEmail
     * @param string|null                   $portalAuth
     * @param string|null                   $portalAuthConf
     * @param bool|null                     $portalAutoApprove
     * @param list<string>|null             $portalCorsOrigins
     * @param string|null                   $portalDeveloperMetaFields
     * @param string|null                   $portalEmailsFrom
     * @param string|null                   $portalEmailsReplyTo
     * @param bool|null                     $portalInviteEmail
     * @param bool|null                     $portalIsLegacy
     * @param bool|null                     $portalResetEmail
     * @param bool|null                     $portalResetSuccessEmail
     * @param string|null                   $portalSessionConf
     * @param list<string>|null             $portalSmtpAdminEmails
     * @param int|null                      $portalTokenExp
     */
    public function __construct(
        public ?array $meta = null,
        public ?bool $portal = null,
        public ?bool $portalAccessRequestEmail = null,
        public ?bool $portalApplicationRequestEmail = null,
        public ?bool $portalApplicationStatusEmail = null,
        public ?bool $portalApprovedEmail = null,
        public ?string $portalAuth = null,
        public ?string $portalAuthConf = null,
        public ?bool $portalAutoApprove = null,
        public ?array $portalCorsOrigins = null,
        public ?string $portalDeveloperMetaFields = null,
        public ?string $portalEmailsFrom = null,
        public ?string $portalEmailsReplyTo = null,
        public ?bool $portalInviteEmail = null,
        public ?bool $portalIsLegacy = null,
        public ?bool $portalResetEmail = null,
        public ?bool $portalResetSuccessEmail = null,
        public ?string $portalSessionConf = null,
        public ?array $portalSmtpAdminEmails = null,
        public ?int $portalTokenExp = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            meta: Data::stringMapOrNull($data, 'meta'),
            portal: Data::boolOrNull($data, 'portal'),
            portalAccessRequestEmail: Data::boolOrNull($data, 'portal_access_request_email'),
            portalApplicationRequestEmail: Data::boolOrNull($data, 'portal_application_request_email'),
            portalApplicationStatusEmail: Data::boolOrNull($data, 'portal_application_status_email'),
            portalApprovedEmail: Data::boolOrNull($data, 'portal_approved_email'),
            portalAuth: Data::stringOrNull($data, 'portal_auth'),
            portalAuthConf: Data::stringOrNull($data, 'portal_auth_conf'),
            portalAutoApprove: Data::boolOrNull($data, 'portal_auto_approve'),
            portalCorsOrigins: Data::stringListOrNull($data, 'portal_cors_origins'),
            portalDeveloperMetaFields: Data::stringOrNull($data, 'portal_developer_meta_fields'),
            portalEmailsFrom: Data::stringOrNull($data, 'portal_emails_from'),
            portalEmailsReplyTo: Data::stringOrNull($data, 'portal_emails_reply_to'),
            portalInviteEmail: Data::boolOrNull($data, 'portal_invite_email'),
            portalIsLegacy: Data::boolOrNull($data, 'portal_is_legacy'),
            portalResetEmail: Data::boolOrNull($data, 'portal_reset_email'),
            portalResetSuccessEmail: Data::boolOrNull($data, 'portal_reset_success_email'),
            portalSessionConf: Data::stringOrNull($data, 'portal_session_conf'),
            portalSmtpAdminEmails: Data::stringListOrNull($data, 'portal_smtp_admin_emails'),
            portalTokenExp: Data::intOrNull($data, 'portal_token_exp'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'meta' => $this->meta,
            'portal' => $this->portal,
            'portal_access_request_email' => $this->portalAccessRequestEmail,
            'portal_application_request_email' => $this->portalApplicationRequestEmail,
            'portal_application_status_email' => $this->portalApplicationStatusEmail,
            'portal_approved_email' => $this->portalApprovedEmail,
            'portal_auth' => $this->portalAuth,
            'portal_auth_conf' => $this->portalAuthConf,
            'portal_auto_approve' => $this->portalAutoApprove,
            'portal_cors_origins' => $this->portalCorsOrigins,
            'portal_developer_meta_fields' => $this->portalDeveloperMetaFields,
            'portal_emails_from' => $this->portalEmailsFrom,
            'portal_emails_reply_to' => $this->portalEmailsReplyTo,
            'portal_invite_email' => $this->portalInviteEmail,
            'portal_is_legacy' => $this->portalIsLegacy,
            'portal_reset_email' => $this->portalResetEmail,
            'portal_reset_success_email' => $this->portalResetSuccessEmail,
            'portal_session_conf' => $this->portalSessionConf,
            'portal_smtp_admin_emails' => $this->portalSmtpAdminEmails,
            'portal_token_exp' => $this->portalTokenExp,
        ]);
    }
}

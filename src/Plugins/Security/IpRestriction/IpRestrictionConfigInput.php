<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\IpRestriction;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `ip-restriction` plugin (IP Restriction; doc `Security/ip-restriction.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Security/ip-restriction.md', '#/properties/config')]
final readonly class IpRestrictionConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ip-restriction';

    /**
     * @param list<string>|null $allow   List of IPs or CIDR ranges to allow.
     * @param list<string>|null $deny    List of IPs or CIDR ranges to deny.
     * @param string|null       $message The message to send as a response body to rejected requests.
     * @param float|null        $status  The HTTP status of the requests that will be rejected by the plugin.
     */
    public function __construct(
        public ?array $allow = null,
        public ?array $deny = null,
        public ?string $message = null,
        public ?float $status = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow' => $this->allow,
            'deny' => $this->deny,
            'message' => $this->message,
            'status' => $this->status,
        ]);
    }
}

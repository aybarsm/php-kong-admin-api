<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\AuditObject;
use Aybarsm\Kong\AdminApi\Models\AuditRequest;
use DateTimeInterface;

/**
 * Audit logs (spec tag "Audit Logs"): `/audit/objects` and `/audit/requests`. Global paths.
 *
 * Both responses are bare JSON arrays. `before`/`after` are spec `date-time` query parameters, sent in
 * RFC 3339 format.
 */
final readonly class AuditLogs extends AbstractResource
{
    /**
     * Database audit records (operationId `get-audit-objects`).
     *
     * @return list<AuditObject>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/audit/objects', 'get-audit-objects', OperationScope::GlobalOnly)]
    public function objects(?DateTimeInterface $before = null, ?DateTimeInterface $after = null): array
    {
        return array_map(
            AuditObject::fromArray(...),
            $this->items(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'audit', 'objects'), self::window($before, $after)),
        );
    }

    /**
     * Request audit records (operationId `get-audit-requests`).
     *
     * @return list<AuditRequest>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/audit/requests', 'get-audit-requests', OperationScope::GlobalOnly)]
    public function requests(?DateTimeInterface $before = null, ?DateTimeInterface $after = null): array
    {
        return array_map(
            AuditRequest::fromArray(...),
            $this->items(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'audit', 'requests'), self::window($before, $after)),
        );
    }

    /**
     * @return array<string, string|null>
     */
    private static function window(?DateTimeInterface $before, ?DateTimeInterface $after): array
    {
        return [
            'before' => $before?->format(DateTimeInterface::RFC3339),
            'after' => $after?->format(DateTimeInterface::RFC3339),
        ];
    }
}

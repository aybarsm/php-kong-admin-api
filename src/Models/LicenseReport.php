<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The license report of `GET /license/report` (spec response `ReportResponse`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema')]
final readonly class LicenseReport implements Model
{
    /**
     * @param string|null                    $checksum        A checksum of the report contents.
     * @param int|null                       $consumersCount  Total number of consumers configured in this deployment.
     * @param LicenseReportCounters|null     $counters        Request counts across all time periods.
     * @param string|null                    $dbVersion       The database engine and version in use.
     * @param LicenseReportDeployment|null   $deploymentInfo  Information about the deployment topology.
     * @param string|null                    $kongVersion     The version of Kong Gateway running on this node.
     * @param LicenseReportLicense|null      $license         Details about the active license.
     * @param LicenseReportPluginsCount|null $pluginsCount    Breakdown of active plugins by tier.
     * @param int|null                       $rbacUsers       Total number of RBAC users configured.
     * @param int|null                       $routesCount     Total number of routes configured in this deployment.
     * @param int|null                       $servicesCount   Total number of services configured in this deployment.
     * @param LicenseReportSystemInfo|null   $systemInfo      Information about the node generating the report.
     * @param int|null                       $timestamp       Unix timestamp of when the report was generated.
     * @param int|null                       $workspacesCount Total number of workspaces in this deployment.
     */
    public function __construct(
        public ?string $checksum = null,
        public ?int $consumersCount = null,
        public ?LicenseReportCounters $counters = null,
        public ?string $dbVersion = null,
        public ?LicenseReportDeployment $deploymentInfo = null,
        public ?string $kongVersion = null,
        public ?LicenseReportLicense $license = null,
        public ?LicenseReportPluginsCount $pluginsCount = null,
        public ?int $rbacUsers = null,
        public ?int $routesCount = null,
        public ?int $servicesCount = null,
        public ?LicenseReportSystemInfo $systemInfo = null,
        public ?int $timestamp = null,
        public ?int $workspacesCount = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $counters = Data::mapOrNull($data, 'counters');
        $deploymentInfo = Data::mapOrNull($data, 'deployment_info');
        $license = Data::mapOrNull($data, 'license');
        $pluginsCount = Data::mapOrNull($data, 'plugins_count');
        $systemInfo = Data::mapOrNull($data, 'system_info');

        return new self(
            checksum: Data::stringOrNull($data, 'checksum'),
            consumersCount: Data::intOrNull($data, 'consumers_count'),
            counters: $counters === null ? null : LicenseReportCounters::fromArray($counters),
            dbVersion: Data::stringOrNull($data, 'db_version'),
            deploymentInfo: $deploymentInfo === null ? null : LicenseReportDeployment::fromArray($deploymentInfo),
            kongVersion: Data::stringOrNull($data, 'kong_version'),
            license: $license === null ? null : LicenseReportLicense::fromArray($license),
            pluginsCount: $pluginsCount === null ? null : LicenseReportPluginsCount::fromArray($pluginsCount),
            rbacUsers: Data::intOrNull($data, 'rbac_users'),
            routesCount: Data::intOrNull($data, 'routes_count'),
            servicesCount: Data::intOrNull($data, 'services_count'),
            systemInfo: $systemInfo === null ? null : LicenseReportSystemInfo::fromArray($systemInfo),
            timestamp: Data::intOrNull($data, 'timestamp'),
            workspacesCount: Data::intOrNull($data, 'workspaces_count'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'checksum' => $this->checksum,
            'consumers_count' => $this->consumersCount,
            'counters' => $this->counters?->toArray(),
            'db_version' => $this->dbVersion,
            'deployment_info' => $this->deploymentInfo?->toArray(),
            'kong_version' => $this->kongVersion,
            'license' => $this->license?->toArray(),
            'plugins_count' => $this->pluginsCount?->toArray(),
            'rbac_users' => $this->rbacUsers,
            'routes_count' => $this->routesCount,
            'services_count' => $this->servicesCount,
            'system_info' => $this->systemInfo?->toArray(),
            'timestamp' => $this->timestamp,
            'workspaces_count' => $this->workspacesCount,
        ]);
    }
}

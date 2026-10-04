<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Node information from `GET /` (spec response `GetKongInfoResponse`).
 */
#[Schema('#/components/responses/GetKongInfoResponse/content/application~1json/schema')]
final readonly class KongInfo implements Model
{
    /**
     * @param array<array-key, mixed>|null $configuration A sanitized version of the Kong configuration, excluding sensitive values.
     * @param string|null                  $edition       Indicates whether the Kong instance is the Community or Enterprise edition.
     * @param string|null                  $hostname      The hostname of the Kong node.
     * @param string|null                  $luaVersion    The version of Lua used by the Kong instance.
     * @param string|null                  $nodeId        A unique identifier for the node, in UUID format.
     * @param KongInfoPids|null            $pids          Process IDs for the master process and worker processes.
     * @param KongInfoPlugins|null         $plugins       Information about plugins.
     * @param string|null                  $tagline       A tagline or slogan for the Kong instance.
     * @param KongInfoTimers|null          $timers        Information about running and pending timers.
     * @param string|null                  $version       The version number of the Kong instance.
     */
    public function __construct(
        public ?array $configuration = null,
        public ?string $edition = null,
        public ?string $hostname = null,
        public ?string $luaVersion = null,
        public ?string $nodeId = null,
        public ?KongInfoPids $pids = null,
        public ?KongInfoPlugins $plugins = null,
        public ?string $tagline = null,
        public ?KongInfoTimers $timers = null,
        public ?string $version = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $pids = Data::mapOrNull($data, 'pids');
        $plugins = Data::mapOrNull($data, 'plugins');
        $timers = Data::mapOrNull($data, 'timers');

        return new self(
            configuration: Data::freeFormOrNull($data, 'configuration'),
            edition: Data::stringOrNull($data, 'edition'),
            hostname: Data::stringOrNull($data, 'hostname'),
            luaVersion: Data::stringOrNull($data, 'lua_version'),
            nodeId: Data::stringOrNull($data, 'node_id'),
            pids: $pids === null ? null : KongInfoPids::fromArray($pids),
            plugins: $plugins === null ? null : KongInfoPlugins::fromArray($plugins),
            tagline: Data::stringOrNull($data, 'tagline'),
            timers: $timers === null ? null : KongInfoTimers::fromArray($timers),
            version: Data::stringOrNull($data, 'version'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'configuration' => $this->configuration,
            'edition' => $this->edition,
            'hostname' => $this->hostname,
            'lua_version' => $this->luaVersion,
            'node_id' => $this->nodeId,
            'pids' => $this->pids?->toArray(),
            'plugins' => $this->plugins?->toArray(),
            'tagline' => $this->tagline,
            'timers' => $this->timers?->toArray(),
            'version' => $this->version,
        ]);
    }
}

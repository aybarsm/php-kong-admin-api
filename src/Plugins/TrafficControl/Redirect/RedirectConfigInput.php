<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\Redirect;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `redirect` plugin (Redirect; doc `TrafficControl/redirect.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/redirect.md', '#/properties/config')]
final readonly class RedirectConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'redirect';

    /**
     * @param string|null $location         The URL to redirect to Required by the plugin doc.
     * @param bool|null   $keepIncomingPath Use the incoming request's path and query string in the redirect URL Default: `false`.
     * @param int|null    $statusCode       The response code to send. Default: `301`.
     */
    public function __construct(
        public ?string $location = null,
        public ?bool $keepIncomingPath = null,
        public ?int $statusCode = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'location' => $this->location,
            'keep_incoming_path' => $this->keepIncomingPath,
            'status_code' => $this->statusCode,
        ]);
    }
}

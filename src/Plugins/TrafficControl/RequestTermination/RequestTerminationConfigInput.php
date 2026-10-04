<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RequestTermination;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `request-termination` plugin (Request Termination; doc `TrafficControl/request-termination.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/request-termination.md', '#/properties/config')]
final readonly class RequestTerminationConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'request-termination';

    /**
     * @param string|null $body        The raw response body to send.
     * @param string|null $contentType Content type of the raw response configured with `config.body`.
     * @param bool|null   $echo        When set, the plugin will echo a copy of the request back to the client. Default: `false`.
     * @param string|null $message     The message to send, if using the default response generator.
     * @param int|null    $statusCode  The response code to send. Default: `503`.
     * @param string|null $trigger     A string representing an HTTP header name.
     */
    public function __construct(
        public ?string $body = null,
        public ?string $contentType = null,
        public ?bool $echo = null,
        public ?string $message = null,
        public ?int $statusCode = null,
        public ?string $trigger = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'body' => $this->body,
            'content_type' => $this->contentType,
            'echo' => $this->echo,
            'message' => $this->message,
            'status_code' => $this->statusCode,
            'trigger' => $this->trigger,
        ]);
    }
}

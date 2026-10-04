<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RequestTermination;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `request-termination` plugin (Request Termination; doc `TrafficControl/request-termination.md`).
 *
 * Read it from a returned Plugin with `RequestTerminationConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/request-termination.md', '#/properties/config')]
final readonly class RequestTerminationConfig implements PluginConfig
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
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            body: Data::stringOrNull($data, 'body'),
            contentType: Data::stringOrNull($data, 'content_type'),
            echo: Data::boolOrNull($data, 'echo'),
            message: Data::stringOrNull($data, 'message'),
            statusCode: Data::intOrNull($data, 'status_code'),
            trigger: Data::stringOrNull($data, 'trigger'),
        );
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

    /**
     * Reads the typed configuration of a `request-termination` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `request-termination` plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    #[Override]
    public static function fromPlugin(Plugin $plugin): static
    {
        if ($plugin->name !== self::NAME) {
            throw new InvalidArgumentException(sprintf('Expected a "%s" plugin, got "%s".', self::NAME, $plugin->name));
        }

        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));
    }
}

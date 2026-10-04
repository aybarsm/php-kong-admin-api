<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Request body for `POST /event-hooks` (spec request body `AddWebhook`).
 *
 * The spec names `config.*` properties with literal dots; they are sent exactly as named.
 */
#[Schema('#/components/requestBodies/AddWebhook/content/application~1json/schema')]
final readonly class WebhookInput implements Input
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['configSecret'];

    /**
     * @param string|null         $configUrl       The URL the JSON POST request is made to with the event data as the payload. Required by the spec on create.
     * @param string|null         $handler         A string describing one of four handler options: webhook, webhook-custom, log, or lambda. Required by the spec on create.
     * @param string|null         $source          A string describing the action that triggers the event hook. Required by the spec on create.
     * @param WebhookHeaders|null $configHeaders   An object defining additional HTTP headers to send in the webhook request.
     * @param string|null         $configSecret    An optional string used to sign the remote webhook for remote verification.
     * @param string|null         $configSslVerify A boolean indicating whether to verify the SSL certificate of the remote HTTPS server where the event hook wi…
     * @param string|null         $event           A string describing the Kong entity the event hook listens to for events.
     * @param bool|null           $onChange        An optional boolean indicating whether to trigger an event when key parts of a payload have changed.
     * @param int|null            $snooze          An optional integer describing the time in seconds to delay an event trigger to avoid spamming an integration.
     */
    public function __construct(
        public ?string $configUrl = null,
        public ?string $handler = null,
        public ?string $source = null,
        public ?WebhookHeaders $configHeaders = null,
        #[SensitiveParameter]
        public ?string $configSecret = null,
        public ?string $configSslVerify = null,
        public ?string $event = null,
        public ?bool $onChange = null,
        public ?int $snooze = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'config.url' => $this->configUrl,
            'handler' => $this->handler,
            'source' => $this->source,
            'config.headers' => $this->configHeaders?->toArray(),
            'config.secret' => $this->configSecret,
            'config.ssl_verify' => $this->configSslVerify,
            'event' => $this->event,
            'on_change' => $this->onChange,
            'snooze' => $this->snooze,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}

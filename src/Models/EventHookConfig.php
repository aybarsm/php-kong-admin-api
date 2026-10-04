<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config` object of EventHook (spec `#/components/schemas/Event-Hooks/properties/data.config`).
 */
#[Schema('#/components/schemas/Event-Hooks/properties/data/items/properties/config')]
final readonly class EventHookConfig implements Model
{
    /**
     * @param string|null                 $body
     * @param bool|null                   $bodyFormat
     * @param list<string>|null           $functions
     * @param EventHookConfigHeaders|null $headers
     * @param bool|null                   $headersFormat
     * @param string|null                 $method
     * @param EventHookConfigPayload|null $payload
     * @param bool|null                   $payloadFormat
     * @param string|null                 $secret
     * @param bool|null                   $sslVerify
     * @param string|null                 $url
     */
    public function __construct(
        public ?string $body = null,
        public ?bool $bodyFormat = null,
        public ?array $functions = null,
        public ?EventHookConfigHeaders $headers = null,
        public ?bool $headersFormat = null,
        public ?string $method = null,
        public ?EventHookConfigPayload $payload = null,
        public ?bool $payloadFormat = null,
        public ?string $secret = null,
        public ?bool $sslVerify = null,
        public ?string $url = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $headers = Data::mapOrNull($data, 'headers');
        $payload = Data::mapOrNull($data, 'payload');

        return new self(
            body: Data::stringOrNull($data, 'body'),
            bodyFormat: Data::boolOrNull($data, 'body_format'),
            functions: Data::stringListOrNull($data, 'functions'),
            headers: $headers === null ? null : EventHookConfigHeaders::fromArray($headers),
            headersFormat: Data::boolOrNull($data, 'headers_format'),
            method: Data::stringOrNull($data, 'method'),
            payload: $payload === null ? null : EventHookConfigPayload::fromArray($payload),
            payloadFormat: Data::boolOrNull($data, 'payload_format'),
            secret: Data::stringOrNull($data, 'secret'),
            sslVerify: Data::boolOrNull($data, 'ssl_verify'),
            url: Data::stringOrNull($data, 'url'),
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
            'body_format' => $this->bodyFormat,
            'functions' => $this->functions,
            'headers' => $this->headers?->toArray(),
            'headers_format' => $this->headersFormat,
            'method' => $this->method,
            'payload' => $this->payload?->toArray(),
            'payload_format' => $this->payloadFormat,
            'secret' => $this->secret,
            'ssl_verify' => $this->sslVerify,
            'url' => $this->url,
        ]);
    }
}

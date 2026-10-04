<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;

/**
 * Declarative configuration (spec tag "Config"): `/config`. Global path.
 */
final readonly class DeclarativeConfig extends AbstractResource
{
    private const string SEGMENT = 'config';

    /**
     * The current declarative configuration (operationId `get-config`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/config', 'get-config', OperationScope::GlobalOnly)]
    public function get(): \Aybarsm\Kong\AdminApi\Models\DeclarativeConfig
    {
        return \Aybarsm\Kong\AdminApi\Models\DeclarativeConfig::fromArray(
            $this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT)),
        );
    }

    /**
     * Load a declarative configuration (operationId `create-config`, POST). The spec's request and 201
     * response bodies are free-form objects; the request is sent as JSON (the spec also lists YAML and
     * multipart, which this client does not use).
     *
     * @param array<string, mixed> $config the declarative configuration document
     *
     * @return array<string, mixed>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/config', 'create-config', OperationScope::GlobalOnly)]
    public function apply(array $config): array
    {
        return $this->object(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT), body: $config);
    }
}

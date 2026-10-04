<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Enums\PartialType;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\PartialTypeSchema;
use Aybarsm\Kong\AdminApi\Models\PluginConfigSchema;
use Aybarsm\Kong\AdminApi\Models\SchemaValidation;

/**
 * Entity schemas (spec tag "Schemas", plus `GET /schemas/plugins/{pluginName}` tagged "Plugins"):
 * `/schemas/{entityName}/validate`, `/schemas/partials/{partialType}` and `/schemas/plugins/{pluginName}`.
 * Global paths.
 */
final readonly class Schemas extends AbstractResource
{
    private const string SEGMENT = 'schemas';

    /**
     * Validate an entity against its schema without saving it (operationId `validate-entity-schema`). The
     * spec's request body is a free-form object.
     *
     * @param string               $entityName e.g. `services`
     * @param array<string, mixed> $entity     the entity to validate
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $entityName is empty
     */
    #[Operation(Transport::METHOD_POST, '/schemas/{entityName}/validate', 'validate-entity-schema', OperationScope::GlobalOnly)]
    public function validate(string $entityName, array $entity): SchemaValidation
    {
        return SchemaValidation::fromArray(
            $this->object(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $entityName, 'validate'), body: $entity),
        );
    }

    /**
     * The schema of a Partial type (operationId `fetch-partial-schema`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $partialType is empty
     */
    #[Operation(Transport::METHOD_GET, '/schemas/partials/{partialType}', 'fetch-partial-schema', OperationScope::GlobalOnly)]
    public function partial(PartialType|string $partialType): PartialTypeSchema
    {
        $type = $partialType instanceof PartialType ? $partialType->value : $partialType;

        return PartialTypeSchema::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'partials', $type)));
    }

    /**
     * The configuration schema of a plugin (operationId `fetch-plugin-schema`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $pluginName is empty
     */
    #[Operation(Transport::METHOD_GET, '/schemas/plugins/{pluginName}', 'fetch-plugin-schema', OperationScope::GlobalOnly)]
    public function plugin(string $pluginName): PluginConfigSchema
    {
        return PluginConfigSchema::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'plugins', $pluginName)));
    }
}

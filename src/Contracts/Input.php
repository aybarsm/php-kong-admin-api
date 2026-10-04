<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Contracts;

/**
 * A typed request body for a write operation.
 */
interface Input
{
    /**
     * The JSON request body using the spec's property names. Null values are omitted;
     * pass an array instead of an Input to send an explicit null.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}

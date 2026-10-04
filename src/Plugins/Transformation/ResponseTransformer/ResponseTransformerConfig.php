<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\ResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `response-transformer` plugin (Response Transformer; doc `Transformation/response-transformer.md`).
 *
 * Read it from a returned Plugin with `ResponseTransformerConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Transformation/response-transformer.md', '#/properties/config')]
final readonly class ResponseTransformerConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'response-transformer';

    /**
     * @param Add|null     $add
     * @param Append|null  $append
     * @param Remove|null  $remove
     * @param Rename|null  $rename
     * @param Replace|null $replace
     */
    public function __construct(
        public ?Add $add = null,
        public ?Append $append = null,
        public ?Remove $remove = null,
        public ?Rename $rename = null,
        public ?Replace $replace = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $add = Data::mapOrNull($data, 'add');
        $append = Data::mapOrNull($data, 'append');
        $remove = Data::mapOrNull($data, 'remove');
        $rename = Data::mapOrNull($data, 'rename');
        $replace = Data::mapOrNull($data, 'replace');

        return new self(
            add: $add === null ? null : Add::fromArray($add),
            append: $append === null ? null : Append::fromArray($append),
            remove: $remove === null ? null : Remove::fromArray($remove),
            rename: $rename === null ? null : Rename::fromArray($rename),
            replace: $replace === null ? null : Replace::fromArray($replace),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'add' => $this->add?->toArray(),
            'append' => $this->append?->toArray(),
            'remove' => $this->remove?->toArray(),
            'rename' => $this->rename?->toArray(),
            'replace' => $this->replace?->toArray(),
        ]);
    }

    /**
     * Reads the typed configuration of a `response-transformer` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `response-transformer` plugin
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

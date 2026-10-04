<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\BotDetection;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `bot-detection` plugin (Bot Detection; doc `Security/bot-detection.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Security/bot-detection.md', '#/properties/config')]
final readonly class BotDetectionConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'bot-detection';

    /**
     * @param list<string>|null $allow An array of regular expressions that should be allowed. Default: `[]`.
     * @param list<string>|null $deny  An array of regular expressions that should be denied. Default: `[]`.
     */
    public function __construct(
        public ?array $allow = null,
        public ?array $deny = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow' => $this->allow,
            'deny' => $this->deny,
        ]);
    }
}

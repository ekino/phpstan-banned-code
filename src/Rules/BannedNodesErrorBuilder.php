<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/phpstan-banned-code project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\PHPStanBannedCode\Rules;

use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedNodesErrorBuilder
{
    public const ERROR_IDENTIFIER_PREFIX = 'ekinoBannedCode';

    public function __construct(private readonly bool $nonIgnorable)
    {
    }

    public function buildError(
        string $errorMessage,
        string $errorSuffix
    ): IdentifierRuleError {
        $errBuilder = RuleErrorBuilder::message($errorMessage)
            ->identifier(\sprintf('%s.%s', self::ERROR_IDENTIFIER_PREFIX, $errorSuffix));

        if ($this->nonIgnorable) {
            $errBuilder->nonIgnorable();
        }

        return $errBuilder->build();
    }
}

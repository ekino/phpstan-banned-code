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

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */

/**
 * @implements Rule<Use_>
 */
class BannedUseTestRule implements Rule
{
    public function __construct(private readonly bool $enabled)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeType(): string
    {
        return Use_::class;
    }

    /**
     * {@inheritdoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$this->enabled) {
            return [];
        }

        if (!$namespace = $scope->getNamespace()) {
            return [];
        }

        if (preg_match('#^Tests#', $namespace)) {
            return [];
        }

        $errors = [];

        foreach ($node->uses as $use) {
            if (preg_match('#^Tests#', $use->name->toString())) {
                $errors[] = RuleErrorBuilder::message(
                    \sprintf('Should not use %s in the non-test file %s', $use->name->toString(), $scope->getFile())
                )
                    ->nonIgnorable()
                    ->identifier('ekinoBannedCode.use.forbidden')
                    ->build();
            }
        }

        return $errors;
    }
}

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

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedUseTestRule implements Rule
{
    public function __construct(
        private readonly bool $enabled,
        private readonly BannedNodesErrorBuilder $errorBuilder
    )
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

        if (!$node instanceof Use_) {
            throw new \InvalidArgumentException(\sprintf('$node must be an instance of %s, %s given', Use_::class, \get_class($node)));
        }

        $errors = [];

        foreach ($node->uses as $use) {
            if (preg_match('#^Tests#', $use->name->toString())) {
                $errors[] = $this->errorBuilder->buildError(
                    \sprintf('Should not use %s in the non-test file %s', $use->name->toString(), $scope->getFile()),
                    'test',
                );
            }
        }

        return $errors;
    }
}

<?php

/*
 * This file is part of the phpstan/phpstan-banned-code project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @author Rémi Marseille <marseille@ekino.com>
 */
class BannedFunctionsRule implements Rule
{
    /**
     * @var array
     */
    private $bannedFunctions;

    /**
     * @param array $bannedFunctions
     */
    public function __construct(array $bannedFunctions)
    {
        $this->bannedFunctions = $bannedFunctions;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    /**
     * {@inheritdoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof FuncCall) {
            throw new \InvalidArgumentException(sprintf('$node must be an instance of %s, %s given', FuncCall::class, get_class($node)));
        }

        if (!$node->name instanceof Name) {
            return [];
        }

        $function = $node->name->toString();

        if (in_array($function, $this->bannedFunctions)) {
            return [sprintf('Should not use "%s", please change the code.', $function)];
        }

        return [];
    }
}

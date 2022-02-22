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
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedNodesRule implements Rule
{
    /**
     * @var array<mixed>
     */
    private $bannedNodes;

    /**
     * @param array<mixed> $bannedNodes
     */
    public function __construct(array $bannedNodes)
    {
        $this->bannedNodes = array_column($bannedNodes, null, 'type');
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * {@inheritdoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $type = $node->getType();

        if (!\array_key_exists($type, $this->bannedNodes)) {
            return [];
        }

        if ($node instanceof FuncCall) {
            if (!$node->name instanceof Name) {
                return [];
            }

            $function = $node->name->toString();

            if (\in_array($function, $this->bannedNodes[$type]['functions'])) {
                return [sprintf('Should not use function "%s", please change the code.', $function)];
            }

            return [];
        }

        return [sprintf('Should not use node with type "%s", please change the code.', $type)];
    }
}

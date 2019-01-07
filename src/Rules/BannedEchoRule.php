<?php

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
use PhpParser\Node\Stmt\Echo_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedEchoRule implements Rule
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param bool $enabled
     */
    public function __construct(bool $enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeType(): string
    {
        return Echo_::class;
    }

    /**
     * {@inheritdoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return $this->enabled ? ['Should not use "echo", please change the code.'] : [];
    }
}

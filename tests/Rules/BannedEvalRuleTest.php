<?php

/*
 * This file is part of the ekino/phpstan-banned-code project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Ekino\PHPStanBannedCode\Rules;

use Ekino\PHPStanBannedCode\Rules\BannedEvalRule;
use PhpParser\Node\Expr\Eval_;
use PHPStan\Analyser\Scope;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedEvalRuleTest extends TestCase
{
    /**
     * Tests getNodeType.
     */
    public function testGetNodeType()
    {
        $this->assertSame(Eval_::class, (new BannedEvalRule())->getNodeType());
    }

    /**
     * Tests processNode.
     */
    public function testProcessNode()
    {
        $this->assertCount(0, (new BannedEvalRule(false))->processNode($this->createMock(Eval_::class), $this->createMock(Scope::class)));
        $this->assertCount(1, (new BannedEvalRule())->processNode($this->createMock(Eval_::class), $this->createMock(Scope::class)));
    }
}

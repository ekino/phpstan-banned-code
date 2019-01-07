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

use Ekino\PHPStanBannedCode\Rules\BannedEchoRule;
use PhpParser\Node\Stmt\Echo_;
use PHPStan\Analyser\Scope;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedEchoRuleTest extends TestCase
{
    /**
     * Tests getNodeType.
     */
    public function testGetNodeType()
    {
        $this->assertSame(Echo_::class, (new BannedEchoRule())->getNodeType());
    }

    /**
     * Tests processNode.
     */
    public function testProcessNode()
    {
        $this->assertCount(0, (new BannedEchoRule(false))->processNode($this->createMock(Echo_::class), $this->createMock(Scope::class)));
        $this->assertCount(1, (new BannedEchoRule())->processNode($this->createMock(Echo_::class), $this->createMock(Scope::class)));
    }
}

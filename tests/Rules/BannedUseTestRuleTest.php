<?php

/*
 * This file is part of the phpstan/phpstan-banned-code project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\BannedUseTestRule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <marseille@ekino.com>
 */
class BannedUseTestRuleTest extends TestCase
{
    /**
     * @var BannedUseTestRule
     */
    private $rule;

    /**
     * @var Scope|MockObject
     */
    private $scope;

    /**
     * Initializes the tests.
     */
    protected function setUp()
    {
        $this->rule  = new BannedUseTestRule();
        $this->scope = $this->createMock(Scope::class);
    }

    /**
     * Tests getNodeType.
     */
    public function testGetNodeType()
    {
        $this->assertSame(Use_::class, $this->rule->getNodeType());
    }

    /**
     * Tests processNode if disabled.
     */
    public function testProcessNodeIfDisabled()
    {
        $this->scope->expects($this->never())->method('getNamespace');

        $this->assertCount(0, (new BannedUseTestRule(false))->processNode($this->createMock(Use_::class), $this->scope));
    }

    /**
     * Tests processNode with test scope.
     */
    public function testProcessNodeWithTestScope()
    {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn('Tests\\Foo\\Bar');

        $this->assertCount(0, $this->rule->processNode($this->createMock(Use_::class), $this->scope));
    }

    /**
     * Asserts processNode throws an exception with invalid argument.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testProcessNodeThrowsException()
    {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn('Foo\\Bar');

        $this->rule->processNode($this->createMock(Node::class), $this->scope);
    }

    /**
     * Tests processNode with errors.
     */
    public function testProcessNodeWithErrors()
    {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn('Foo\\Bar');

        $node = new Use_([
            new UseUse(new Name('Foo\\Bar')),
            new UseUse(new Name('Tests\\Foo\\Bar')),
        ]);

        $this->assertCount(1, $this->rule->processNode($node, $this->scope));
    }
}

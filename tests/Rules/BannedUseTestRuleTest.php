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

use Ekino\PHPStanBannedCode\Rules\BannedUseTestRule;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PHPStan\Analyser\Scope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
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
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->rule  = new BannedUseTestRule();
        $this->scope = $this->createMock(Scope::class);
    }

    /**
     * Tests getNodeType.
     */
    public function testGetNodeType(): void
    {
        $this->assertSame(Use_::class, $this->rule->getNodeType());
    }

    /**
     * Tests processNode if disabled.
     */
    public function testProcessNodeIfDisabled(): void
    {
        $this->scope->expects($this->never())->method('getNamespace');

        $this->assertCount(0, (new BannedUseTestRule(false))->processNode($this->createMock(Use_::class), $this->scope));
    }

    /**
     * Tests processNode with test scope.
     */
    public function testProcessNodeWithTestScope(): void
    {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn('Tests\\Foo\\Bar');

        $this->assertCount(0, $this->rule->processNode($this->createMock(Use_::class), $this->scope));
    }

    /**
     * Asserts processNode throws an exception with invalid argument.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testProcessNodeThrowsException(): void
    {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn('Foo\\Bar');

        $this->rule->processNode($this->createMock(Node::class), $this->scope);
    }

    /**
     * Tests processNode with errors.
     */
    public function testProcessNodeWithErrors(): void
    {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn('Foo\\Bar');

        $node = new Use_([
            new UseUse(new Name('Foo\\Bar')),
            new UseUse(new Name('Tests\\Foo\\Bar')),
        ]);

        $this->assertCount(1, $this->rule->processNode($node, $this->scope));
    }
}

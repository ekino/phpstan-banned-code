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

use Ekino\PHPStanBannedCode\Rules\BannedNodesRule;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Eval_;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedNodesRuleTest extends TestCase
{
    /**
     * @var BannedNodesRule
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
        $this->rule  = new BannedNodesRule([
            ['type' => 'Stmt_Echo'],
            ['type' => 'Expr_Eval'],
            ['type' => 'Expr_Exit'],
            ['type' => 'Expr_FuncCall', 'functions' => ['debug_backtrace', 'dump']],
        ]);
        $this->scope = $this->createMock(Scope::class);
    }

    /**
     * Tests getNodeType.
     */
    public function testGetNodeType()
    {
        $this->assertSame(Node::class, $this->rule->getNodeType());
    }

    /**
     * Tests processNode with unhandled nodes.
     *
     * @param Expr $node
     *
     * @dataProvider getUnhandledNodes
     */
    public function testProcessNodeWithUnhandledType(Expr $node)
    {
        $this->assertCount(0, $this->rule->processNode($node, $this->scope));
    }

    /**
     * Tests processNode with banned/allowed functions.
     */
    public function testProcessNodeWithFunctions()
    {
        foreach (['debug_backtrace', 'dump'] as $bannedFunction) {
            $node = new FuncCall(new Name($bannedFunction));

            $this->assertCount(1, $this->rule->processNode($node, $this->scope));
        }

        foreach (['array_search', 'sprintf'] as $allowedFunction) {
            $node = new FuncCall(new Name($allowedFunction));

            $this->assertCount(0, $this->rule->processNode($node, $this->scope));
        }
    }

    /**
     * Tests processNode with handled nodes.
     *
     * @param Expr $node
     *
     * @dataProvider getHandledNodes
     */
    public function testProcessNodeWithHandledTypes(Expr $node)
    {
        $this->assertCount(1, $this->rule->processNode($node, $this->scope));
    }

    /**
     * @return \Generator
     */
    public function getUnhandledNodes()
    {
        yield [new Include_($this->createMock(Expr::class), Include_::TYPE_INCLUDE)];
    }

    /**
     * @return \Generator
     */
    public function getHandledNodes()
    {
        yield [new Eval_($this->createMock(Expr::class))];
        yield [new Exit_()];
    }
}

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

namespace Tests\Ekino\PHPStanBannedCode\Rules;

use Ekino\PHPStanBannedCode\Rules\BannedNodesErrorBuilder;
use Ekino\PHPStanBannedCode\Rules\BannedNodesRule;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Eval_;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\Print_;
use PhpParser\Node\Expr\ShellExec;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\LNumber;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\NonIgnorableRuleError;
use PHPStan\Rules\RuleError;
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
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->rule  = new BannedNodesRule(
            [
                ['type' => 'Stmt_Echo'],
                ['type' => 'Expr_Eval'],
                ['type' => 'Expr_Exit'],
                ['type' => 'Expr_FuncCall', 'functions' => ['debug_backtrace', 'dump', 'Safe\namespaced']],
                ['type' => 'Expr_Print'],
                ['type' => 'Expr_ShellExec'],
            ]
        );
        $this->scope = $this->createMock(Scope::class);
    }

    /**
     * Tests getNodeType.
     */
    public function testGetNodeType(): void
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
    public function testProcessNodeWithUnhandledType(Expr $node): void
    {
        $this->assertCount(0, $this->rule->processNode($node, $this->scope));
    }

    public function testProcessNodeWithBannedFunctions(): void
    {
        $ruleWithoutLeadingSlashes = new BannedNodesRule(
            [
                [
                    'type'      => 'Expr_FuncCall',
                    'functions' => [
                        'root',
                        'Safe\namespaced',
                    ]
                ],
            ]
        );

        $ruleWithLeadingSlashes = new BannedNodesRule(
            [
                [
                    'type'      => 'Expr_FuncCall',
                    'functions' => [
                        '\root',
                        '\Safe\namespaced',
                    ]
                ],
            ]
        );

        $rootFunction = new FuncCall(new Name('root'));
        $this->assertNodeTriggersError($ruleWithoutLeadingSlashes, $rootFunction);
        $this->assertNodeTriggersError($ruleWithLeadingSlashes, $rootFunction);

        $namespacedFunction = new FuncCall(new FullyQualified('Safe\namespaced'));
        $this->assertNodeTriggersError($ruleWithoutLeadingSlashes, $namespacedFunction);
        $this->assertNodeTriggersError($ruleWithLeadingSlashes, $namespacedFunction);
    }

    protected function assertNodeTriggersError(BannedNodesRule $rule, Node $node): void
    {
        $errors = $rule->processNode($node, $this->scope);
        $this->assertCount(1, $errors);
        $error = $errors[0];
        $this->assertStringStartsWith('ekinoBannedCode.', $error->getIdentifier());
        $this->assertInstanceOf(NonIgnorableRuleError::class, $error);
    }

    protected function assertNodePasses(BannedNodesRule $rule, Node $node): void
    {
        $this->assertCount(0, $rule->processNode($node, $this->scope));
    }

    public function testProcessNodeWithAllowedFunctions(): void
    {
        $rootFunction = new FuncCall(new Name('allowed'));
        $this->assertNodePasses($this->rule, $rootFunction);

        $namespacedFunction = new FuncCall(new FullyQualified('Safe\allowed'));
        $this->assertNodePasses($this->rule, $namespacedFunction);
    }

    public function testProcessNodeWithFunctionInClosure(): void
    {
        $node = new FuncCall(new Variable('myClosure'));

        $this->assertNodePasses($this->rule, $node);
    }

    public function testProcessNodeWithArrayDimFetch(): void
    {
        $node = new FuncCall(
            new Expr\ArrayDimFetch(
                new Variable('myArray'),
                LNumber::fromString('0', ['kind' => LNumber::KIND_DEC])
            )
        );

        $this->assertNodePasses($this->rule, $node);
    }

    /**
     * Tests processNode with handled nodes.
     *
     * @param Expr $node
     *
     * @dataProvider getHandledNodes
     */
    public function testProcessNodeWithHandledTypes(Expr $node): void
    {
        $this->assertNodeTriggersError($this->rule, $node);
    }

    /**
     * @return \Generator<array<Include_>>
     */
    public function getUnhandledNodes(): \Generator
    {
        yield [new Include_($this->createMock(Expr::class), Include_::TYPE_INCLUDE)];
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public function getHandledNodes(): \Generator
    {
        yield [new Eval_($this->createMock(Expr::class))];
        yield [new Exit_()];
        yield [new Print_($this->createMock(Expr::class))];
        yield [new ShellExec([$this->createMock(Expr::class)])];
    }
}

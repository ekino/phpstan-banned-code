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
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\BannedFunctionsRule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <marseille@ekino.com>
 */
class BannedFunctionsRuleTest extends TestCase
{
    /**
     * @var BannedFunctionsRule
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
        $this->rule  = new BannedFunctionsRule(['die', 'var_dump']);
        $this->scope = $this->createMock(Scope::class);
    }

    /**
     * Tests getNodeType.
     */
    public function testGetNodeType()
    {
        $this->assertSame(FuncCall::class, $this->rule->getNodeType());
    }

    /**
     * Asserts processNode throws an exception with invalid argument.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testProcessNodeThrowsException()
    {
        $this->rule->processNode($this->createMock(Node::class), $this->scope);
    }

    /**
     * Tests processNode with banned functions.
     */
    public function testProcessNodeWithBannedFunctions()
    {
        foreach (['die', 'var_dump'] as $bannedFunction) {
            $node = new FuncCall(new Name($bannedFunction));

            $this->assertCount(1, $this->rule->processNode($node, $this->scope));
        }
    }

    /**
     * Tests processNode with allowed functions.
     */
    public function testProcessNodeWithAllowedFunctions()
    {
        foreach (['array_search', 'sprintf'] as $allowedFunction) {
            $node = new FuncCall(new Name($allowedFunction));

            $this->assertCount(0, $this->rule->processNode($node, $this->scope));
        }
    }
}

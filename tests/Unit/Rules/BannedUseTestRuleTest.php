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

namespace Tests\Ekino\PHPStanBannedCode\Unit\Rules;

use Ekino\PHPStanBannedCode\Rules\BannedNodesErrorBuilder;
use Ekino\PHPStanBannedCode\Rules\BannedUseTestRule;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PHPStan\Analyser\NodeCallbackInvoker;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\NonIgnorableRuleError;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Rémi Marseille <remi.marseille@ekino.com>
 */
class BannedUseTestRuleTest extends TestCase
{
    private BannedUseTestRule $rule;

    private NodeCallbackInvoker&Scope&MockObject $scope;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->rule  = new BannedUseTestRule(
            true,
            new BannedNodesErrorBuilder(true)
        );

        /** @phpstan-ignore-next-line */
        $this->scope = $this->createMockForIntersectionOfInterfaces([
            NodeCallbackInvoker::class,
            Scope::class,
        ]);
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
        $testRule = new BannedUseTestRule(
            false,
            new BannedNodesErrorBuilder(true)
        );

        $this->assertCount(0, ($testRule)->processNode($this->createMock(Use_::class), $this->scope));
    }

    /**
     * Asserts processNode throws an exception with invalid argument.
     */
    public function testProcessNodeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->scope->expects($this->once())->method('getNamespace')->willReturn('Foo\\Bar');

        $this->rule->processNode($this->createMock(Node::class), $this->scope);
    }

    /**
     * @dataProvider namespaceDataProvider
     */
    public function testProcessNodeWithTestNamespaces(string $namespace): void
    {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn($namespace);

        $this->assertCount(0, $this->rule->processNode($this->createMock(Use_::class), $this->scope));
    }

    /**
     * @return iterable<string, list<string>>
     */
    public static function namespaceDataProvider(): iterable
    {
        yield 'Tests namespace prefix' => ['Tests\\Foo\\Bar'];
        yield 'Nested test namespace' => ['Modules\\Foo\\Tests'];
        yield 'Deeply nested test namespace' => ['Modules\\Foo\\Tests\\Models\\State'];
        yield 'Trailing Tests segment' => ['App\\Module\\Tests'];
    }

    /**
     * @dataProvider errorCasesDataProvider
     * @param list<string> $useStatements
     */
    public function testProcessNodeWithErrors(
        string $namespace,
        array $useStatements,
        int $expectedErrorCount
    ): void {
        $this->scope->expects($this->once())->method('getNamespace')->willReturn($namespace);

        $uses = array_map(fn($use) => new UseUse(new Name($use)), $useStatements);
        $node = new Use_($uses);

        $errors = $this->rule->processNode($node, $this->scope);
        $this->assertCount($expectedErrorCount, $errors);

        foreach ($errors as $error) {
            $this->assertStringStartsWith('ekinoBannedCode.', $error->getIdentifier());
            $this->assertInstanceOf(NonIgnorableRuleError::class, $error);
        }
    }

    /**
     * @return iterable<string, array{string, array<string>, int}>
     */
    public static function errorCasesDataProvider(): iterable
    {
        yield 'Basic test import detection' => [
            'Foo\\Bar',
            ['Foo\\Bar', 'Tests\\Foo\\Bar'],
            1
        ];

        yield 'Multiple nested test imports' => [
            'App\\Services',
            ['App\\Models\\User', 'Modules\\Foo\\Tests\\SomeTestHelper', 'Modules\\Bar\\Tests\\Models\\A'],
            2
        ];

        yield 'No test-related imports' => [
            'App\\SomeTestsRelated\\Service',
            ['App\\SomeTestsRelated\\Model', 'App\\NotTestsButSimilar\\Helper'],
            0
        ];
    }
}

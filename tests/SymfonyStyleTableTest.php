<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use tomkyle\Tabulate\SymfonyStyleTable;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

class SymfonyStyleTableTest extends TestCase
{
    private function createSymfonyStyleTable(bool $withHeaders = true, string $defaultAlign = 'left', bool $autoAlign = true): array
    {
        $stream = fopen('php://memory', 'r+');
        $input = new ArrayInput([]);
        $output = new StreamOutput($stream);
        $io = new SymfonyStyle($input, $output);

        $table = new SymfonyStyleTable($io, $withHeaders, $defaultAlign, $autoAlign);

        return [$table, $stream];
    }

    public function testFromConsoleFactoryMethod(): void
    {
        $table = SymfonyStyleTable::fromConsole();
        $this->assertInstanceOf(SymfonyStyleTable::class, $table);
    }

    public function testInvokeOutputsTableWithDefaultOptions(): void
    {
        $rows = [
            ['Name' => 'Alice', 'Age' => 30],
            ['Name' => 'Bob', 'Age' => 25],
        ];

        [$table, $stream] = $this->createSymfonyStyleTable();
        $table($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $this->assertStringContainsString('Alice', $output);
        $this->assertStringContainsString('Bob', $output);
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('Age', $output);
    }

    public function testInvokeWithoutHeaders(): void
    {
        $rows = [
            ['Name' => 'Alice', 'Age' => 30],
        ];

        [$table, $stream] = $this->createSymfonyStyleTable(false);
        $table($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $this->assertStringContainsString('Alice', $output);
        $this->assertStringContainsString('30', $output);
        // Headers should not be present
        $this->assertStringNotContainsString('Name', $output);
        $this->assertStringNotContainsString('Age', $output);
    }

    public static function alignmentTestProvider(): array
    {
        return [
            'alignRight with array' => [
                'method' => 'alignRight',
                'fields' => ['Age', 'Score'],
                'defaultAlign' => 'left',
                'expectedAlignments' => [
                    'Age' => true,
                    'Score' => true,
                    'Name' => false,
                ],
            ],
            'alignLeft with array overriding default right' => [
                'method' => 'alignLeft',
                'fields' => ['Name'],
                'defaultAlign' => 'right',
                'expectedAlignments' => [
                    'Name' => false,
                    'Age' => true,
                    'Score' => true,
                ],
            ],
            'alignRight with single field' => [
                'method' => 'alignRight',
                'fields' => 'Price',
                'defaultAlign' => 'left',
                'expectedAlignments' => [
                    'Price' => true,
                    'Product' => false,
                ],
            ],
        ];
    }

    #[DataProvider('alignmentTestProvider')]
    public function testAlignmentMethods(string $method, array|string $fields, string $defaultAlign, array $expectedAlignments): void
    {
        [$table, $stream] = $this->createSymfonyStyleTable(true, $defaultAlign, false);
        $table->$method($fields);

        // Test alignment configuration
        foreach ($expectedAlignments as $field => $expectedRightAligned) {
            $this->assertSame(
                $expectedRightAligned,
                $table->isRightAligned($field),
                sprintf("Field '%s' alignment does not match expected value", $field),
            );
        }

        // Test with sample data to ensure output works
        $rows = [
            ['Name' => 'Alice', 'Age' => 30, 'Score' => 95.5, 'Product' => 'Laptop', 'Price' => 999.99],
            ['Name' => 'Bob', 'Age' => 25, 'Score' => 87.2, 'Product' => 'Mouse', 'Price' => 29.99],
        ];

        $table($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $this->assertStringContainsString('Alice', $output);
        $this->assertStringContainsString('Bob', $output);
    }

    public function testMixedAlignmentOverridesDefaultAlign(): void
    {
        $rows = [
            ['Name' => 'Alice', 'Age' => 30, 'Salary' => 50000],
            ['Name' => 'Bob', 'Age' => 25, 'Salary' => 45000],
        ];

        [$table, $stream] = $this->createSymfonyStyleTable(true, 'right', false);
        $table->alignLeft('Name')->alignRight('Salary');

        // Test alignment configuration
        $this->assertFalse($table->isRightAligned('Name')); // Explicitly left
        $this->assertTrue($table->isRightAligned('Age')); // Default right
        $this->assertTrue($table->isRightAligned('Salary')); // Explicitly right

        $table($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $this->assertStringContainsString('Alice', $output);
        $this->assertStringContainsString('Bob', $output);
        $this->assertStringContainsString('50000', $output);
        $this->assertStringContainsString('45000', $output);
    }

    public function testAutoAlignmentWithNumericValues(): void
    {
        $rows = [
            ['Product' => 'Laptop', 'Price' => 999.99, 'Stock' => 15],
            ['Product' => 'Mouse', 'Price' => 29.99, 'Stock' => 150],
        ];

        [$table, $stream] = $this->createSymfonyStyleTable(true, 'left', true);
        $table($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $this->assertStringContainsString('Laptop', $output);
        $this->assertStringContainsString('Mouse', $output);
        $this->assertStringContainsString('999.99', $output);
        $this->assertStringContainsString('29.99', $output);
    }    public function testFluentInterface(): void
    {
        [$table] = $this->createSymfonyStyleTable();

        $result = $table->alignRight('Age')->alignLeft('Name');
        $this->assertSame($table, $result);
    }

    public static function defaultAlignmentProvider(): array
    {
        return [
            'default left alignment' => [
                'defaultAlign' => 'left',
                'fieldsToTest' => ['Name', 'Age'],
                'expectedRightAligned' => [false, false],
            ],
            'default right alignment' => [
                'defaultAlign' => 'right',
                'fieldsToTest' => ['Name', 'Age'],
                'expectedRightAligned' => [true, true],
            ],
        ];
    }

    #[DataProvider('defaultAlignmentProvider')]
    public function testIsRightAlignedWithDefaultAlignment(string $defaultAlign, array $fieldsToTest, array $expectedRightAligned): void
    {
        [$table] = $this->createSymfonyStyleTable(true, $defaultAlign, false);

        foreach ($fieldsToTest as $index => $field) {
            $this->assertSame($expectedRightAligned[$index], $table->isRightAligned($field));
        }
    }

    public static function explicitAlignmentProvider(): array
    {
        return [
            'explicit right and left alignment' => [
                'rightFields' => ['Price'],
                'leftFields' => ['Name'],
                'tests' => [
                    'Price' => true,
                    'Name' => false,
                    'UnknownField' => false,
                ],
            ],
            'multiple right alignments' => [
                'rightFields' => ['Age', 'Score'],
                'leftFields' => [],
                'tests' => [
                    'Age' => true,
                    'Score' => true,
                    'Name' => false,
                ],
            ],
        ];
    }

    #[DataProvider('explicitAlignmentProvider')]
    public function testIsRightAlignedWithExplicitAlignment(array $rightFields, array $leftFields, array $tests): void
    {
        [$table] = $this->createSymfonyStyleTable(true, 'left', false);

        // Set specific alignments
        if ($rightFields !== []) {
            $table->alignRight($rightFields);
        }

        if ($leftFields !== []) {
            $table->alignLeft($leftFields);
        }

        foreach ($tests as $field => $expected) {
            $this->assertSame($expected, $table->isRightAligned($field));
        }
    }

    public function testIsRightAlignedPriority(): void
    {
        [$table] = $this->createSymfonyStyleTable(true, 'right', false);

        // Default is right, but explicitly set left should override
        $table->alignLeft('Name');

        $this->assertFalse($table->isRightAligned('Name')); // Explicit left overrides default right
        $this->assertTrue($table->isRightAligned('Age')); // Uses default right

        // Now explicitly set Name to right again
        $table->alignRight('Name');
        $this->assertTrue($table->isRightAligned('Name')); // Explicit right
    }
}

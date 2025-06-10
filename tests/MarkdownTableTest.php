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
use tomkyle\Tabulate\MarkdownTable;

class MarkdownTableTest extends TestCase
{
    public function testInvokeOutputsMarkdownWithDefaultOptions(): void
    {
        $rows = [
            ['foo' => 'bar', 'baz' => 123],
            ['foo' => 'quux', 'baz' => 456],
        ];
        $stream = fopen('php://memory', 'r+');

        $markdownTable = new MarkdownTable(true, 'left', true, $stream);
        $markdownTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = '';
        $expected .= '| foo | baz |' . PHP_EOL;
        $expected .= '| ---- | ---: |' . PHP_EOL;
        $expected .= '| bar | 123 |' . PHP_EOL;
        $expected .= '| quux | 456 |' . PHP_EOL;

        $this->assertSame($expected, $output);
    }

    public function testInvokeWithoutHeaders(): void
    {
        $rows = [
            ['a' => 'b', 'c' => 'd'],
        ];
        $stream = fopen('php://memory', 'r+');

        $markdownTable = new MarkdownTable(false, 'left', false, $stream);
        $markdownTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = '| b | d |' . PHP_EOL;

        $this->assertSame($expected, $output);
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
                'expectedSeparator' => '| ---- | ---: | ---: |',
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
                'expectedSeparator' => '| ---- | ---: | ---: |',
            ],
            'alignRight with single field' => [
                'method' => 'alignRight',
                'fields' => 'Price',
                'defaultAlign' => 'left',
                'expectedAlignments' => [
                    'Price' => true,
                    'Product' => false,
                ],
                'expectedSeparator' => '| ---- | ---: |',
            ],
        ];
    }

    #[DataProvider('alignmentTestProvider')]
    public function testAlignmentMethods(string $method, array|string $fields, string $defaultAlign, array $expectedAlignments, string $expectedSeparator): void
    {
        $rows = match ($method . '_' . (is_array($fields) ? 'array' : 'string')) {
            'alignRight_array', 'alignLeft_array' => [
                ['Name' => 'Alice', 'Age' => 30, 'Score' => 95.5],
                ['Name' => 'Bob', 'Age' => 25, 'Score' => 87.2],
            ],
            default => [
                ['Product' => 'Laptop', 'Price' => 999.99],
                ['Product' => 'Mouse', 'Price' => 29.99],
            ],
        };

        $stream = fopen('php://memory', 'r+');
        $markdownTable = new MarkdownTable(true, $defaultAlign, false, $stream);
        $markdownTable->$method($fields);

        // Test alignment configuration
        foreach ($expectedAlignments as $field => $expectedRightAligned) {
            $this->assertSame(
                $expectedRightAligned,
                $markdownTable->isRightAligned($field),
                sprintf("Field '%s' alignment does not match expected value", $field),
            );
        }

        $markdownTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        // Check that the separator row has correct alignment markers
        $this->assertStringContainsString($expectedSeparator, $output);

        // Check that data is present based on the actual rows used
        if (isset($rows[0]['Name'])) {
            $this->assertStringContainsString('Alice', $output);
            $this->assertStringContainsString('Bob', $output);
        } else {
            $this->assertStringContainsString('Laptop', $output);
            $this->assertStringContainsString('Mouse', $output);
        }
    }

    public function testAlignLeftMethod(): void
    {
        $rows = [
            ['Name' => 'Alice', 'Age' => 30, 'Score' => 95.5],
            ['Name' => 'Bob', 'Age' => 25, 'Score' => 87.2],
        ];
        $stream = fopen('php://memory', 'r+');

        $markdownTable = new MarkdownTable(true, 'right', false, $stream);
        $markdownTable->alignLeft(['Name']);

        // Test alignment configuration
        $this->assertFalse($markdownTable->isRightAligned('Name'));
        $this->assertTrue($markdownTable->isRightAligned('Age'));
        $this->assertTrue($markdownTable->isRightAligned('Score'));

        $markdownTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = '';
        $expected .= '| Name | Age | Score |' . PHP_EOL;
        $expected .= '| ---- | ---: | ---: |' . PHP_EOL;
        $expected .= '| Alice | 30 | 95.5 |' . PHP_EOL;
        $expected .= '| Bob | 25 | 87.2 |' . PHP_EOL;

        $this->assertSame($expected, $output);
    }

    public function testAlignRightWithSingleField(): void
    {
        $rows = [
            ['Product' => 'Laptop', 'Price' => 999.99],
            ['Product' => 'Mouse', 'Price' => 29.99],
        ];
        $stream = fopen('php://memory', 'r+');

        $markdownTable = new MarkdownTable(true, 'left', false, $stream);
        $markdownTable->alignRight('Price');

        // Test alignment configuration
        $this->assertTrue($markdownTable->isRightAligned('Price'));
        $this->assertFalse($markdownTable->isRightAligned('Product'));

        $markdownTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = '';
        $expected .= '| Product | Price |' . PHP_EOL;
        $expected .= '| ---- | ---: |' . PHP_EOL;
        $expected .= '| Laptop | 999.99 |' . PHP_EOL;
        $expected .= '| Mouse | 29.99 |' . PHP_EOL;

        $this->assertSame($expected, $output);
    }

    public function testMixedAlignmentOverridesDefaultAlign(): void
    {
        $rows = [
            ['Name' => 'Alice', 'Age' => 30, 'Salary' => 50000],
            ['Name' => 'Bob', 'Age' => 25, 'Salary' => 45000],
        ];
        $stream = fopen('php://memory', 'r+');

        $markdownTable = new MarkdownTable(true, 'right', false, $stream);
        $markdownTable->alignLeft('Name')->alignRight('Salary');
        $markdownTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = '';
        $expected .= '| Name | Age | Salary |' . PHP_EOL;
        $expected .= '| ---- | ---: | ---: |' . PHP_EOL;
        $expected .= '| Alice | 30 | 50000 |' . PHP_EOL;
        $expected .= '| Bob | 25 | 45000 |' . PHP_EOL;

        $this->assertSame($expected, $output);
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
        $stream = fopen('php://memory', 'r+');
        $markdownTable = new MarkdownTable(true, $defaultAlign, false, $stream);

        foreach ($fieldsToTest as $index => $field) {
            $this->assertSame($expectedRightAligned[$index], $markdownTable->isRightAligned($field));
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
        $stream = fopen('php://memory', 'r+');
        $markdownTable = new MarkdownTable(true, 'left', false, $stream);

        // Set specific alignments
        if ($rightFields !== []) {
            $markdownTable->alignRight($rightFields);
        }

        if ($leftFields !== []) {
            $markdownTable->alignLeft($leftFields);
        }

        foreach ($tests as $field => $expected) {
            $this->assertSame($expected, $markdownTable->isRightAligned($field));
        }
    }

    public function testIsRightAlignedPriority(): void
    {
        $stream = fopen('php://memory', 'r+');
        $markdownTable = new MarkdownTable(true, 'right', false, $stream);

        // Default is right, but explicitly set left should override
        $markdownTable->alignLeft('Name');

        $this->assertFalse($markdownTable->isRightAligned('Name')); // Explicit left overrides default right
        $this->assertTrue($markdownTable->isRightAligned('Age')); // Uses default right

        // Now explicitly set Name to right again
        $markdownTable->alignRight('Name');
        $this->assertTrue($markdownTable->isRightAligned('Name')); // Explicit right
    }

    public function testFluentInterface(): void
    {
        $stream = fopen('php://memory', 'r+');
        $markdownTable = new MarkdownTable(true, 'left', false, $stream);

        $result = $markdownTable->alignRight('Age')->alignLeft('Name');
        $this->assertSame($markdownTable, $result);
    }
}

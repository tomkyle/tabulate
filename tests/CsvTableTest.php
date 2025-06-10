<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use tomkyle\Tabulate\CsvTable;

class CsvTableTest extends TestCase
{
    public function testInvokeOutputsCsvWithDefaultOptions(): void
    {
        $rows = [
            ['foo' => 'bar', 'baz' => 123],
            ['foo' => 'quux', 'baz' => 456],
        ];
        $stream = fopen('php://memory', 'r+');

        $csvTable = new CsvTable(stream: $stream);
        $csvTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = 'foo	baz' . PHP_EOL;
        $expected .= 'bar	123' . PHP_EOL;
        $expected .= 'quux	456' . PHP_EOL;

        $this->assertSame($expected, $output);
    }

    public function testInvokeWithoutHeaders(): void
    {
        $rows = [
            ['a' => 'b', 'c' => 'd'],
        ];
        $stream = fopen('php://memory', 'r+');

        $csvTable = new CsvTable(separator: "\t", enclosure: '"', escape: '\\', withHeaders: false, stream: $stream);
        $csvTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = 'b	d' . PHP_EOL;

        $this->assertSame($expected, $output);
    }

    public function testWriteToFile(): void
    {
        $rows = [
            ['Name' => 'Alice', 'Age' => 30],
            ['Name' => 'Bob', 'Age' => 25],
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_test_');
        $this->assertNotFalse($tempFile);

        $csvTable = new CsvTable(stream: $tempFile);
        $csvTable($rows);

        $this->assertFileExists($tempFile);
        $content = file_get_contents($tempFile);
        $this->assertIsString($content);

        // Check for headers and data
        $this->assertStringContainsString('Name', $content);
        $this->assertStringContainsString('Age', $content);
        $this->assertStringContainsString('Alice', $content);
        $this->assertStringContainsString('Bob', $content);

        unlink($tempFile);
    }

    public function testWriteToFileWithoutHeaders(): void
    {
        $rows = [
            ['Product' => 'Laptop', 'Price' => 999.99],
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_test_noheaders_');
        $this->assertNotFalse($tempFile);

        $csvTable = new CsvTable(withHeaders: false, stream: $tempFile);
        $csvTable($rows);

        $this->assertFileExists($tempFile);
        $content = file_get_contents($tempFile);
        $this->assertIsString($content);

        // Should not contain headers
        $this->assertStringNotContainsString('Product', $content);
        $this->assertStringNotContainsString('Price', $content);
        // But should contain data
        $this->assertStringContainsString('Laptop', $content);
        $this->assertStringContainsString('999.99', $content);

        unlink($tempFile);
    }

    public function testWriteToFileWithCustomSeparator(): void
    {
        $rows = [
            ['A' => '1', 'B' => '2'],
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'csv_test_separator_');
        $this->assertNotFalse($tempFile);

        $csvTable = new CsvTable(separator: ';', stream: $tempFile);
        $csvTable($rows);

        $this->assertFileExists($tempFile);
        $content = file_get_contents($tempFile);
        $this->assertIsString($content);

        $this->assertStringContainsString(';', $content);

        unlink($tempFile);
    }
}

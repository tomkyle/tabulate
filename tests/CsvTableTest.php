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
}

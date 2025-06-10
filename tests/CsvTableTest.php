<?php

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

        $expected = 'foo' . "\t" . 'baz' . PHP_EOL;
        $expected .= 'bar' . "\t" . '123' . PHP_EOL;
        $expected .= 'quux' . "\t" . '456' . PHP_EOL;

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

        $expected = 'b' . "\t" . 'd' . PHP_EOL;

        $this->assertSame($expected, $output);
    }
}
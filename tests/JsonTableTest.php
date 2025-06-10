<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use tomkyle\Tabulate\JsonTable;

class JsonTableTest extends TestCase
{
    public function testInvokeOutputsJsonWithDefaultOptions(): void
    {
        $rows = [
            ['foo' => 'bar', 'baz' => 123],
            ['foo' => 'quux', 'baz' => 456],
        ];
        $stream = fopen('php://memory', 'r+');

        $defaultOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;
        $jsonTable = new JsonTable($defaultOptions, $stream);
        $jsonTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);

        $expected = json_encode($rows, $defaultOptions) . PHP_EOL;

        $this->assertSame($expected, $output);
    }

    public function testSetOptionsReturnsSelfAndAffectsOutput(): void
    {
        $rows = [['key' => 'value']];
        $stream = fopen('php://memory', 'r+');

        $jsonTable = new JsonTable(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, $stream);
        $returned = $jsonTable->setOptions(0);

        $this->assertSame($jsonTable, $returned);

        $jsonTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);
        $expected = json_encode($rows, 0) . PHP_EOL;

        $this->assertSame($expected, $output);
    }
}

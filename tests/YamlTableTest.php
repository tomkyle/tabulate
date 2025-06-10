<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use tomkyle\Tabulate\YamlTable;
use Symfony\Component\Yaml\Yaml;

class YamlTableTest extends TestCase
{
    public function testInvokeOutputsYamlWithDefaultOptions(): void
    {
        $rows = [
            ['foo' => 'bar', 'baz' => 'qux'],
            ['foo' => 'quux', 'baz' => 'corge'],
        ];
        $stream = fopen('php://memory', 'r+');

        $yamlTable = new YamlTable(Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK, $stream);
        $yamlTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);
        $expected = Yaml::dump($rows, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);

        $this->assertSame($expected, $output);
    }

    public function testSetOptionsReturnsSelfAndAffectsOutput(): void
    {
        $rows = [['key' => 'value']];
        $stream = fopen('php://memory', 'r+');

        $yamlTable = new YamlTable(Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK, $stream);
        $returned = $yamlTable->setOptions(Yaml::DUMP_OBJECT_AS_MAP);

        $this->assertSame($yamlTable, $returned);

        $yamlTable($rows);

        rewind($stream);
        $output = stream_get_contents($stream);
        $expected = Yaml::dump($rows, 10, 2, Yaml::DUMP_OBJECT_AS_MAP);

        $this->assertSame($expected, $output);
    }

    public function testWriteToFile(): void
    {
        $rows = [
            ['Name' => 'Alice', 'Age' => 30],
            ['Name' => 'Bob', 'Age' => 25],
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'yaml_test_');
        $this->assertNotFalse($tempFile);

        $yamlTable = new YamlTable(stream: $tempFile);
        $yamlTable($rows);

        $this->assertFileExists($tempFile);
        $content = file_get_contents($tempFile);
        $this->assertIsString($content);

        // Check YAML structure
        $this->assertStringContainsString('Name: Alice', $content);
        $this->assertStringContainsString('Age: 30', $content);
        $this->assertStringContainsString('Name: Bob', $content);
        $this->assertStringContainsString('Age: 25', $content);

        unlink($tempFile);
    }

    public function testWriteToFileWithCustomOptions(): void
    {
        $rows = [
            ['Product' => 'Laptop with "quotes"'],
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'yaml_test_options_');
        $this->assertNotFalse($tempFile);

        $yamlTable = new YamlTable(yamlOptions: Yaml::DUMP_OBJECT_AS_MAP, stream: $tempFile);
        $yamlTable($rows);

        $this->assertFileExists($tempFile);
        $content = file_get_contents($tempFile);
        $this->assertIsString($content);

        $this->assertStringContainsString('Product:', $content);
        $this->assertStringContainsString('quotes', $content);

        unlink($tempFile);
    }
}

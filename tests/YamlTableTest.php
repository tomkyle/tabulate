<?php

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
}

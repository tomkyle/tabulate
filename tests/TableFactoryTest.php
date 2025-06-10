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
use tomkyle\Tabulate\TableFactory;
use tomkyle\Tabulate\MarkdownTable;
use tomkyle\Tabulate\CsvTable;
use tomkyle\Tabulate\YamlTable;
use tomkyle\Tabulate\JsonTable;
use tomkyle\Tabulate\SymfonyStyleTable;
use tomkyle\Tabulate\AlignableTableAbstract;
use tomkyle\Tabulate\StreamAwareInterface;

class TableFactoryTest extends TestCase
{
    public function testFromStringReturnsCorrectTableInstances(): void
    {
        $stream = fopen('php://memory', 'r+');
        $factory = new TableFactory(stream: $stream);

        $this->assertInstanceOf(MarkdownTable::class, $factory->fromString('markdown'));
        $this->assertInstanceOf(MarkdownTable::class, $factory->fromString('md'));
        $this->assertInstanceOf(CsvTable::class, $factory->fromString('csv'));
        $this->assertInstanceOf(YamlTable::class, $factory->fromString('yaml'));
        $this->assertInstanceOf(YamlTable::class, $factory->fromString('yml'));
        $this->assertInstanceOf(JsonTable::class, $factory->fromString('json'));
        $this->assertInstanceOf(SymfonyStyleTable::class, $factory->fromString('cli'));
        $this->assertInstanceOf(SymfonyStyleTable::class, $factory->fromString('symfony'));
    }

    public static function streamAwareClassesProvider(): array
    {
        return [
            [MarkdownTable::class],
            [CsvTable::class],
            [YamlTable::class],
            [JsonTable::class],
        ];
    }

    #[DataProvider('streamAwareClassesProvider')]
    public function testCertainClassesImplementStreamAwareInterface(string $className): void
    {
        $this->assertTrue(
            (new \ReflectionClass($className))->implementsInterface(StreamAwareInterface::class),
        );
    }

    public static function alignableTableClassesProvider(): array
    {
        return [
            [MarkdownTable::class],
            [SymfonyStyleTable::class],
        ];
    }

    #[DataProvider('alignableTableClassesProvider')]
    public function testCertainClassesAreInstanceOfAlignableTableAbstract(string $className): void
    {
        $this->assertTrue(
            (new \ReflectionClass($className))->isSubclassOf(AlignableTableAbstract::class),
        );
    }

    public function testFromStringSetsStreamOnCreatedTable(): void
    {
        $stream = fopen('php://memory', 'r+');
        $factory = new TableFactory(stream: $stream);
        $table = $factory->fromString('csv');

        $this->assertSame($stream, $table->getStream());
    }

    public function testFromStringWithInvalidTypeThrowsException(): void
    {
        $factory = new TableFactory();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid output format "invalid". Allowed values: csv, cli, yaml, json, symfony, markdown');
        $factory->fromString('invalid');
    }

    public function testFromStringWithInvalidDefaultAlignThrowsException(): void
    {
        $factory = new TableFactory();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid alignment specified. Use "left" or "right".');
        $factory->fromString('markdown', 'center');
    }

    public function testConstructorWithInvalidStreamThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid stream provided. Must be a valid resource.');
        new TableFactory(stream: 'not a stream');
    }
}

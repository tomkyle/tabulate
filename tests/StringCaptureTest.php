<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use tomkyle\Tabulate\StringCapture;
use tomkyle\Tabulate\StreamAwareInterface;
use tomkyle\Tabulate\TableInterface;

class StringCaptureTest extends TestCase
{
    public function testInvokeAndToString(): void
    {
        $rows = [['foo' => 'bar'], ['foo' => 'baz']];
        $stream = fopen('php://memory', 'r+');

        $dummyTable = new class implements TableInterface, StreamAwareInterface {
            use \tomkyle\Tabulate\StreamAwareTrait;

            public function __invoke(iterable $rows): void
            {
                fwrite($this->stream, json_encode($rows));
            }
        };

        $capture = new StringCapture($dummyTable, $stream);
        $capture($rows);

        $this->assertSame(json_encode($rows), (string) $capture);
    }

    public function testRenderReturnsString(): void
    {
        $rows = [['a' => 'b']];
        $stream = fopen('php://memory', 'r+');

        $dummyTable = new class implements TableInterface, StreamAwareInterface {
            use \tomkyle\Tabulate\StreamAwareTrait;

            public function __invoke(iterable $rows): void
            {
                fwrite($this->stream, 'rendered');
            }
        };

        $capture = new StringCapture($dummyTable, $stream);

        $this->assertSame('rendered', $capture->render($rows));
    }

    public function testToStringReturnsEmptyInitially(): void
    {
        $stream = fopen('php://memory', 'r+');
        $dummyTable = new class implements TableInterface, StreamAwareInterface {
            use \tomkyle\Tabulate\StreamAwareTrait;

            public function __invoke(iterable $rows): void {}
        };

        $capture = new StringCapture($dummyTable, $stream);

        $this->assertSame('', (string) $capture);
    }

    public function testMultipleInvokesOverwritePreviousContent(): void
    {
        $stream = fopen('php://memory', 'r+');
        $dummyTable = new class implements TableInterface, StreamAwareInterface {
            use \tomkyle\Tabulate\StreamAwareTrait;

            public function __invoke(iterable $rows): void
            {
                fwrite($this->stream, (string) $rows[0]['val']);
            }
        };

        $capture = new StringCapture($dummyTable, $stream);
        $capture([['val' => 'first']]);
        $this->assertSame('first', (string) $capture);

        $capture([['val' => 'second']]);
        $this->assertSame('second', (string) $capture);
    }
}

<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

class MarkdownTable extends AlignableTableAbstract implements TableInterface, StreamAwareInterface
{
    use StreamAwareTrait;

    /**
     * @param bool $withHeaders Whether to include headers in the output. Defaults to true.
     * @param string $defaultAlign The default alignment for fields. Can be 'left' or 'right'. Defaults to 'left'.
     * @param bool $autoAlign Automatically determine field alignment based on sample record. Defaults to true.
     * @param resource $stream The stream to write to. Defaults to STDOUT.
     */
    public function __construct(protected bool $withHeaders = true, string $defaultAlign = 'left', private bool $autoAlign = true, $stream = STDOUT)
    {
        $this->setStream($stream);
        $this->setDefaultAlign($defaultAlign);
    }


    /**
     * Outputs a Markdown table to the configured stream/file pointer.
     *
     * @param iterable<int|string,array<string,string>> $rows An iterable of associative arrays representing the rows of the table.
     */
    public function __invoke(iterable $rows): void
    {
        if (!is_array($rows)) {
            $rows = iterator_to_array($rows);
        }

        if ($this->autoAlign) {
            $sampleRecord = $rows[array_key_last($rows)] ?: [];
            $this->autoAlign($sampleRecord);
        }

        if ($this->withHeaders) {
            $headers = $this->extractHeaders($rows);
            fwrite($this->stream, '| ' . implode(' | ', $headers) . " |" . PHP_EOL);

            // Create a separator row with appropriate alignment for numeric fields
            $aligners = array_map(fn($field) => $this->isRightAligned($field) ? '---:' : '----', $headers);

            // Write the separator row
            fwrite($this->stream, '| ' . implode(' | ', $aligners) . " |" . PHP_EOL);
        }

        foreach ($rows as $row) {
            fwrite($this->stream, '| ' . implode(' | ', $row) . " |" . PHP_EOL);
        }
    }

}

<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

class CsvTable extends TableAbstract implements TableInterface, StreamAwareInterface
{
    use StreamAwareTrait;

    /**
     * @param string                 $separator   Optional: CSV separator. Defaults to tab.
     * @param string                 $enclosure   Optional: CSV enclosure character. Defaults to double quote.
     * @param string                 $escape      Optional: CSV escape character. Defaults to backslash.
     * @param bool                   $withHeaders Optional: Whether to include headers. Defaults to true.
     * @param resource               $stream      The stream to write to. Defaults to STDOUT.
     */
    public function __construct(public string $separator = "\t", public string $enclosure = '"', public string $escape = '\\', protected bool $withHeaders = true, $stream = STDOUT)
    {
        $this->setStream($stream);
    }


    /**
     * Outputs a CSV table to the configured stream/file pointer.
     *
     * @param iterable<int|string,array<string,string>> $rows An iterable of associative arrays representing the rows of the table.
     */
    public function __invoke(iterable $rows): void
    {
        if (!is_array($rows)) {
            $rows = iterator_to_array($rows);
        }

        $csvOptions = [
            'separator' => $this->separator,
            'enclosure' => $this->enclosure,
            'escape'    => $this->escape,
        ];

        if ($this->withHeaders) {
            $headers = $this->extractHeaders($rows);
            fputcsv($this->stream, $headers, ...$csvOptions);
        }


        foreach ($rows as $row) {
            fputcsv($this->stream, $row, ...$csvOptions);
        }
    }


}

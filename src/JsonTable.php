<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

class JsonTable extends TableAbstract implements TableInterface, StreamAwareInterface
{
    use StreamAwareTrait;

    /**
     * @param int $jsonOptions JSON encoding options. Defaults to JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR.
     * @param resource $stream The stream to write to. Defaults to STDOUT.
     */
    public function __construct(protected int $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, $stream = STDOUT)
    {
        $this->setStream($stream);
    }

    /**
     * Sets the JSON options for dumping.
     *
     * @param int $jsonOptions JSON dump options.
     */
    public function setOptions(int $jsonOptions): self
    {
        $this->jsonOptions = $jsonOptions;
        return $this;
    }

    /**
     * Outputs a CSV table to the configured stream/file pointer.
     *
     * @param iterable<int|string,array<string,string>> $rows An iterable of associative arrays representing the rows of the table.
     */
    public function __invoke(iterable $rows): void
    {
        $jsonResult = json_encode($rows, $this->jsonOptions);
        if ($jsonResult === false) {
            throw new \JsonException('Failed to encode data to JSON: ' . json_last_error_msg());
        }

        fwrite($this->stream, $jsonResult);
        fwrite($this->stream, PHP_EOL);
    }


}

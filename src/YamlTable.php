<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

use Symfony\Component\Yaml\Yaml;

class YamlTable extends TableAbstract implements TableInterface, StreamAwareInterface
{
    use StreamAwareTrait;

    /**
     * @param int<0, 64721> $yamlOptions YAML dump options. Defaults to Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK.
     * @param resource $stream The stream to write to. Defaults to STDOUT.
     */
    public function __construct(protected int $yamlOptions = Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK, $stream = STDOUT)
    {
        $this->setStream($stream);
    }


    /**
     * Sets the YAML options for dumping.
     *
     * @param int<0, 64721> $yamlOptions YAML dump options.
     */
    public function setOptions(int $yamlOptions): self
    {
        $this->yamlOptions = $yamlOptions;
        return $this;
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

        fwrite($this->stream, Yaml::dump($rows, 10, 2, $this->yamlOptions));
    }


}

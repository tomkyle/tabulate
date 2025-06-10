<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

class TableFactory implements StreamAwareInterface
{
    use StreamAwareTrait;

    /**
     * Constructor for the TableFactory class.
     *
     * @param string $defaultAlign The default alignment for the table columns, 'left' or  'right'.
     *                              This is used when creating tables that support alignment.
     * @param resource|string $stream The stream to write to. Defaults to STDOUT.
     */
    public function __construct(private string $defaultAlign = 'left', $stream = STDOUT)
    {
        $this->setStream($stream);
    }


    /**
     * Create a table formatter based on the specified type.
     *
     * @param string $type The type of table to create (e.g., 'markdown', 'md', 'csv', 'yaml', 'json', 'cli', 'symfony').
     * @param string $defaultAlign The default alignment for the table columns, if applicable.
     * @return TableInterface The created table formatter instance.
     * @throws \InvalidArgumentException If the specified type is not supported.
     */
    public function fromString(string $type, ?string $defaultAlign = null): TableInterface
    {
        $type = strtolower($type);

        switch ($type) {
            case 'markdown':
            case 'md':
                $table = new MarkdownTable();
                break;
            case 'csv':
                $table = new CsvTable();
                break;
            case 'yml':
            case 'yaml':
                $table = new YamlTable();
                break;
            case 'json':
                $table = new JsonTable();
                break;
            case 'cli':
            case 'symfony':
                $table = SymfonyStyleTable::fromConsole();
                break;
            default:
                $msg = sprintf('Invalid output format "%s". Allowed values: csv, cli, yaml, json, symfony, markdown', $type);
                throw new \InvalidArgumentException($msg);
        }

        if ($table instanceof StreamAwareInterface) {
            $table->setStream($this->getStream());
        }

        if ($table instanceof AlignableTableAbstract) {
            $table->setDefaultAlign($defaultAlign ?: $this->defaultAlign);
        }

        return $table;
    }
}

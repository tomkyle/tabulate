<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

abstract class TableAbstract implements TableInterface
{
    /**
     * @inheritDoc
     */
    abstract public function __invoke(iterable $rows): void;

    /**
     * Extracts headers from the provided rows.
     *
     * This method collects the keys from each associative array in the rows
     * and returns a unique list of headers. It is useful for generating table headers
     * when the rows are not guaranteed to have the same keys or structure.
     *
     * @param array<string,mixed> $rows An array of associative arrays representing the rows of the table.
     * @return array<int,string|int> An array of headers.
     */
    public function extractHeaders(array $rows): array
    {
        $collectedKeys = array_map('array_keys', $rows);
        return array_values(array_unique(array_merge(...$collectedKeys)));
    }
}

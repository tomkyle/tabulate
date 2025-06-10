<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

interface TableInterface
{
    /**
     * Output a formatted table.
     *
     * @param iterable<int|string,array<string,string>> $rows An iterable of associative arrays representing the rows of the table.
     */
    public function __invoke(iterable $rows): void;
}

<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

interface StreamAwareInterface
{
    /**
     * Sets the stream or file pointer to write the formatted output to.
     *
     * @param resource $stream The stream or file pointer to write to.
     */
    public function setStream($stream): self;
}

<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

trait StreamAwareTrait
{
    /**
     * @var mixed $stream The stream resource associated with this formatter.
     */
    private $stream;


    /**
     * Returns the stream resource associated with this formatter.
     *
     * @return mixed The stream resource, or null if not set.
     */
    public function getStream(): mixed
    {
        return $this->stream;
    }

    /**
     * Sets the stream resource for this formatter.
     *
     * @param mixed $stream The stream resource to set. Must be a valid resource.
     * @return self Returns the current instance for method chaining.
     * @throws \InvalidArgumentException If the provided stream is not a valid resource.
     */
    public function setStream($stream): self
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Invalid stream provided. Must be a valid resource.');
        }

        $this->stream = $stream;
        return $this;
    }
}

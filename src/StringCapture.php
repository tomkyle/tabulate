<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

class StringCapture implements TableInterface, \Stringable, StreamAwareInterface
{
    use StreamAwareTrait;

    private StreamAwareInterface&TableInterface $table;

    /**
     * @param StreamAwareInterface&TableInterface $table The table instance to wrap.
     * @param string|resource $stream The stream to write to. For capturing string, default value 'php://memory' makes sense.
     */
    public function __construct(StreamAwareInterface&TableInterface $table, $stream = 'php://memory')
    {
        $this->table = $table;

        if (is_string($stream)) {
            $resource = fopen($stream, 'r+');
            if ($resource === false) {
                throw new \InvalidArgumentException('Unable to open stream: ' . $stream);
            }
        } else {
            $resource = $stream;
        }

        $this->setStream($resource);
        $table->setStream($this->stream);
    }


    /**
     * Sets the stream resource for this formatter.
     *
     * @param resource|string $stream The stream resource to set. Must be a valid resource.
     * @return self Returns the current instance for method chaining.
     * @throws \InvalidArgumentException If the provided stream is not a valid resource.
     */
    public function setStream($stream): self
    {
        if (is_string($stream)) {
            $stream = fopen($stream, 'w');
        }

        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Invalid stream provided. Must be a valid resource.');
        }

        $this->stream = $stream;
        $this->table->setStream($this->stream);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function __invoke(iterable $rows): void
    {
        // Reset stream position
        rewind($this->stream);
        ftruncate($this->stream, 0);

        // Render table to memory stream
        ($this->table)($rows);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        rewind($this->stream);
        $content = stream_get_contents($this->stream);
        return $content !== false ? $content : '';
    }

    /**
     * Renders the table and returns the output as string.
     *
     * @param iterable<int|string,array<string,string>> $rows
     */
    public function render(iterable $rows): string
    {
        $this($rows);
        return (string) $this;
    }

    public function __destruct()
    {
        fclose($this->stream);
    }

    /**
     * Static factory method for fluent usage.
     */
    public static function wrap(StreamAwareInterface&TableInterface $table): self
    {
        return new self($table);
    }
}

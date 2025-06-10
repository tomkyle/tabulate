<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

abstract class AlignableTableAbstract extends TableAbstract implements TableInterface
{
    /**
     * @var array<int|string> $alignRight
     */
    protected array $alignRight = [];

    /**
     * @var array<int|string> $alignLeft
     */
    protected array $alignLeft = [];

    protected string $defaultAlign = 'left';


    /**
     * Sets the default alignment for fields in the table.
     *
     * @param string $align The alignment to set. Accepts 'left' or 'right'.
     * @return self Fluid interface
     * @throws \InvalidArgumentException If an invalid alignment is specified.
     */
    public function setDefaultAlign(string $align): self
    {
        if (!in_array($align, ['left', 'right'])) {
            throw new \InvalidArgumentException('Invalid alignment specified. Use "left" or "right".');
        }

        $this->defaultAlign = $align;
        return $this;
    }


    /**
     * Aligns specified fields to the right.
     *
     * This method allows you to specify one or more fields that should be aligned to the right in the table.
     * If a field is already aligned to the right, it will not be added again.
     *
     * @param array|string $fields The field name(s) to align to the right. Can be a single field or an array of fields.
     * @return self Fluid interface
     */
    public function alignRight(array|string $fields): self
    {
        $fields = is_array($fields) ? $fields : [$fields];
        foreach ($fields as $field) {
            if (!in_array($field, $this->alignRight)) {
                $this->alignRight[] = $field;
            }
            // Remove from left alignment if it was set
            $this->alignLeft = array_values(array_filter($this->alignLeft, fn($f) => $f !== $field));
        }
        return $this;
    }


    /**
     * Aligns specified fields to the left.
     *
     * This method allows you to specify one or more fields that should be aligned to the left in the table.
     * If a field is already aligned to the left, it will not be added again.
     *
     * @param array|string $fields The field name(s) to align to the left. Can be a single field or an array of fields.
     * @return self Fluid interface
     */
    public function alignLeft(array|string $fields): self
    {
        $fields = is_array($fields) ? $fields : [$fields];
        foreach ($fields as $field) {
            if (!in_array($field, $this->alignLeft)) {
                $this->alignLeft[] = $field;
            }
            // Remove from right alignment if it was set
            $this->alignRight = array_values(array_filter($this->alignRight, fn($f) => $f !== $field));
        }
        return $this;
    }


    /**
     * Checks if a field is aligned to the right.
     *
     * @param string|int $field The field name to check.
     * @return bool Returns true if the field is right-aligned, false otherwise.
     */
    public function isRightAligned(string|int $field): bool
    {
        return ($this->defaultAlign === "right" || in_array($field, $this->alignRight)) && !in_array($field, $this->alignLeft);
    }


    /**
     * Automatically determines the alignment of fields based on a sample record.
     *
     * This method inspects the values of the sample record and classifies fields
     * as either numeric (right-aligned) or non-numeric (left-aligned).
     *
     * The sample record should be an associative array where keys are field names
     * and values are the corresponding values. Numeric fields will be aligned to the right,
     * while non-numeric fields will be aligned to the left.
     *
     * @param array<int|string,mixed> $sampleRecord Sample record
     * @return self Fluid interface
     */
    public function autoAlign(array $sampleRecord): self
    {
        foreach ($sampleRecord as $field => $value) {
            if (is_numeric($value)) {
                $this->alignRight[] = $field;
            } else {
                $this->alignLeft[] = $field;
            }
        }

        return $this;
    }
}

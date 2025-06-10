<?php

/**
 * This file is part of tomkyle/tabulate
 *
 * Format 2D arrays as CLI console table, Markdown, CSV, YAML, JSON.
 */

namespace tomkyle\Tabulate;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\OutputInterface;

class SymfonyStyleTable extends AlignableTableAbstract implements TableInterface
{
    /**
     * @param SymfonyStyle $io The SymfonyStyle instance to use for output.
     * @param bool $withHeaders Whether to include headers in the output. Defaults to true.
     * @param string $defaultAlign The default alignment for fields. Can be 'left' or 'right'. Defaults to 'left'.
     * @param bool $autoAlign Automatically determine field alignment based on sample record. Defaults to true.
     */
    public function __construct(protected SymfonyStyle $io, protected bool $withHeaders = true, string $defaultAlign = 'left', private readonly bool $autoAlign = true)
    {
        $this->setDefaultAlign($defaultAlign);
    }


    /**
     * Creates a new instance of SymfonyStyleTable using the provided Input and Output interfaces.
     *
     * @param InputInterface|null $input The input interface to use. Defaults to ArgvInput if not provided.
     * @param OutputInterface|null $output The output interface to use. Defaults to StreamOutput with STDOUT if not provided.
     * @param bool $withHeaders Whether to include headers in the output. Defaults to true.
     * @param string $defaultAlign The default alignment for fields. Can be 'left' or 'right'. Defaults to 'left'.
     * @param bool $autoAlign Automatically determine field alignment based on sample record. Defaults to true.
     * @return self A new instance of SymfonyStyleTable.
     */
    public static function fromConsole(?InputInterface $input = null, ?OutputInterface $output = null, bool $withHeaders = true, string $defaultAlign = 'left', bool $autoAlign = true): self
    {
        $input = $input ?: new ArgvInput();
        $output = $output ?: new StreamOutput(STDOUT);
        $symfonyStyle  = new SymfonyStyle($input, $output);

        return new self($symfonyStyle, $withHeaders, $defaultAlign, $autoAlign);
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

        if ($this->autoAlign) {
            $sampleRecord = $rows[array_key_last($rows)] ?: [];
            $this->autoAlign($sampleRecord);
        }

        $headers = $this->extractHeaders($rows);

        $table = $this->io->createTable();
        $table->setStyle('compact');
        $table->setRows($rows);

        if ($this->withHeaders) {
            $table->setHeaders($headers);
            $table->getStyle()->setCellHeaderFormat('<options=bold>%s</>');
        }

        $this->applyAlignment($table, $headers);

        $table->render();
    }


    /**
     * Applies alignment styles to the table columns based on the headers.
     *
     * This method sets the alignment for each column based on whether the field is right-aligned or left-aligned.
     * It uses the default alignment style for left-aligned fields and a modified style for right-aligned fields.
     *
     * @param array<int,string|int> $headers The headers of the table.
     */
    private function applyAlignment(Table $table, array $headers): void
    {
        // Apply left alignment for specific fields
        $leftAlignStyle = clone $table->getStyle();
        $leftAlignStyle->setPadType(STR_PAD_RIGHT);

        $rightAlignStyle = clone $table->getStyle();
        $rightAlignStyle->setPadType(STR_PAD_LEFT);

        foreach ($headers as $idx => $field) {
            $alignStyle = $this->isRightAligned($field)
                        ? $rightAlignStyle
                        : $leftAlignStyle;
            $table->setColumnStyle($idx, $alignStyle);
        }
    }

}

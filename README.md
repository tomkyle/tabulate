# tomkyle/tabulate

[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/tomkyle/tabulate.svg?style=flat)](https://packagist.org/packages/tomkyle/tabulate)
[![PHP version](https://img.shields.io/packagist/php-v/tomkyle/tabulate.svg)](https://packagist.org/packages/tomkyle/tabulate)
[![Tests](https://github.com/tomkyle/tabulate/actions/workflows/tests.yml/badge.svg)](https://github.com/tomkyle/tabulate/actions/workflows/tests.yml)


A library for formatting two-dimensional associative arrays as tables in various output formats: CLI console, Markdown, CSV, YAML, and JSON.


## Overview

Tabulate provides a set of formatter classes that render two-dimensional associative arrays (rows of records) into various output formats. Factory class `TableFactory` dynamically creates the appropriate formatter based on a format string which is useful in CLI scripts providing  a `--format` option.

| Format | Factory String | Description |
|--------|----------------|-------------|
| **SymfonyStyleTable** | `cli`, `symfony` | CLI table with Symfony Console styling |
| **MarkdownTable** | `markdown`, `md` | Markdown-formatted table |
| **CsvTable** | `csv` | Comma-Separated Values |
| **YamlTable** | `yaml`, `yml` | YAML document |
| **JsonTable** | `json` | JSON document |

### Column alignment

The `MarkdownTable` and `SymfonyStyleTable` formatters support *automatic* and *manual* column alignment based on data types (numeric values are right-aligned, text is left-aligned) with options to override specific columns.


## Installation

Install via Composer:

```bash
composer require tomkyle/tabulate
```

## Requirements

- PHP 8.3 or higher
- Symfony Console component for `SymfonyStyleTable`
- Symfony Yaml component for `YamlTable`


## Basic Usage

```php
use tomkyle\Tabulate\TableFactory;

// Sample data: rows of associative arrays
$data = [
    ['Name' => 'Alice', 'Age' => 30],
    ['Name' => 'Bob',   'Age' => 25],
];

// Create a TableFactory (default align 'left', output to STDOUT)
$factory = new TableFactory();

// Render as Markdown table
$factory->fromString('markdown')($data);

// Render as CSV
$factory->fromString('csv')($data);

// Or use a formatter directly
(new \tomkyle\Tabulate\JsonTable())($data);
```


## API Reference

### TableFactory

Factory for creating table formatters by type (`markdown`, `csv`, `yaml`, `json`, `cli`, `symfony`).

```php
public function __construct(
	string   $defaultAlign = 'left', 
	resource $stream = STDOUT);

public function fromString(
	string $type, 
	?string $defaultAlign = null): TableInterface;
```


### Output invocation

All table formatters implement the `TableInterface`, which defines a single method `__invoke(array $rows): void` to render the table.

```php
namespace tomkyle\Tabulate;

interface TableInterface {
	public function __invoke(array $rows): void;
}
```

In order to display the table, you can call the formatter instance with an array of rows, where each row is an associative array representing a record.

```php
$formatter = $tableFactory->fromString('markdown');

$formatter([
    ['Name' => 'Alice', 'Age' => 30],
    ['Name' => 'Bob',   'Age' => 25],
]);
```

### Aligning columns

Column alignment enhances table readability by aligning data appropriately. Alignment features are available for `MarkdownTable` and `SymfonyStyleTable` formatters, which extend the `AlignableTableAbstract` class.

#### Auto-alignment (default behavior)

Tables automatically determine column alignment based on data types:
- **Numeric values** (integers, floats) → right-aligned
- **Non-numeric values** (strings, etc.) → left-aligned

```php
$data = [
    ['Product' => 'Laptop', 'Price' => 999.99, 'Stock' => 15],
    ['Product' => 'Mouse',  'Price' => 29.99,  'Stock' => 150],
];

$factory = new TableFactory();
$factory->fromString('markdown')($data);
// Result:
// | Product | Price | Stock |
// | ----    | ---:  | ---:  |
// | Laptop  | 999.99| 15    |
// | Mouse   | 29.99 | 150   |
```

#### Manual column alignment

Override auto-alignment by explicitly setting column alignment:

```php
use tomkyle\Tabulate\MarkdownTable;

$table = new MarkdownTable(autoAlign: false);

// Align specific columns to the right
$table->alignRight(['Price', 'Stock']);

// Align specific columns to the left  
$table->alignLeft('Product');

// Method chaining is supported
$table->alignRight('Price')->alignLeft(['Product', 'Name']);
```

#### Default alignment

Set a default alignment for all columns, then override specific ones:

```php
$table = new MarkdownTable();

// Set default alignment for all columns
$table->setDefaultAlign('right'); // 'left' or 'right'

// Override specific columns
$table->alignLeft('Product'); // Product will be left-aligned, others right-aligned
```

#### Checking alignment

Query the current alignment of any column:

```php
if ($table->isRightAligned('Price')) {
    echo "Price column is right-aligned";
}

if (!$table->isRightAligned('Product')) {
    echo "Product column is left-aligned";
}
```

#### Constructor configuration

Configure alignment behavior when creating table instances:

```php
// MarkdownTable with custom alignment settings
$markdownTable = new MarkdownTable(
    withHeaders: true,
    defaultAlign: 'right',  // Default alignment for all columns
    autoAlign: false        // Disable automatic alignment detection
);

// SymfonyStyleTable with alignment
$symfonyTable = SymfonyStyleTable::fromConsole(
    defaultAlign: 'left',   // Default alignment
    autoAlign: true         // Enable auto-alignment (default)
);
```

#### Alignment priority

Alignment is determined in the following order of priority:

1. **Explicit field alignment** (via `alignRight()`/`alignLeft()`)
2. **Auto-alignment** (if enabled, based on data types)  
3. **Default alignment** (set via `setDefaultAlign()`)

```php
$table = new MarkdownTable(defaultAlign: 'right');
$table->alignLeft('Name');  // Explicit alignment overrides default

// Result: 'Name' is left-aligned, other columns use default (right)
```

### Available formatters

#### SymfonyStyleTable

Renders a table using Symfony Console styling.

```php
public static function fromConsole(
	?InputInterface $input = null, 
	?OutputInterface $output = null, 
	bool $withHeaders = true, 
	string $defaultAlign = 'left', 
	bool $autoAlign = true): self;

public function __construct(
	SymfonyStyle $io, 
	bool $withHeaders = true, 
	string $defaultAlign = 'left', 
	bool $autoAlign = true);
```


#### MarkdownTable

Renders a Markdown-formatted table.

```php
public function __construct(
	bool     $withHeaders = true, 
	string   $defaultAlign = 'left', 
	bool     $autoAlign = true, 
	resource $stream = STDOUT);
```


#### CsvTable

Renders a CSV table.

```php
public function __construct(
	string   $separator = "\t", 
	string   $enclosure = '"', 
	string   $escape = '\\', 
	bool     $withHeaders = true, 
	resource $stream = STDOUT);
```


#### YamlTable

Renders a YAML document.

```php
public function __construct(
	int $yamlOptions = Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK, 
	resource $stream = STDOUT);
```


#### JsonTable

Renders a JSON document.

```php
public function __construct(
	int $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, 
	resource $stream = STDOUT)
```




## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

### Guidelines

- Follow PER-CS coding standards
- Add tests for any new features
- Update documentation as needed
- Ensure all tests pass

# Development

This project follows PER-CS coding standards and includes:

- PHPUnit for testing
- PHPStan for static analysis  
- PHP-CS-Fixer for code formatting
- Rector for automated refactoring
- File watching for continuous development

### Setup Development Environment

```bash
git clone https://github.com/tomkyle/tabulate.git
cd tabulate
composer install
npm install
```

### Development Workflow

The project uses *npm* scripts for development tasks:

```bash
# Watch files for changes (runs PHPStan, Rector, and tests automatically)
npm run watch

# Code quality tools
npm run phpcs          # Check code style (dry-run)
npm run phpcs:apply    # Fix code style

npm run phpstan        # Run static analysis

npm run rector         # Check for refactoring suggestions (dry-run)
npm run rector:apply   # Apply refactoring suggestions

# Testing
npm run phpunit        # Run tests with coverage
npm run phpunit:short  # Run tests without coverage
```

### File Watching

The watch command monitors source and test files for changes and automatically runs the appropriate tools:

- **Source files** (`src/**/*.php`): Runs PHP-CS-Fixer, PHPStan, and Rector
- **Test files** (`tests/**/*.php`): Runs PHPUnit tests

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Changelog

All notable changes to this project will be documented in the [CHANGELOG.md](CHANGELOG.md) file.

## Support

- [GitHub Issues](https://github.com/tomkyle/tabulate/issues) for bug reports and feature requests
- [GitHub Discussions](https://github.com/tomkyle/tabulate/discussions) for questions and community support


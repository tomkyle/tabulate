# tomkyle/tabulate

[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/tomkyle/tabulate.svg?style=flat)](https://packagist.org/packages/tomkyle/tabulate)
[![PHP version](https://img.shields.io/packagist/php-v/tomkyle/tabulate.svg)](https://packagist.org/packages/tomkyle/tabulate)
[![Tests](https://github.com/tomkyle/tabulate/actions/workflows/php.yml/badge.svg)](https://github.com/tomkyle/tabulate/actions/workflows/php.yml)

Format 2D arrays as CLI tables, Markdown, CSV, YAML, and JSON.

## Requirements

- PHP 8.3 or higher
- Symfony Console component for `SymfonyStyleTable`
- Symfony Yaml component for `YamlTable`

## Installation

Install via Composer:

```bash
composer require tomkyle/tabulate
```

## Quick Start

```bash
composer require tomkyle/tabulate
```

```php
use tomkyle\Tabulate\TableFactory;

$data = [
    ['Name' => 'Alice', 'Age' => 30],
    ['Name' => 'Bob',   'Age' => 25],
];

$factory = new TableFactory();
$factory->fromString('markdown')($data);
```

## Features

- **Multiple formats**: CLI, Markdown, CSV, YAML, JSON
- **Column alignment**: Auto-detection and manual override  
- **Stream output**: Direct file writing, memory-efficient processing
- **Factory pattern**: Dynamic formatter creation for your CLI script with `--format` option support

## Documentation

- [Basic Usage](docs/basic-usage.md)
- [Stream Output](docs/streams.md)
- [Column Alignment](docs/alignment.md)
- [API Reference](docs/api.md)
- [Contributing](docs/contributing.md)

## License

MIT License - see [LICENSE](LICENSE) file.

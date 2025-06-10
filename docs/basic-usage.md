# Basic Usage

## TableFactory

The recommended approach for creating table formatters:

```php
use tomkyle\Tabulate\TableFactory;

$data = [
    ['Name' => 'Alice', 'Age' => 30],
    ['Name' => 'Bob',   'Age' => 25],
];

$factory = new TableFactory();

// Console output
$factory->fromString('markdown')($data);
$factory->fromString('csv')($data);
$factory->fromString('json')($data);

// File output
$factory = new TableFactory(stream: 'report.md');
$factory->fromString('markdown')($data);
```

## Format Types

| Format | Factory Strings | Output |
|--------|----------------|---------|
| **CLI Table** | `cli`, `symfony` | Symfony Console styled table |
| **Markdown** | `markdown`, `md` | GitHub-compatible Markdown table |
| **CSV** | `csv` | Comma-separated values |
| **YAML** | `yaml`, `yml` | YAML document |
| **JSON** | `json` | JSON array |

## Direct Formatter Usage

You can also use formatters directly:

```php
use tomkyle\Tabulate\MarkdownTable;
use tomkyle\Tabulate\JsonTable;
use tomkyle\Tabulate\CsvTable;
use tomkyle\Tabulate\YamlTable;

// To console
(new MarkdownTable())($data);

// To file
(new JsonTable(stream: 'data.json'))($data);
```

## String Output with StringCapture

For stream-aware formatters that normally output to streams, use `StringCapture` which works with any formatter class implementing `StreamAwareInterface`:

- MarkdownTable
- JsonTable
- CsvTable
- YamlTable

```php
use tomkyle\Tabulate\StringCapture;
use tomkyle\Tabulate\MarkdownTable;

$capture = StringCapture::wrap(new MarkdownTable());
$capture($data);
$result = (string) $capture; // Get as string

// Or use the render method
$result = StringCapture::wrap(new MarkdownTable())->render($data);
```

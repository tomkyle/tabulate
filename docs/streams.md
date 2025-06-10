# Stream Output

Stream-aware formatters (`MarkdownTable`, `CsvTable`, `YamlTable`, `JsonTable`) use PHP streams for flexible, memory-efficient output. Advantages:

- **Memory efficient**: Large datasets don't require full output in memory
- **Real-time processing**: Output appears immediately
- **Flexible destinations**: Console, files, HTTP responses, or any PHP stream
- **Pipeline integration**: Can be piped to other processes
- **Resource management**: PHP handles buffering automatically

## Output Destinations

### Console Output (Default)

```php
$table = new MarkdownTable();
$table($data); // Outputs to STDOUT
```

### File Output

**Using file path (recommended):**
```php
$table = new MarkdownTable(stream: 'report.md');
$table($data); // Creates/overwrites report.md
```

**Using file handle:**
```php
$file = fopen('report.md', 'w');
$table = new MarkdownTable(stream: $file);
$table($data);
fclose($file);
```

### Memory Capture

```php
$memory = fopen('php://memory', 'r+');
$table = new CsvTable(stream: $memory);
$table($data);

rewind($memory);
$content = stream_get_contents($memory);
fclose($memory);
```

### HTTP Response Streaming

```php
$table = new CsvTable(stream: fopen('php://output', 'w'));
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export.csv"');
$table($data); // Streams directly to browser
```

## Using TableFactory with Streams

```php
use tomkyle\Tabulate\TableFactory;

// Console output
$factory = new TableFactory();
$factory->fromString('markdown')($data);

// File output
$factory = new TableFactory(stream: 'report.csv');
$factory->fromString('csv')($data);

// Custom stream
$factory = new TableFactory(stream: fopen('data.json', 'w'));
$factory->fromString('json')($data);
```

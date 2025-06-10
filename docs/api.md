# API Reference

## TableFactory

Factory for creating table formatters by type.

```php
public function __construct(
    string $defaultAlign = 'left', 
    resource|string $stream = STDOUT
);

public function fromString(
    string $type, 
    ?string $defaultAlign = null
): TableInterface;
```

## TableInterface

All formatters implement this interface:

```php
namespace tomkyle\Tabulate;

interface TableInterface {
    public function __invoke(iterable $rows): void;
}
```

## StringCapture

Captures stream output as strings:

```php
public function __construct(StreamAwareInterface&TableInterface $table);
public function __invoke(iterable $rows): void;
public function __toString(): string;
public function render(iterable $rows): string;
public static function wrap(StreamAwareInterface&TableInterface $table): self;
```

## Formatters

### SymfonyStyleTable

```php
public static function fromConsole(
    ?InputInterface $input = null, 
    ?OutputInterface $output = null, 
    bool $withHeaders = true, 
    string $defaultAlign = 'left', 
    bool $autoAlign = true
): self;

public function __construct(
    SymfonyStyle $io, 
    bool $withHeaders = true, 
    string $defaultAlign = 'left', 
    bool $autoAlign = true
);
```

### MarkdownTable

```php
public function __construct(
    bool $withHeaders = true, 
    string $defaultAlign = 'left', 
    bool $autoAlign = true, 
    resource|string $stream = STDOUT
);
```

### CsvTable

```php
public function __construct(
    string $separator = "\t", 
    string $enclosure = '"', 
    string $escape = '\\', 
    bool $withHeaders = true, 
    resource|string $stream = STDOUT
);
```

### YamlTable

```php
public function __construct(
    int $yamlOptions = Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK, 
    resource|string $stream = STDOUT
);
```

### JsonTable

```php
public function __construct(
    int $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, 
    resource|string $stream = STDOUT
);
```

## Alignment Methods (MarkdownTable & SymfonyStyleTable)

```php
public function setDefaultAlign(string $align): self;
public function alignRight(array|string $fields): self;
public function alignLeft(array|string $fields): self;
public function isRightAligned(string|int $field): bool;
public function autoAlign(array $sampleRecord): self;
```

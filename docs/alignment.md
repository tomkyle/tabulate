# Column Alignment

Column alignment is available for `MarkdownTable` and `SymfonyStyleTable` formatters.

## Alignment Priority

1. **Explicit field alignment** (via `alignRight()`/`alignLeft()`)
2. **Auto-alignment** (if enabled, based on data types)
3. **Default alignment** (set via `setDefaultAlign()`)

```php
$table = new MarkdownTable(defaultAlign: 'right');
$table->alignLeft('Name'); // Explicit overrides default
// Result: 'Name' is left-aligned, others use default (right)
```

## Auto-alignment (Default)

Tables automatically align columns based on data types:
- **Numeric values** → right-aligned
- **Non-numeric values** → left-aligned

```php
$data = [
    ['Product' => 'Laptop', 'Price' => 999.99, 'Stock' => 15],
    ['Product' => 'Mouse',  'Price' => 29.99,  'Stock' => 150],
];

$factory = new TableFactory();
$factory->fromString('markdown')($data);
```

Output:
```markdown
| Product | Price | Stock |
| ----    | ---:  | ---:  |
| Laptop  | 999.99| 15    |
| Mouse   | 29.99 | 150   |
```

## Manual Alignment

Override auto-alignment with explicit settings:

```php
use tomkyle\Tabulate\MarkdownTable;

$table = new MarkdownTable(autoAlign: false);

// Align specific columns
$table->alignRight(['Price', 'Stock']);
$table->alignLeft('Product');

// Method chaining
$table->alignRight('Price')->alignLeft(['Product', 'Name']);
```

## Default Alignment

Set a default for all columns, then override specific ones:

```php
$table = new MarkdownTable();
$table->setDefaultAlign('right'); // 'left' or 'right'
$table->alignLeft('Product'); // Override for specific column
```

## Checking Alignment

Query current alignment state:

```php
if ($table->isRightAligned('Price')) {
    echo "Price column is right-aligned";
}
```

## Constructor Configuration

```php
// MarkdownTable with custom alignment
$table = new MarkdownTable(
    withHeaders: true,
    defaultAlign: 'right',
    autoAlign: false
);

// SymfonyStyleTable
$table = SymfonyStyleTable::fromConsole(
    defaultAlign: 'left',
    autoAlign: true
);
```


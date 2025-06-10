# Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Guidelines

- Follow PER-CS coding standards
- Add tests for any new features
- Update documentation as needed
- Ensure all tests pass

## Development Setup

```bash
git clone https://github.com/tomkyle/tabulate.git
cd tabulate
composer install
npm install
```

## Development Workflow

The project uses npm scripts for development tasks:

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

## File Watching

The watch command monitors source and test files for changes and automatically runs the appropriate tools:

- **Source files** (`src/**/*.php`): Runs PHP-CS-Fixer, PHPStan, and Rector
- **Test files** (`tests/**/*.php`): Runs PHPUnit tests

## Quality Standards

This project follows PER-CS coding standards and includes:

- PHPUnit for testing
- PHPStan for static analysis  
- PHP-CS-Fixer for code formatting
- Rector for automated refactoring
- File watching for continuous development

## Submitting Changes

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all quality checks pass
6. Submit a pull request

For major changes, please open an issue first to discuss what you would like to change.

<?php

use Rector\Core\Configuration\Option;
use Rector\ValueObject\PhpVersion;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codingStyle: true,
        codeQuality: true
    )

    ;

# Mmonolog Ray Handler

A [Monolog](https://github.com/Seldaek/monolog) handler for [Ray](https://github.com/spatie/ray)

## Install

```bash
composer require n5s/monolog-ray-handler
```

## Usage

```php
<?php

use Monolog\Level;
use Monolog\Logger;
use n5s\MonologRayHandler\RayHandler;

$logger = new Logger('ray-channel');
$logger->pushHandler(new RayHandler(Level::Warning));

// Add records to the log
$logger->debug('Foo');
$logger->error('Bar');
$logger->critical('Logger', ['logger' => $logger]);
```

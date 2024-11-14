
# Official Telex SDK for Laravel
This is the official Laravel SDK for [Telex](https://telex.im).

## Features

- **Request Metrics Collection**: Track HTTP request metrics including endpoint, latency, status codes, and HTTP method.
- **Error Reporting**: Automatically capture panics and errors, report them to a webhook URL, and optionally rethrow panics based on configuration.
- **Performance Metrics Collection**: Monitor application performance metrics such as memory usage, CPU usage, garbage collection cycles, and goroutine count.
- **Customizable**: Supports configurable options such as timeouts and synchronous/asynchronous metric delivery.
- **Flexible Status Handling**: Categorize requests as `success` (for 2xx status codes) or `error` (for 3xx, 4xx, and 5xx status codes).

## Getting Started

The installation steps below work on version 11.x of the Laravel framework.

Install the php
Install the composer 
run the code '''composer global require laravel/installer'''

For older Laravel versions see:[laravel installation doc](https://laravel.com/docs/11.x/installation#installing-php)

### Install

Install the `Telex/telex-laravel` package:

```bash
composer require sentry/sentry-laravel
```

Enable capturing unhandled exception to report to Sentry by making the following change to your `bootstrap/app.php`:

```php {filename:bootstrap/app.php}
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
```

> Alternatively, you can configure Sentry as a [Laravel Log Channel](https://docs.sentry.io/platforms/php/guides/laravel/usage/#log-channels), allowing you to capture `info` and `debug` logs as well.

### Configure

Configure the Telex APM with this command:

```shell
php artisan vendor:publish --provider="TelexAPM\APMServiceProvider" --tag="config"
```

It creates the config file (`config/sentry.php`) WITH your `.env` file.

### Usage

```php
use function telex\captureException;

try {
    $this->functionThatMayFail();
} catch (\Throwable $exception) {
    captureException($exception);
}
```

To learn more about how to use the SDK [refer to our docs](https://docs.sentry.io/platforms/php/guides/laravel/).
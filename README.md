# Nova Menu Builder

This [Laravel Nova](https://nova.laravel.com) package allows you to create and manage pages and regions. The package is geared towards headless CMS's.

## Features

TODO

## Screenshots

TODO

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require optimistdigital/nova-page-manager
```

Publish the database migration(s) and run migrate:

```bash
php artisan vendor:publish --tag=migrations
php artisan migrate
```

Register the tool with Nova in the `tools()` method of the `NovaServiceProvider`:

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \OptimistDigital\NovaPageManager\NovaPageManager,
    ];
}
```

## Usage

TODO

## Credits

- [Tarvo Reinpalu](https://github.com/Tarpsvo)

## License

Nova page manager is open-sourced software licensed under the [MIT license](LICENSE.md).

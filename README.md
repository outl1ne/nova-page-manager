# Nova Menu Builder

This [Laravel Nova](https://nova.laravel.com) package allows you to create and manage pages and regions. The package is geared towards headless CMS's.

## Features

- Pages and Regions management
- Programmatically created templates for Pages and Regions
- Multilanguage support - each page and region has a defined locale

## Screenshots

TODO

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require optimistdigital/nova-page-manager
```

Publish the database migration(s) and run migrate:

```bash
php artisan vendor:publish --provider="OptimistDigital\NovaPageManager\ToolServiceProvider" --tag="migrations"
php artisan migrate
```

Register the tool with Nova in the `tools()` method of the `NovaServiceProvider`:

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \OptimistDigital\NovaPageManager\NovaPageManager([
            'templates' => [], // Register Template classes, ie: [HomePageTemplate::class]
            'locales' => [] // Register locales, ie: ['en_US' => 'English']
        ]),
    ];
}
```

## Usage

### Creating templates

Templates can be created using the following Artisan command:

```bash
php artisan pagemanager:template {className}
```

This will ask you a few additional details and will create a base template in `App\Nova\Templates`;

### Registering templates

All your templates have to be registered in the `NovaPageManager` constructor, inside the `NovaServiceProvider`'s `tools()` function.

Example:

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \OptimistDigital\NovaPageManager\NovaPageManager([
            'templates' => [
                \App\Nova\Templates\HomePageTemplate::class
            ],
            'locales' => []
        ]),
    ];
}
```

### Defining locales

Locales can be defined similarly to how templates are registered. Pass the dictionary of languages to the `NovaPageManager` constructor.

Example:

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \OptimistDigital\NovaPageManager\NovaPageManager([
            'templates' => [],
            'locales' => [
                'en_US' => 'English',
                'et_EE' => 'Estonian'
            ]
        ]),
    ];
}
```

## Credits

- [Tarvo Reinpalu](https://github.com/Tarpsvo)

## License

Nova page manager is open-sourced software licensed under the [MIT license](LICENSE.md).

# Nova Page Manager

This [Laravel Nova](https://nova.laravel.com) package allows you to create and manage pages and regions. The package is geared towards headless CMS's.

## Features

- Pages and Regions management
- Programmatically created templates for Pages and Regions
- Multilanguage support

## Screenshots

![Index View](docs/index.png)

![Filter Dropdown](docs/filter.png)

![Page Content Area](docs/content.png)

## Installation

Install the package in a Laravel Nova project via Composer:

```bash
composer require optimistdigital/nova-page-manager
```

Publish the `nova-page-manager` configuration file and edit it to your preference:

```bash
php artisan vendor:publish --provider="OptimistDigital\NovaPageManager\ToolServiceProvider" --tag="config"
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
        new \OptimistDigital\NovaPageManager\NovaPageManager
    ];
}
```

## Usage

### Creating templates

Templates can be created using the following Artisan command:

```bash
php artisan pagemanager:template {className}
```

This will ask you a few additional details and will create a base template in `App\Nova\Templates`.

The template base has a few properties:

```php
// Define whether the template is for a page or a region
// Applicable values: 'page', 'region'
public static $type = 'page';

// The unique name for the page, usually similar to a slug
public static $name = 'about-us';

// The package has built in SEO fields support
// This boolean decides whether or not to display them
public static $seo = false;

// Return all fields here, just as you would inside a resource
public function fields(Request $request): array
{
  return [
      Text::make('Title', 'title')
  ];
}
```

### Registering templates

All your templates have to be registered using the `NovaPageManager::configure()` function, preferably in `NovaServiceProvider`'s `boot()` function.

Example:

```php
// in app/Providers/NovaServiceProvider.php

public function boot()
{
    \OptimistDigital\NovaPageManager\NovaPageManager::configure([
        'templates' => [
            \App\Nova\Templates\HomePageTemplate::class
        ],
        'locales' => []
    ]);
}
```

### Defining locales

Locales can be defined similarly to how templates are registered. Pass the dictionary of languages to the `NovaPageManager::configure()` function.

Example:

```php
// in app/Providers/NovaServiceProvider.php

public function boot()
{
    \OptimistDigital\NovaPageManager\NovaPageManager::configure([
        'templates' => [],
        'locales' => [
            'en_US' => 'English',
            'et_EE' => 'Estonian'
        ]
    ]);
}
```

## Credits

- [Tarvo Reinpalu](https://github.com/Tarpsvo)

## License

Nova page manager is open-sourced software licensed under the [MIT license](LICENSE.md).

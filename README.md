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

### Overwrite package resources

You can overwrite the package resources (Page & Region) by setting the config options in `nova-page-manager.php`.

Note: If you create your resources under `App\Nova` namespace, to avoid key duplication you must manually register all other resources in the `NovaServiceProvider`. See [Registering resources](https://nova.laravel.com/docs/2.0/resources/#registering-resources)

## Helper functions

### nova_get_pages_structure()

The helper function `nova_get_pages_structure()` returns the base pages structure (slugs, templates, child-parent relationships) that you can build your routes upon in the front-end. This does not return the pages' data.

Example response:

```json
[
  {
    "locales": ["en_US", "et_EE"],
    "id": {
      "en_US": 3,
      "et_EE": 4
    },
    "name": {
      "en_US": "Home",
      "et_EE": "Kodu"
    },
    "slug": {
      "en_US": "/",
      "et_EE": "/"
    },
    "template": "home-page",
    "children": [
      {
        "locales": ["en_US"],
        "id": {
          "en_US": 5
        },
        "name": {
          "en_US": "About"
        },
        "slug": {
          "en_US": "about"
        },
        "template": "home-page"
      }
    ]
  }
]
```

### nova_get_regions()

The helper function `nova_get_regions()` returns all the regions and their data.

Example response:

```json
[
  {
    "locales": ["en_US"],
    "id": {
      "en_US": 3
    },
    "name": {
      "en_US": "Main header"
    },
    "template": "main-header",
    "data": {
      "en_US": {
        "content": [
          {
            "layout": "horizontal-text-section",
            "attributes": {
              "text": "Lorem ipsum"
            }
          }
        ]
      }
    }
  }
]
```

### nova_get_page(\$pageId)

The helper function `nova_get_page($pageId)` finds and returns the page with the given ID.

Example response for querying page with ID `3` (`nova_get_page(3)`):

```json
{
  "locale": "en_US",
  "id": 3,
  "name": "Home",
  "slug": "/",
  "data": {
    "banner": [],
    "categories_grid": []
  },
  "template": "home-page"
}
```

## Credits

- [Tarvo Reinpalu](https://github.com/Tarpsvo)

## License

Nova page manager is open-sourced software licensed under the [MIT license](LICENSE.md).

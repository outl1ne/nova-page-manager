# Nova Page Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/outl1ne/nova-page-manager.svg?style=flat-square)](https://packagist.org/packages/outl1ne/nova-page-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/outl1ne/nova-page-manager.svg?style=flat-square)](https://packagist.org/packages/outl1ne/nova-page-manager)

This [Laravel Nova](https://nova.laravel.com) package allows you to create and manage pages and regions for your frontend application.

## Requirements

```
- PHP >=8.0
- laravel/nova ^4.13
```

## Features

- Page and region management w/ custom fields
- Multiple locale support

## Screenshots

![Form (dark)](docs/screenshots/form-dark.jpeg)

## Installation

Install the package in a Laravel Nova project via Composer and run migrations:

```bash
# Install package
composer require outl1ne/nova-page-manager

# Run automatically loaded migrations
php artisan migrate
```

Publish the `nova-page-manager` configuration file and edit it to your preference:

```bash
php artisan vendor:publish --provider="Outl1ne\PageManager\NPMServiceProvider" --tag="config"
```

Register the tool with Nova in the `tools()` method of the `NovaServiceProvider`:

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \Outl1ne\PageManager\PageManager()
          ->withSeoFields(fn () => []), // Optional
    ];
}
```

## Usage

### Creating templates

Templates can be created using the following Artisan command:

```bash
php artisan npm:template {className}
```

This will ask you a few additional details and will create a base template in `App\Nova\Templates`.

The base template exposes a few overrideable functions:

```php
// Name displayed in CMS
public function name(Request $request)
{
    return parent::name($request);
}

// Fields displayed in CMS
public function fields(Request $request): array
{
    return [];
}

// Resolve data for serialization
public function resolve($page): array
{
    // Modify data as you please (ie turn ID-s into models)
    return $page->data;
}

// Page only
// Optional suffix to the route (ie {blogPostName})
public function pathSuffix() {
    return null;
}
```

### Registering templates

All your templates have to be registered in the `config/nova-page-manager.php` file.

```php
// in /config/nova-page-manager.php

// ...
'templates' => [
    'pages' => [
        'home-page' => [
            'class' => '\App\Nova\Templates\HomePageTemplate',
            'unique' => true, // Whether more than one page can be created with this template
        ],
    ],
    'regions' => [
        'header' => [
            'class' => '\App\Nova\Templates\HeaderRegionTemplate',
            'unique' => true,
        ],
    ],
],
// ...
```

### Defining locales

The locales are defined in the config file.

```php
// in /config/nova-page-manager.php

// ...
'locales' => [
  'en' => 'English',
  'et' => 'Estonian',
],

// OR

'locales' => function () {
  return Locale::all()->pluck('name', 'key');
},

// or if you wish to cache the configuration, pass a function name instead:

'locales' => NPMConfiguration::class . '::locales',
// ...
```

### Add links to front-end pages

To display a link to the actual page next to the slug, add or overwrite the value in `config/nova-page-manager.php` for the key `base_url`.

```php
// in /config/nova-page-manager.php

'base_url' => 'https://webshop.com', // Will add slugs to the end to make the URLs
```

### Overwriting models and resources

You can overwrite the page/region models or resources, just set the new classes in the config file.

### Custom locale display

To customize the locale display you can use `Nova::provideToScript` to pass `customLocaleDisplay` as in the example below.

```php
// in app/Providers/NovaServiceProvider.php

public function boot()
{
    Nova::serving(function () {
        Nova::provideToScript([
            // ...
            'customLocaleDisplay' => [
                'en' => <img src="/flag-en.png"/>,
                'et' => <img src="/flag-et.png"/>,
            ]
        ]);
    });
}
```

## Advanced usage

### Non-translatable panels

There's some cases where it's more sensible to translate sub-fields of a panel instead of the whole panel. This is possible, but is considered an "advanced usecase" as the feature is really new and experimental, also the developer experience of it is questionable.

You can create a non-translatable panel like so:

```php
// In your PageTemplate class

public function fields() {
  return [
    Panel::make('Some panel', [
      Text::make('Somethingsomething'),
      Text::make('Sub-translatable', 'subtranslatable')
        ->translatable(),
    ])
    ->translatable(false),
  ];
}
```

This will create a key with `__` in the page data object. This means that the page data will end up looking something like this:

```php
[
  '__' => [
    'somethingsomething' => 'your value',
    'subtranslatable' => [
      'en' => 'eng value',
      'et' => 'et value'
    ]
  ],
  'en' => [],
  'et' => [],
]
```

## Helper functions

Helper functions can be found in the `Outl1ne\PageManager\Helpers\NPMHelpers` class.

### NPMHelpers::getPagesStructure()

Calls `resolve()` on their template class and returns all pages as a tree where child pages are nested inside the `children` array key recursively.

### NPMHelpers::getPages()

Calls `resolve()` on their template class and returns all pages. Returns an array of arrays.

### NPMHelpers::getRegions()

Calls `resolve()` on their template class and returns all regions. Returns an array of arrays.

### NPMHelpers::getPageByTemplate($templateSlug)

Finds a single page by its template slug (from the config file), calls `resolve()` on its template class and returns it.

### NPMHelpers::getPagesByTemplate($templateSlug)

Same as `getPageByTemplate`, but returns an array of pages.

### NPMHelpers::formatPage($page)

Calls `resolve()` on the page's template class and returns the page as an array.

### NPMHelpers::formatRegion($region)

Calls `resolve()` on the region's template class and returns the region as an array.

## Localization

The translation file(s) can be published by using the following command:

```bash
php artisan vendor:publish --provider="Outl1ne\PageManager\ToolServiceProvider" --tag="translations"
```

You can add your translations to `resources/lang/vendor/nova-page-manager/` by creating a new translations file with the locale name (ie `et.json`) and copying the JSON from the existing `en.json`.

## Example of route and controller to serve pages

In routes/web.php
```php
Route::get('/page/{path?}', [PageController::class, 'show'])
    ->where('path', '[\w-]+');
```

In the controller
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Outl1ne\PageManager\NPM;
use Inertia\Inertia;

class PageController extends Controller
{
  /**
     * Handles dynamic page requests
     *
     * @param Request $request
     * @param string|null $path
     * @return \Inertia\Response
     */
public function show(Request $request, $path = '/')
    {
        
        // Get the current locale
        $locale = App::getLocale();
        $locales = NPM::getLocales();
        
        // Force a valid locale
        if (!isset($locales[$locale])) {
            $locale = array_key_first($locales);
        }
        
        // get page model class
        $pageModel = NPM::getPageModel();
        
        // get the model istance
        $page = $pageModel::where('active', true)
        ->where(function($query) use ($locales, $path) {
            foreach($locales as $locale => $name){
                $query->orWhere('slug->' . $locale, $path);
            }
        })
        ->first();
        
        // return 404 if page not found
        if (!$page) {
            abort(404);
        }
        
        // Get the page template
        $templateSlug = $page->template;
        
        
        // Prepare page data
        // maybe here you should filter the data by the locale
        $pageData = $page->toArray();
        
        // Render the dynamic page component
        return Inertia::render('DynamicPage', [
            'page' => $pageData,
            'template' => $templateSlug,
            //maybe here you're interested also in regions
            //'regions' => NPM::getRegions(),
        ]);

        //OR

        //  return view('page', [
        //     'page' => $pageData,
        //     'template' => $templateSlug,
        // ]);

        //OR

        //  return view('page.' . $templateSlug, [
        //     'page' => $pageData,
        // ]);

        // ...
    }
}
```

## Credits

- [Tarvo Reinpalu](https://github.com/Tarpsvo)
- [Kaspar Rosin](https://github.com/KasparRosin)

## License

Nova page manager is open-sourced software licensed under the [MIT license](LICENSE.md).

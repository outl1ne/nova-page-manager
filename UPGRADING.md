# Upgrading from Nova Page Manager 2.0 to 3.0

Changed the way drafts are toggled - there's no longer a flag `drafts_enabled` in the config. You can safely remove that entry from the configuration file.

From 3.0 onwards, to have drafts, you must install `nova-drafts`. After the install, the drafts will be available automatically.

```bash
composer require optimistdigital/nova-drafts
```

# Upgrading from Nova Page Manager 1.0 to 2.0

## Migrations

Migrations are now loaded automatically and can be deleted from your project. This aims to reduce the number of migration files inside the end project's folder and keep them more relevant.

Migration files to delete:

```
2019_03_13_000000_create_page_manager_tables.php
2019_04_18_000001_add_child_parent_relationships.php
2019_04_24_000002_create_region_and_pages_tables.php
2019_05_03_000003_make_slug_locale_pair_unique.php
2019_06_10_000004_add_draft_fields_to_page.php
```

## Configuration

The `NovaPageManager::configure()` function is now removed and all the configuration options have to be defined in the `config/nova-page-manager.php` file.

Easiest way to migrate is to re-publish the configuration file and re-configure it by copying the data from the `NovaPageManager::configure()` function to the config file.

To force re-publish the config file:

```bash
php artisan vendor:publish --provider="OptimistDigital\NovaPageManager\ToolServiceProvider" --tag="config" --force
```

Configuration options to migrate:

### Templates

```php
// NovaServiceProvider.php
NovaPageManager::configure([
  'templates' => [
    HomePageTemplate::class,
  ],
]);

// is now ->

// config/nova-page-manager.php
return [
  // ...
  'templates' => [
    HomePageTemplate::class,
  ],
  // ...
]
```

### Drafts

```php
// NovaServiceProvider.php
NovaPageManager::configure([
  'drafts' => true,
]);

// is now ->

// config/nova-page-manager.php
return [
  // ...
  'drafts_enabled' => true,
  // ...
]
```

### Locales

```php
// NovaServiceProvider.php
NovaPageManager::configure([
  'locales' => ['en' => 'English'],
]);

// is now ->

// config/nova-page-manager.php
return [
  // ...
  'locales' => ['en' => 'English'],

  // OR

  'locales' => function () {
    return Locale::all()->pluck('name', 'key');
  },
  // ...
]
```

### Page preview URL

```php
// NovaServiceProvider.php
use OptimistDigital\NovaPageManager\Models\Page;

NovaPageManager::pagePreviewUrl(function (Page $page) {
  return env('APP_URL') . '/' . $page->path;
});

// is now ->

// config/nova-page-manager.php
use OptimistDigital\NovaPageManager\Models\Page;

return [
  // ...
  'page_url' => function (Page $page) {
    return env('APP_URL') . '/' . $page->path;
  }),
  // ...
]
```

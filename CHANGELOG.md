# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.5] - 2020-04-07

### Changed

- Change SEO fields on Pages from `VARCHAR` to `LONGTEXT` (migration required)

## [3.1.4] - 2020-04-02

### Changed

- Remove `SEO`, `Page data`, `Region data` headings as they're no longer necessary due to `Panel` separation
- Update packages

## [3.1.3] - 2020-03-24

### Changed

- Fix query bug in `nova_get_pages_structure_flat()` that caused duplicate entries
- Update packages

## [3.1.2] - 2020-03-05

### Added

- Support Nova 3.0 in `composer.json` requirements

## [3.1.1] - 2020-02-27

### Changed

- Fixed issue where `previewToken` check always passed (affects only `nova-drafts` users)
- Updated packages

## [3.1.0] - 2020-02-19

### Added

- Added `resolveResponseUsing(callback $callback)` function to all `Field` elements that allows you to modify the value before it's returned through the Page Manager's API

### Changed

- Fix `NovaLang` index query

## [3.0.4] - 2020-02-19

### Changed

- Fix PHP 7.4 exception

## [3.0.3] - 2020-02-10

### Changed

- Allow the hiding of resources from navigation by setting the config value of `page_resource` and/or `region_resource` to `false`

## [3.0.2] - 2020-01-20

### Changed

- Improve visual style of slug field on edit view
- Fix localized pages path generation logic

## [3.0.1] - 2020-01-17

### Changed

- Add `nova-lang` support to Regions (by [@KasparRosin](https://github.com/KasparRosin))
- Update packages

## [3.0.0] - 2020-01-17

### Changed

- Breaking! Changed the way drafts are toggled - there's no longer a flag `drafts_enabled` in the config.
  - From now on, to have drafts, you must install `nova-drafts`:
  - `composer require optimistdigital/nova-drafts`
  - After the install, the drafts will be enabled automatically.
- Update Node packages and dist files

## [2.3.2] - 2020-01-10

### Added

- Added new config option `page_path` which allows project-specific path manipulation

## [2.3.1] - 2019-12-17

### Added

- Added new helper `nova_get_pages_structure_flat()` that returns pages as a flat structure with full paths instead of slugs

## [2.3.0] - 2019-12-16

### Changed

- Reduced number of scripts the page manager tool loads to 1 (by [@KasparRosin](https://github.com/KasparRosin))
- Updated nova-locale-field dependency to 2.0.0

## [2.2.2] - 2019-12-11

### Changed

- Force page slug to be lowercase

## [2.2.1] - 2019-12-06

### Changed

- Fix possible index name conflict in latest migration (by [@KasparRosin](https://github.com/KasparRosin))

## [2.2.0] - 2019-12-05

### Added

- Added `nova_page_manager_get_page_by_path($path, $previewToken = null, $locale = null)` helper function (by [@KasparRosin](https://github.com/KasparRosin))

### Changed

- Made default config serializable
- Improved slug validation (multiple pages can have the same slug as long as they have a different parent now) (by [@KasparRosin](https://github.com/KasparRosin))

## [2.1.1] - 2019-11-21

### Changed

- Added `$template::$view` variable support (thanks to [@lvdhoorn](https://github.com/lvdhoorn))
- Added better slug matching (w/ locale support) (thanks to [@lvdhoorn](https://github.com/lvdhoorn))

## [2.1.0] - 2019-11-13

### Changed

- Added [nova-lang](https://github.com/optimistdigital/nova-lang/) support
- Fixed migration index names (thanks to [@jgile](https://github.com/jgile))

## [2.0.3] - 2019-11-01

### Changed

- Fixed invalid HEREDOC causing `ViewException` on PHP 7.2
- Fixed `LocaleFilter` crashing

## [2.0.2] - 2019-10-25

### Changed

- Fixed home page slug validation (/ did not pass validation after last update)

## [2.0.1] - 2019-10-25

### Changed

- Hide Pages' IDs
- Order pages in such a way that its children are shown underneath it in Index view (by [@KasparRosin](https://github.com/KasparRosin))
- Slug field now shows the parents' slugs in edit and create views (by [@KasparRosin](https://github.com/KasparRosin))
- Slug field now only allows alphanumeric characters, dashes and underscores (by [@KasparRosin](https://github.com/KasparRosin))

## [2.0.0] - 2019-10-23

NB! This is a major release. Please consult [UPGRADING.md](UPGRADING.md) for an upgrading guide.

### Changed

- Moved all configuration options from `NovaPageManager::configure()` to `config/nova-page-manager.php`.
- Improved drafting feature (thanks to [@KasparRosin](https://github.com/KasparRosin))
- Changed page link text from 'view' to 'view draft' when the page is a draft (thanks to [@KasparRosin](https://github.com/KasparRosin))
- Fixed draft not updating all of the Page's fields when publishing (thanks to [@KasparRosin](https://github.com/KasparRosin))
- Fixed "create & add another" continuously adding draft buttons to the UI (thanks to [@KasparRosin](https://github.com/KasparRosin))

## [1.9.9] - 2019-10-22

### Changed

- Add `path` property to `nova_format_page()` result

## [1.9.8] - 2019-10-18

### Changed

- Made `Page` and `Region` resources searchable and sortable by template

## [1.9.7] - 2019-10-16

### Added

- Added option to display a link to the front-end website in the slug field using `NovaPageManager::pagePreviewUrl()`

## [1.9.6] - 2019-10-15

### Changed

- Added `path` attribute to `Page` models (which displays the path with parent slugs appended)

## [1.9.5] - 2019-09-06

### Changed

- Fixed missing SEO fields when using `nova_get_page` or `nova_format_page`

## [1.9.4] - 2019-08-13

### Changed

- Improved `Panel` name sanitization function

## [1.9.3]

### Added

- Add discard button to drafts by [@mikkoun](https://github.com/mikkoun)

## [1.9.2]

### Added

- Add `nova_format_page($page)` helper function

### Changed

- Fix resolving of Flexible fields
- Fix resolving of Computed fields

## [1.9.1]

### Changed

- Fixed `nova_get_page()` crash when a `Panel` field has no value

## [1.9.0]

### Added

- Pages drafting support by [@mikkoun](https://github.com/mikkoun)

NB! Migration required.

```bash
php artisan vendor:publish --provider="OptimistDigital\NovaPageManager\ToolServiceProvider" --tag="migrations"
php artisan migrate
```

## [1.8.2]

### Changed

- Fixed runtime crash caused by a syntax error (trailing comma) as reported by [@ds-pda5](https://github.com/ds-pda5)

## [1.8.1]

### Changed

- Fixed panels not being included in `nova_get_page()` return value
- Fixed SEO fields not being included in `nova_get_page()` return value

## [1.8.0]

### Added

- Support `Laravel\Nova\Panel` component along with grouping by Panel (fields are grouped under Panel's name)

## [1.7.4]

### Changed

- Add missing `->locales()` call to `LocaleFilter`

## [1.7.3]

### Changed

- Require `doctrine/dbal` to fix initial migrations
- Updated `optimistdigital/nova-locale-field` to use its locale and locale children filters
- Removed filters from this project

## [1.7.2]

### Changed

- Fixed not being able to remove parent from a `Page`

## [1.7.1]

### Changed

- Template class now has access to the resource (`$this->resource`) that can be used in the `fields()` function

## [1.7.0]

### Changed

- Replaced `locale-field` and `locale-parent-field` with a spin-off package `optimistdigital/nova-locale-field`

## [1.6.1]

### Added

- Added config option `page_resource` to overwrite the `Page` Nova resource
- Added config option `region_resource` to overwrite the `Region` Nova resource

Thanks to [@slovenianGooner](https://github.com/slovenianGooner)

If you want to get the updated config, you can force re-publish the config file.

```bash
php artisan vendor:publish --provider="OptimistDigital\NovaPageManager\ToolServiceProvider" --tag="config" --force
```

## [1.6.0]

### Added

- Added config option `max_locales_shown_on_index` with default value of `2`

### Changed

- Translation options are now shown on both Index and Detail views
- When the amount of translation options exceeds the configured limit, the options are only displayed on the Detail view (thanks to [@slovenianGooner](https://github.com/slovenianGooner))
- Fixed `LocaleParentField` not showing the title that was defined in the `Resource`
- Fixed `LocaleParentField` not showing the parent value even though it has a parent

## [1.5.2]

### Changed

- Fixed SEO field imports in `Page` class from [@slovenianGooner](https://github.com/slovenianGooner)

## [1.5.1]

### Changed

- Pass the template model as the second parameter `resolveResponseValue($value, $templateModel)` function

## [1.5.0]

### Changed

- Added ability to apply custom response formatting by implementing `resolveResponseValue($value)` function on any field
- Reworked the way data is returned through `nova_get_page` and `nova_get_region` helpers

## [1.4.0]

### Changed

- Made `nova_get_page(id)` return just one page, instead of all related locales of the page

## [1.3.1]

### Changed

- Fixed unique slug-locale pair validation

## [1.3.0]

### Changed

- Made slug-locale pair unique in pages table (migration required)

## [1.2.3]

### Added

- Added `nova_get_page($pageId)` helper function

## [1.2.2]

### Changed

- Ask for template type before template slug
- Remove 'seo' field from region template

## [1.2.1]

### Changed

- Fixed multiple issues with form fields being disabled when they're not supposed to be
- Fixed issues with empty placeholders in select type form fields

## [1.2.0]

### Added

- Added `nova_get_regions()` helper

### Changed

- Regions and pages are now in separate tables (migration required)
- Only one region (and its translations) per region template is now allowed
- Refactored `Page`, `Region` and `TemplateResource` resource classes

### Removed

- Removed `type` column from pages table (while renaming the table)

## [1.1.0] - 2019-04-04

### Added

- Child-parent relationship to pages (migration is necessary)
- `nova_get_pages_structure()` helper function that returns the basic page's layout

### Changed

- Update README.md from [@kikoseijo](https://github.com/kikoseijo)

### Removed

- Removed TemplateResolver as a fix was merged to [nova-flexible-content](https://github.com/whitecube/nova-flexible-content)

## [1.0.2] - 2019-04-04

### Changed

- Fix default table name in TemplateResource

## [1.0.1] - 2019-03-30

### Added

- Ability to customize table name from [@stephenlake](https://github.com/stephenlake)

### Changed

- Fix unique validation from [@stephenlake](https://github.com/stephenlake)

## [1.0.0] - 2019-03-17

### Added

- Page and region management
- Programmatically created templates for pages and regions
- Multilanguage support

# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[1.7.4]: https://github.com/optimistdigital/nova-page-manager/compare/1.7.3...1.7.4
[1.7.3]: https://github.com/optimistdigital/nova-page-manager/compare/1.7.2...1.7.3
[1.7.2]: https://github.com/optimistdigital/nova-page-manager/compare/1.7.1...1.7.2
[1.7.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.7.0...1.7.1
[1.7.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.6.1...1.7.0
[1.6.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.6.0...1.6.1
[1.6.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.5.2...1.6.0
[1.5.2]: https://github.com/optimistdigital/nova-page-manager/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.4.0...1.5.0
[1.4.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.3.1...1.4.0
[1.3.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.3...1.3.0
[1.2.3]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.0.2...1.1.0
[1.0.2]: https://github.com/optimistdigital/nova-page-manager/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/optimistdigital/nova-page-manager/releases/tag/1.0.0

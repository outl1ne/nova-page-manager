# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.5.4] - 15-09-2022

### Fixes

- Fixed providing too few arguments to region resolve function.

## [5.5.3] - 08-09-2022

### Fixes

- Fixed array_map call when page seo fields were null.

## [5.5.2] - 06-09-2022

### Changed

- Changed the default order of fields.
  - Now matches with the order that was in version >5.0.

Simplifies the transition from version 4.0 => 5.0.<br/>
If you prefer the previous layout, you can override the resource class and change the order to your liking.

## [5.5.1] - 05-09-2022

### Added

- Filtering by template for both regions and pages
- Filtering by uniqueness for both regions and pages

## [5.5.0] - 05-09-2022

### Added

- Added regex start and end boundaries for `collectAndReplaceUsing` function
- Improved slug field behaviour with different locales
- Added support for downloading image fields

### Fixed

- Fixed an issue with some fields, where only the first value of an array was saved.

## [5.4.3] - 31-08-2022

### Added

- Support providing eloquent Builder instance to `collectAndReplace` function.

## [5.4.2] - 31-08-2022

### Changed

- Fixed support for fields that wished to read other fields values.
  - Specifically targeted `nova-flexible-content` package where having multiple
    Flexible fields inside a page would throw an error.

## [5.4.1] - 25-08-2022

### Changed

- Fixed page seo image field storing

## [5.4.0] - 25-08-2022

### Changed

- Fixed NPMHelpers page formatter not returning name and slug translations
  - NB! Previously returned a default translation string for 'name' and 'slug', now it returns an object, ie: (`['en' => 'Name']`)
- Fixed migrations on Postgres (thanks to [@KasparRosin](https://github.com/KasparRosin))

## [5.3.4] - 23-08-2022

### Added

- Added `$flat` option to `NPMHelpers::getPageStructure`

### Changed

- Improved data replacement error handling

## [5.3.3] - 18-08-2022

### Changed

- Fixed data replacement logic

## [5.3.2] - 18-08-2022

### Added

- Added `collectAndReplaceUsing` helper to Template

### Changed

- Fixed data replacement logic

## [5.3.1] - 17-08-2022

### Added

- Now returns `seo` data from formatPage function.

### Removed

- Removed SEO fields from regions.

## [5.3.0] - 17-08-2022

### Added

- Added Nova `dependsOn` support
- Added advanced DataReplaceHelpers to Template to make resolve()-ing easier

### Changed

- Fixed issue with flexible content compatibility

## [5.2.0] - 15-08-2022

### Added

- Added `whitecube/nova-flexible-content` support

### Changed

- Fixed UI panel width problems with latest Nova version
- Fixed invalid type for the `name` column (requires running migrations)
- Changed Tailwind prefix from `npm-` to `o1-`
- Updated packages

## [5.1.2] - 29-07-2022

### Fixed

- Fix mysql query syntax that was breaking with postgres databases.

## [5.1.1] - 29-07-2022

### Fixed

- Fixed support with `outl1ne/nova-multiselect-field` that was broken in release [5.1.0](#510---29-07-2022)

## [5.1.0] - 29-07-2022

### Added

- Added support for Panels inside page template fields

- Added `fieldPrefix` macro to Panel
  - Allows you to store panel data in objects.
  - ->fieldPrefix('hero') stores data in `data->{locale}->hero->{field_attribute}`

### Changed

- Updated packages

## [5.0.8] - 28-07-2022

### Changed

- Improved the query speed of `getPageByPath` helper.

## [5.0.7] - 11-07-2022

### Changed

- Fix disabling of resources/models not working

## [5.0.6] - 15-06-2022

### Changed

- Fixed instances of missing $params in function declarations

## [5.0.5] - 15-06-2022

NB! Breaking - Template's resolve() function had its signature changed. It's now:

```php
public function resolve($page, $params): array
{}
```

### Added

- Added $params to Template's resolve function which are filled when using `getPageByPath` (thanks to [@kaareloun](https://github.com/kaareloun))

## [5.0.4] - 14-06-2022

### Added

- `NPMHelpers::getPageByPath()` function (thanks to [@kaareloun](https://github.com/kaareloun))

## [5.0.3] - 13-06-2022

### Changed

- Fix landing (/) slug handling

## [5.0.2] - 13-06-2022

### Changed

- Fixed crash on update when Template has no fields defined

## [5.0.1] - 13-06-2022

### Added

- SEO fields support and configuration options

### Changed

- Reworked fields filling logic on both the JS and PHP side to support File and Image fields
- Fixed spread operator used on an array with string keys causing an exception on PHP 8.0
- Fixed stub not having correct return types (thanks to [@kaareloun](https://github.com/kaareloun))

## [5.0.0] - 09-06-2022

Major rework for Nova 4.

### Changed

- Dropped PHP 7.X support
- Dropped Nova 3.X support
- Reworked localization logic
- Renamed namespace from OptimistDigital to Outl1ne

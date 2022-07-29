# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

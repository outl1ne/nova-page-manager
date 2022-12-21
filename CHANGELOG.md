# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.8.5] - 21-12-2022

### Changed

- Fixed issue where after deleting an image, it was not possible to save the resource due to timestamp update
- Fixed crash regarding the resolving of DateTime fields
- Fixed issue where SEO image would block title and description from saving

## [5.8.4] - 22-11-2022

### Changed

- Fixed issue with multiple Flexible fields on the same page
- Removed usages of Arr::map() to support Laravel 8
- Updated packages

## [5.8.3] - 16-11-2022

### Changed

- Improved error handling with fields that do not provide .fill()
- Updated packages

## [5.8.2] - 07-11-2022

### Changed

- Fixed multiple panels not being filled properly in page/region edit views

## [5.7.3] - 03-11-2022

### Changed

- Fixed double-digit array indexes not working with collectAndReplace
- Fixed hardcoded keyName in collectAndReplace

## [5.8.1] - 03-11-2022

### Changed

- Fixed a crash with region templates

## [5.8.0] - 02-11-2022

### Changed

- Stubs now have fallback to empty array to avoid type error. (thanks to [@KaarelOun](https://github.com/KaarelOun))

## [5.8.0-RC3] - 24-10-2022

### Changed

- Fixed double-digit array indexes not working with collectAndReplace
- Fixed hardcoded keyName in collectAndReplace

## [5.8.0-RC2] - 21-10-2022

### Changed

- Fixed non-unique keys crash in collectAndReplace function (thanks to [@KaarelOun](https://github.com/KaarelOun))

## [5.8.0-RC1] - 21-10-2022

### Added

- Added `->translatable(false)` advanced option for Panels

## [5.7.2] - 13-10-2022

### Changed

- Fixed crash with Nova 4.16 and Peekable

## [5.7.1] - 13-10-2022

### Added

- Added the ability to use PageLinkField component without `outl1ne/nova-translatable`.

## [5.7.0] - 04-10-2022

### Changed

- Fixed dependsOn support for Nova 4.13 and above
- Bumped minimum Nova version to 4.13
- Updated packages

## [5.6.2] - 26-09-2022

### Fixes

- Fixed `getPagesStructure` support with postgres.
  - Previously pages with parent_id were not returned.
    This was caused by postgres ordering `null` results differently from mysql.

## [5.6.1] - 26-09-2022

### Changed

- Hide the whole page fields component with locale button when there's no fields
- Updated packages

## [5.6.0] - 16-09-2022

### Added

- Added `keys` parameter to NPMHelpers::getPageStructure function.

### Changed

- Rewrote the `NPMHelpers::getPagesStructure` to further optimize query speed and reduce n+1 queries.

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

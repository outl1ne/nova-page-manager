# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.3.1]

### Changed

- Fixed unique slug-locale pair validation

## [1.3.0]

### Changed

- Make slug-locale pair unique in pages table (migration required)

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

[unreleased]: https://github.com/optimistdigital/nova-page-manager/compare/1.1.0...HEAD
[1.3.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.3...1.3.0
[1.2.3]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/optimistdigital/nova-page-manager/compare/1.0.2...1.1.0
[1.0.2]: https://github.com/optimistdigital/nova-page-manager/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/optimistdigital/nova-page-manager/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/optimistdigital/nova-page-manager/compare/37b3ead816bbb9cfb18a6294f8c3b1b882c24fbb...1.0.0

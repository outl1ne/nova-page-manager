# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.0.1] - 13-06-2022

### Added

- SEO fields support and configuration options

### Changed

- Reworked fields filling logic on both the JS and PHP side to support File and Image fields
- Fixed spreading operator used on an array with string keys causing an exception on PHP 8.0
- Fixed stub not having correct return types (thanks to [@kaareloun](https://github.com/kaareloun))

## [5.0.0] - 09-06-2022

Major rework for Nova 4.

### Changed

- Dropped PHP 7.X support
- Dropped Nova 3.X support
- Reworked localization logic
- Renamed namespace from OptimistDigital to Outl1ne

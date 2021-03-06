# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Features
- InDesign template file download with PimPrint-Plugin.
- Simplified API for content aware updates.
- `PublicationTreeBuilder` service for direct usage.
- `Template` commands for single page and facing page documents.
- `SplitTable` command for automatic table splitting across multiple pages.

### Breaking changes
- Change `GoToPage` and `NextPage` constructor signature.
- Rename namespace to `Mds\PimPrint\CoreBundle\InDesign\Command\Variables`.
- Move `host` node in `mds_pim_print_core` configuration. 

## [1.1.0] - 2020-09-01
### Features
- Add UpdateElements command and selected elements filtering in `CommandQueue`.
- Add helper traits for concrete rendering services (E.g. `ElementCollectionRenderingTrait`).
- Add abstract template classes for standardized paper sizes.
- Add configurable form field to InDesign plugin.
- Set request and Pimcore locales to rendered language.
- Add extension possibilities to `AbstractPublicationTreeBuilder`.

### Breaking changes
- Change `GroupEnd` constructor signature.

### Fix
- Add configurable `LC_NUMERIC` locale setting to ensure InDesign compatible float to string conversion.

## [1.0.1] - 2020-08-06
### Changed
- Ensure float margin values in `PositionTrait`. 
- Update `mds-agenturgruppe/php-code-checker` to v1.0.1.

### Fixed
- BruteforceProtectionListener checks for InDesign requests.
 

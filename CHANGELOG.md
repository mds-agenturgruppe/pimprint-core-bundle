# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.0] - 2023-11-20

### Feature

- Removed `\Mds\PimPrint\CoreBundle\InDesign\Command\TextBox::setDefaultUseLanguageLayer`
    - Use localized InDesign page-elements feature
- Enhanced `boxIdentReference` handling for content sensitive updated
    - `\Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait::$boxIdentReference`

### Fixed

- Refactor `\Mds\PimPrint\CoreBundle\Service\ProjectsManager::$project` to non `static` member variable.

## [2.0.0] - 2022-09-13

### Features

- PHP 8 compatibility
- Pimcore 10 compatibility
- Symfony Authentication Manager support

## [1.3.0] - 2022-08-29

### Feature

- Custom fields in InDesign PimPrint-Plugin:
    - `\Mds\PimPrint\CoreBundle\InDesign\CustomField\Input`
    - `\Mds\PimPrint\CoreBundle\InDesign\CustomField\Select`
    - `\Mds\PimPrint\CoreBundle\InDesign\CustomField\Search`
- Configuration options for factory PimPrint-Plugin field Publication:
    - visibility
    - required
    - custom label

### Fix

- PHP Session detection for JSON encoded POST requests.

## [1.3.0] - 2022-08-29

### Feature

- Custom fields in InDesign PimPrint-Plugin:
    - `\Mds\PimPrint\CoreBundle\InDesign\CustomField\Input`
    - `\Mds\PimPrint\CoreBundle\InDesign\CustomField\Select`
    - `\Mds\PimPrint\CoreBundle\InDesign\CustomField\Search`
- Configuration options for factory PimPrint-Plugin field Publication:
    - visibility
    - required
    - custom label

### Fix

- PHP Session detection for JSON encoded POST requests.

## [1.2.1] - 2022-04-25

### Fix

- [[AbstractProject] Loading languages in multi-domain setup #1](https://github.com/mds-agenturgruppe/pimprint-core-bundle/pull/1)
- Uses `\Pimcore\Localization\LocaleService` instead of `\Mds\PimPrint\CoreBundle\Service\UserHelper`

### Breaking changes

- Removed `\Mds\PimPrint\CoreBundle\Service\UserHelper`

## [1.2.0] - 2020-10-22

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
 

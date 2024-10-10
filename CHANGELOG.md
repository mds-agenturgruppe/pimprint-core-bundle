# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Feature

- New placement mode for localized page-elements to omit the master locale box geometry.
    - `\Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox::USE_MASTER_LOCALE_NONE`

## [3.2.2] - 2024-01-16

### Fixed

- Add `\Mds\PimPrint\CoreBundle\InDesign\Html\AbstractParser::createParagraph` parameter `$characterStyle` for styling characters for parsed html.

## [3.2.1] - 2023-12-05

### Fixed

- Selected elements update modes
    - Removed `static` access to `\Mds\PimPrint\CoreBundle\Service\ProjectsManager::isLocalizedProject`

## [3.2.0] - 2023-11-27

### Feature

- Asset remote File Storage support
    - Configuration `mds_pim_print_core.file_storage_mtime` to use Asset `modificationDate` if Storage returns wrong `lastModified` timestamp.

### Fixed

- Refactor `\Mds\PimPrint\CoreBundle\Service\ProjectsManager::$project` to non `static` member variable.

## [3.1.0] - 2023-10-11

### Feature

- Scalable Vector Graphics (SVG) support in `ImageBox` Command
    - SVG support can be disabled via `mds_pim_print_core.svg_support` configuration parameter.
    - Note: SVG support was dropped with CS4, but resumed with version CC 2020 (15.0)

## [3.0.0] - 2023-07-28

### Breaking changes

- Compatibility to mds PimPrint 2 InDesign Plugin with overall performance improvements in InDesign.
- Removed `\Mds\PimPrint\CoreBundle\InDesign\Command\TextBox::setDefaultUseLanguageLayer`
    - Use localized InDesign page-elements feature
- Renamed config parameter `create_update_info` to `create_update_layers`
- Refactored `\Mds\PimPrint\CoreBundle\Project\AbstractProject` to `\Mds\PimPrint\CoreBundle\Project\RenderingProject`
- All existing rendering project classes must extend `\Mds\PimPrint\CoreBundle\Project\RenderingProject now.
- All existing rendering project services must extend `mds.pimprint.core.rendering_project` instead of `mds.pimprint.core.abstract_project` now.

### Feature

- Plugin uses InDesign `Articles` feature for update info display, instead of layers.
- Localized InDesign page-elements
    - `\Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox::setLocalized`
        - Default setting for all `AbstractBox` types via:
            - `\Mds\PimPrint\CoreBundle\InDesign\Command\Traits\DefaultLocalizedTrait::setDefaultLocalized`
            - `\Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox::setDefaultLocalized`
- Enhanced `boxIdentReference` handling for content sensitive updated
    - `\Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait::$boxIdentReference`
- Add `\Mds\PimPrint\CoreBundle\InDesign\Command\FileBox` for placing clientside located files into InDesign picture page-elements.
- Add `\Mds\PimPrint\CoreBundle\InDesign\Command\DocumentSetup` for changing settings of the generated document.
- Add `\Mds\PimPrint\CoreBundle\InDesign\Command\DocumentTemplateSetup` to transfer the settings of the template document to the generated document.
- Enhanced `\Mds\PimPrint\CoreBundle\InDesign\Template\AbstractTemplate` for use in `DocumentSetup` command.
- Add `\Mds\PimPrint\CoreBundle\InDesign\Command\RemoveEmptyPages` for removing empty pages from the end of the document after generation.
- Add `setUseTemplatePosition` to `\Mds\PimPrint\CoreBundle\InDesign\Command\Traits\PositionTrait` to place InDesign page-elements at the template document position.
- Add `\Mds\PimPrint\CoreBundle\InDesign\Command\SortLayers` for ordering of layers in generated document.
- Localizing of InDesign documents with locale aware manual positioning:
    - `\Mds\PimPrint\CoreBundle\Project\LocalizedRenderingProject`
- ScaledImageBox for scaling and offset ImageBoxes:
    - `\Mds\PimPrint\CoreBundle\InDesign\Command\ImageBoxScaled`
- Add `\Mds\PimPrint\CoreBundle\InDesign\Command\VariableOutput` to output InDesign variables in the plugin.

### Fix

- Since InDesign 17 tables must have a `width` and `height` set when adding rows programatically. `\Mds\PimPrint\CoreBundle\InDesign\Command\Table` command now requires `width`
  and `height` parameter.
- In the plugin, locale names are displayed instead of language names for the selection of render locales.

### Refactor

- Remove create parameter in `\Mds\PimPrint\CoreBundle\InDesign\Command\SetLayer`. Layers are always created.

## [2.0.0] - 2022-09-13

### Features

- PHP 8 compatibility
- Pimcore 10 compatibility
- Symfony Authentication Manager support

## [1.3.2] - 2022-10-31

### Fixed

- Allow `image/x-eps` to be used in `ImageBox`.

## [1.3.1] - 2022-09-13

### Fixed

- PHP 7.3 compatibility
    - `\Mds\PimPrint\CoreBundle\Service\PluginParameters`
    - `\Mds\PimPrint\CoreBundle\Service\UrlGeneratorAccessor`

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

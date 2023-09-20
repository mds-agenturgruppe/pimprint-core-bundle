# mds PimPrint CoreBundle

mds PimPrint CoreBundle - The InDesign Printing Solution for Pimcore.

## Supported Pimcore Versions

- Pimcore 11: `mds-agenturgruppe/pimprint-core-bundle:^4.0`
- Pimcore 10: `mds-agenturgruppe/pimprint-core-bundle:^3.0`
- Pimcore 5/6: `mds-agenturgruppe/pimprint-core-bundle:^1.0`

## Prerequisites

- [PHP 8.1](https://secure.php.net/) or higher
- [Pimcore](https://github.com/pimcore/pimcore) Version 11.x

## Installation for Pimcore 11

Install `MdsPimPrintCoreBundle` into your Pimcore by issuing:

```bash
composer require mds-agenturgruppe/pimprint-core-bundle:^4.0
```

Enable `MdsPimPrintCoreBundle` in `config/bundles.php`:

```php
MdsPimPrintCoreBundle::class => ['all' => true],
```

Install `MdsPimPrintCoreBundle` with:

```bash
bin/console pimcore:bundle:install MdsPimPrintCoreBundle
```

For further details please refer the [installation guide](https://pimprint.mds.eu/docs/Getting_Started/Installation.html) in the documentation.

## InDesign Plugin

Document generation in InDesign is done with the mds.PimPrint plugin. Please email <a href="mailto:info@mds.eu?subject=PimPrint Plugin">info@mds.eu</a> to get the plugin.

## Further Information

* [PimPrint Website](https://pimprint.mds.eu)
* [Documentation](https://pimprint.mds.eu/docs)

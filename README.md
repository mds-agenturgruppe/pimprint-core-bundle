# mds PimPrint CoreBundle
mds PimPrint CoreBundle - The InDesign Printing Solution for Pimcore.

## Supported Pimcore Versions
- Pimcore 5/6: `mds-agenturgruppe/pimprint-core-bundle:^1.0`
- Pimcore 10: `mds-agenturgruppe/pimprint-core-bundle:^2.0`

## Prerequisites
- [PHP 7.1](https://secure.php.net/) or higher
- [Pimcore](https://github.com/pimcore/pimcore) Version 5.x/6.x

## Installation for Pimcore 5/6
Install the `MdsPimPrintCoreBundle` into your Pimcore by issuing:
```bash
composer require mds-agenturgruppe/pimprint-core-bundle:^1.0
```
Enable `MdsPimPrintCoreBundle` with:
```bash
bin/console pimcore:bundle:enable MdsPimPrintCoreBundle
```
Or enable in manually in the Pimcore extension manager.

## InDesign Plugin
Document generation in InDesign is done with the mds.PimPrint plugin. Please email <a href="mailto:info@mds.eu?subject=PimPrint Plugin">info@mds.eu</a> to get the plugin.

## Further Information
* [PimPrint Website](https://pimprint.mds.eu)
* [Documentation](https://pimprint.mds.eu/docs)

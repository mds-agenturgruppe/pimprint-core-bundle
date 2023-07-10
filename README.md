# mds PimPrint CoreBundle

mds PimPrint CoreBundle - The InDesign Printing Solution for Pimcore.

## Supported Pimcore Versions

- Pimcore 10: `mds-agenturgruppe/pimprint-core-bundle:^3.0`
- Pimcore 10: `mds-agenturgruppe/pimprint-core-bundle:^2.0`
- Pimcore 5/6: `mds-agenturgruppe/pimprint-core-bundle:^1.0`

## Prerequisites

- [PHP 8.0](https://secure.php.net/) or higher
- [Pimcore](https://github.com/pimcore/pimcore) Version 10.x

## Installation for Pimcore 10

Install the `MdsPimPrintCoreBundle` into your Pimcore by issuing:

```bash
composer require mds-agenturgruppe/pimprint-core-bundle:^3.0
```

Enable and install `MdsPimPrintCoreBundle` with:

```bash
bin/console pimcore:bundle:enable MdsPimPrintCoreBundle
bin/console pimcore:bundle:install MdsPimPrintCoreBundle
```

## InDesign Plugin

Document generation in InDesign is done with the mds.PimPrint plugin. Please email <a href="mailto:info@mds.eu?subject=PimPrint Plugin">info@mds.eu</a> to get the plugin.

## Further Information

* [PimPrint Website](https://pimprint.mds.eu)
* [Documentation](https://pimprint.mds.eu/docs)

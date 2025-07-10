# mds PimPrint CoreBundle

mds PimPrint CoreBundle — The flexible InDesign Printing Solution for Pimcore.

## Supported Pimcore Versions

| PimPrint Release | PimPrint InDesign Plugin | Supported Pimcore Version | Maintained |
|------------------|--------------------------|---------------------------|:----------:|
| `5.x`            | `2.x`                    | `12.x`                    |     ✅      |
| `4.x`            | `2.x`                    | `11.x`                    |     ✅      |
| `3.x`            | `2.x`                    | `10.6`                    |     ✅      |
| `2.x`            | `1.x`                    | `10.0 - 10.5`             |     ❌      |
| `1.x`            | `1.x`                    | `5.x`, `6.x`              |     ❌      |

## Prerequisites

- [PHP 8.3](https://secure.php.net/) or higher
- [Pimcore](https://github.com/pimcore/pimcore) Version 12.x

## Installation for Pimcore 12

Install `MdsPimPrintCoreBundle` into your Pimcore by issuing:

```bash
composer require mds-agenturgruppe/pimprint-core-bundle:^5.0
```

Enable `MdsPimPrintCoreBundle` in `config/bundles.php`:

```php
MdsPimPrintCoreBundle::class => ['all' => true],
```

Install `MdsPimPrintCoreBundle` with:

```bash
bin/console pimcore:bundle:install MdsPimPrintCoreBundle
```

For further details please refer to the [installation guide](https://pimprint.mds.eu/docs/Getting_Started/Installation.html) in the documentation.

## InDesign Plugin

Document generation in InDesign is done with the mds.PimPrint plugin. Please email <a href="mailto:info@mds.eu?subject=PimPrint Plugin">info@mds.eu</a> to get the plugin.

## Further Information

* [PimPrint Website](https://pimprint.mds.eu)
* [Documentation](https://pimprint.mds.eu/docs)

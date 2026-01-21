# PimPrint-Demo Pimcore Installation

PimPrint-Demo is a Pimcore bundle that is build for the public available [Pimcore Demo](https://github.com/pimcore/demo).

## Supported Pimcore Demos Versions

| Pimcore Demo | PimPrint Demo | PimPrint Maintained |
|--------------|---------------|:-------------------:|
| `2025.x`     | `5.x`         |          ✅          |
| `2024.4`     | `4.x`         |          ✅          |

The following guide assumes you have a running Pimcore demo in matching version.

## Installing PimPrint Demo

Install `MdsPimPrintDemoBundle` matching your Pimcore Demo version by issuing:

```bash
composer require mds-agenturgruppe/pimprint-demo-bundle:^5.0
```

Enable `MdsPimPrintCoreBundle` and `MdsPimPrintDemoBundle` in `config/bundles.php`:

```php
\Mds\PimPrint\CoreBundle\MdsPimPrintCoreBundle::class => ['all' => true],
\Mds\PimPrint\DemoBundle\MdsPimPrintDemoBundle::class => ['all' => true],
```

```bash
bin/console pimcore:bundle:install MdsPimPrintCoreBundle
```

> For `MdsPimPrintCoreBundle` installation details please refer
> the [installation instruction page](../01_Getting_Started/01_Installation.md#page_Installing_PimPrint_into_Pimcore_12).
>

For [template change in demo projects](./05_CarsDemo.md#page_Changing_the_Template) `MdsPimPrintDemoBundle` creates predefined properties and imports InDesign template files into
the Pimcore asset management. If you want to test this, execute the migrations by issuing:

```bash
bin/console doctrine:migrations:migrate --prefix=Mds\\PimPrint\\DemoBundle\\Migrations
```

## Installing the InDesign plugin

In order to generate InDesign documents with the PimPrint-Demo [install the mds.PimPrint InDesign plugin](../01_Getting_Started/01_Installation.md)
and [create a server connection](../20_InDesign_Plugin/00_Server_connection.md) to your Pimcore Demo installation.

# PimPrint-Demo Pimcore Installation
PimPrint-Demo is a Pimcore bundle that is build for the public available [Pimcore Demo](https://github.com/pimcore/demo/tree/1.6). If you already have Pimcore Demo installed directly jump to the [Installing PimPrint Demo](#page_Installing_PimPrint_Demo).

The following guide assumes you use the Pimcore 6 demo. For other Pimcore versions, please refer to the [Supported Pimcore Versions](../README.md#page_Supported_Pimcore_Versions) section.

## Installing Pimcore Demo
Install the [Pimcore 6 Demo](https://github.com/pimcore/demo/tree/1.6) as described in the [Readme](https://github.com/pimcore/demo/blob/1.6/README.md#getting-started).

## Installing PimPrint Demo
Install the `MdsPimPrintDemoBundle` into your Pimcore Demo by issuing:
```bash
composer require mds-agenturgruppe/pimprint-demo-bundle:^1.0
```

Enable `MdsPimPrintCoreBundle` and `MdsPimPrintDemoBundle` by issuing following commands in exactly this order:
```bash
bin/console pimcore:bundle:enable MdsPimPrintCoreBundle
bin/console pimcore:bundle:enable MdsPimPrintDemoBundle
```

For [template change in demo projects](./03_DataPrint_Demos.md#page_Changing_the_Template) `MdsPimPrintDemoBundle` creates predefined properties and imports InDesign template files into the Pimcore asset management. If you want to test this, execute the migrations by issuing:
```bash
bin/console pimcore:migrations:migrate -b MdsPimPrintDemoBundle
```
 
## Installing the InDesign plugin
In order to generate InDesign documents with the PimPrint-Demo [install the mds.PimPrint InDesign plugin](../01_Getting_Started/01_Installation.md) and [create a server connection](../20_InDesign_Plugin/00_Server_connection.md) to your Pimcore Demo installation.

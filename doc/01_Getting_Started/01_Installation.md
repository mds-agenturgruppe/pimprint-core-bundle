# PimPrint for Pimcore Installation
The following guide assumes you have a running [Pimcore](https://pimcore.com) 5.x/6.x installation. For installing Pimcore 6 please visit the [Pimcore Installation documentation](https://pimcore.com/docs/pimcore/6.9/Development_Documentation/Getting_Started/Installation.html).

For other Pimcore versions, please refer to the [Supported Pimcore Versions](../README.md#page_Supported_Pimcore_Versions) section.

## Installing PimPrint into Pimcore 5.x/6.x
Install the `MdsPimPrintCoreBundle` into your Pimcore by issuing:
```bash
composer require mds-agenturgruppe/pimprint-core-bundle:^1.0
```
Enable `MdsPimPrintCoreBundle` with:
```bash
bin/console pimcore:bundle:enable MdsPimPrintCoreBundle
```

## Installing PimPrint InDesign-Plugin
The InDesign plugin provided by mds as an CEP-Extension ZXP-File. Please email <a href="mailto:info@mds.eu?subject=PimPrint Plugin">info@mds.eu</a> to get the plugin and further information.
 
For easy installation of the ZXP-File we recommend [Anastasiy’s Extension Manager](https://install.anastasiy.com) available for Mac and Windows platforms.
Install the extension manager, launch it and choose the `mds.pimprint.indesign.plugin-x.x.x.zxp` extension file to install.   

## Connecting the InDesign-Plugin with Pimcore
Start InDesign and open the mds.PimPrint Plugin from the InDesign main menu.
![InDesign - Open PimPrint Plugin](../img/indesign-open_pimprint.png)

The first time the plugin is started a server connection must be configured.
![Plugin - No connection configured](../img/plugin-first_start.png)

Click on the _Settings_ button to open the plugin settings pane. Please refer to the [Server connection page](../20_InDesign_Plugin/00_Server_connection.md) to learn how to configure a server connection.


 


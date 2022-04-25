# PimPrint for Pimcore Installation
The following guide assumes you have a running [Pimcore](https://pimcore.com) 5.x/6.x installation. For installing Pimcore please visit the [Pimcore Installation documentation](https://pimcore.com/docs/pimcore/current/Development_Documentation/Getting_Started/Installation.html).

## Installing PimPrint into Pimcore
Install the `MdsPimPrintCoreBundle` into your Pimcore by issuing:
```bash
composer require mds-agenturgruppe/pimprint-core-bundle
```
Enable `MdsPimPrintCoreBundle` with:
```bash
bin/console pimcore:bundle:enable MdsPimPrintCoreBundle
```
Or enable it manually in the Pimcore extension manager.

## Installing PimPrint InDesign-Plugin
The InDesign plugin provided by mds as an CEP-Extension ZXP-File. Please email <a href="mailto:info@mds.eu?subject=PimPrint Plugin">info@mds.eu</a> to get the plugin and further information.
 
For easy installation of the ZXP-File we recommend [Anastasiy’s Extension Manager](https://install.anastasiy.com) availible for Mac and Windows platforms.
Install the extension manager, launch it and choose the `mds.pimprint.indesign.plugin-x.x.x.zxp` extension file to install.   

## Connecting the InDesign-Plugin with Pimcore
Start InDesign and open the mds.PimPrint Plugin from the InDesign main menu.
![InDesign - Open PimPrint Plugin](../img/indesign-open_pimprint.png)

The first time the plugin is started a server connection must be configured.
![Plugin - No connection configured](../img/plugin-first_start.png)

Click on the _Settings_ button to open the plugin settings pane. Please refer to the [Server connection page](../20_InDesign_Plugin/00_Server_connection.md) to learn how to configure a server connection.


 


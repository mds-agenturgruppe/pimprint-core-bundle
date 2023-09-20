# PimPrint for Pimcore Installation

## Prerequisites

The following guide assumes you have a running [Pimcore](https://pimcore.com) 11.x installation. For installing Pimcore please visit
the [Pimcore Getting Started documentation](https://pimcore.com/docs/platform/Pimcore/Getting_Started).

For other Pimcore versions, please refer to the [Supported Pimcore Versions](../README.md#page_Supported_Pimcore_Versions) section.

## Installing PimPrint into Pimcore 11

Install `MdsPimPrintCoreBundle` into your Pimcore by issuing:

```bash
composer require mds-agenturgruppe/pimprint-core-bundle:^4.0
```

Enable `MdsPimPrintCoreBundle` in `config/bundles.php`:

```php
MdsPimPrintCoreBundle::class => ['all' => true],
```

PimPrint needs a Symfony security firewall for handling the user authentication process.
Add the firewall configuration right after `pimcore_admin` in the `firewall` section of your `config/packages/security.yaml` file.

```yaml
pimprint_api: '%mds.pimprint.core.firewall_settings%'
```

To automatically add the firewall configuration by the installer issue:

```shell
 bin/console pimcore:bundle:install MdsPimPrintCoreBundle -n
```

> <strong>Attention</strong>:<br>
> Do not be surprised that your `security.yaml` looks ugly after automatic installation!<br>
> `\Symfony\Component\Yaml\Yaml::dump()` sometimes creates really ugly files.
> 
> We recomend to add the firewall configuration manually to your `security.yaml`.

## Installing PimPrint InDesign-Plugin

The InDesign plugin provided by mds as an CEP-Extension ZXP-File. Please email <a href="mailto:info@mds.eu?subject=PimPrint Plugin">info@mds.eu</a> to get the plugin and further
information.

For easy installation of the ZXP-File we recommend [Anastasiy’s Extension Manager](https://install.anastasiy.com) available for Mac and Windows platforms.
Install the extension manager, launch it and choose the `mds.pimprint.v2.indesign.plugin-2.x.x.zxp` extension file to install.

## Connecting the InDesign-Plugin with Pimcore

Start InDesign and open the mds.PimPrint Plugin from the InDesign main menu.
![InDesign - Open PimPrint Plugin](../img/indesign-open_pimprint.png)

The first time the plugin is started a server connection must be configured.
![Plugin - No connection configured](../img/plugin-first_start.png)

Click on the _Settings_ button to open the plugin settings pane. Please refer to the [Server connection page](../20_InDesign_Plugin/00_Server_connection.md) to learn how to
configure a server connection.


 


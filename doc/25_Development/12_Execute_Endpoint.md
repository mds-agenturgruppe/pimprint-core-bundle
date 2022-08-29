# Execute Endpoint
Endpoint route:
```
/pimprint-api/project/{identifier}/run
```
Execute endpoint is call by the plugin when a publication is generated. The selected project `identifyer` is passed as parameter in the URL. In addition to the selected project execute endpoint needs more required parameters to control the generated publication: 

| Parameter          | Documentation                                                                      |
|--------------------|------------------------------------------------------------------------------------|
| `publicationIdent` | Publication `identifyer` returned by [Project Endpoint](./11_Project_Endpoint.md). |
| `renderLanguage`   | Publication language returned by [Project Endpoint](./11_Project_Endpoint.md).     |

For a documentation of all parameters the Plugin sends to the server refer to the Service `\Mds\PimPrint\CoreBundle\Service\PluginParameters` in the API documentation or directly to the source.
 
When accessing the endpoint with a browser you can pass the required parameters with GET: 
```
/pimprint-api/project/dataPrintCarBrochure/run?publicationIdent=556&renderLanguage=de
```

The response below shows a shorted example of generation instructions for the Plugin from the [PimPrint-Demo](../05_PimPrint-Demo/README.md):

```json
{
  "commands": [
    {
      "cmd": "opendoc",
      "type": "usecurrent",
      "language": ""
    },
    {
      "cmd": "opendoc",
      "type": "template",
      "language": "0",
      "name": "PimPrint-DataPrintDemo_blue.indd"
    }
  ],
  "preProcess": [],
  "postProcess": [],
  "success": true,
  "messages": [],
  "debugMode": false,
  "images": {},
  "settings": {}
}
```

By accessing this endpoint you can check and debug the `buildPublication()` method described in the [Getting Started section](../01_Getting_Started/02_Project_Service.md#page_Generate_a_publication), creating the [Commands](../15_Rendering_Commands.md) to send the rendering instructions to the Plugin.

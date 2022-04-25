# Projects Endpoint
Endpoint route:
```
/pimprint-api/projects
```
Projects endpoint is call by the plugin to get the list of registered projects from the server and returns this JSON structure:
```json
{
  "projects": [
    {
      "name": "Getting Started",
      "identifier": "gettingStarted"
    },
    {
      "name": "Command Demo",
      "identifier": "commandDemo"
    },
    {
      "name": "Car Brochure",
      "identifier": "dataPrintCarBrochure"
    },
    {
      "name": "Car List",
      "identifier": "dataPrintCarList"
    },
    {
      "name": "AccessoryPart List",
      "identifier": "dataPrintAccessoryPartList"
    }
  ],
  "success": true,
  "messages": [],
  "debugMode": false
}
```
By accessing this endpoint you can verify which projects are registered in `MdsPimPrintCoreBundle` and you get the `identifier` of each project, which is used in all other API endpoints as parameter to identify the requested project.

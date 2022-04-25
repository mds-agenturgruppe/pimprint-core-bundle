# Projects Endpoint
Endpoint route:
```
/pimprint-api/project/{identifier}
```
Project endpoint is call by the plugin when a project is selected to load project settings and publications the projects can generate. The `identifyer` of the selected project is passed as  parameter.

The response below shows a shorted example with some publications from the [PimPrint-Demo](../05_PimPrint-Demo/README.md) when accessing project identified by `dataPrintCarBrochure` requesting the route `/pimprint-api/project/dataPrintCarBrochure`:

```json
{
  "formFields": {...},
  "languages": [
    {
      "iso": "en",
      "label": "English"
    },
  ],
  "publications": [
    {
      "identifier": "390",
      "label": "Cars",
      "children": [
        {
          "identifier": "556",
          "label": "Commercial Cars",
          "children": []
        },
      ]
    },
    {
      "identifier": "3",
      "label": "Manufacturer",
      "children": [
        {
          "identifier": "80",
          "label": "AC Cars",
          "children": []
        },
      ]
    }
  ],
  "success": true,
  "messages": [],
  "debugMode": false,
  "images": [],
  "settings": {...}
}
```
The `publications` array withing the JSON is created by the `getPublicationsTree()` method implemented in project service, described in the [Getting Started section](../01_Getting_Started/02_Project_Service.md#page_Defining_publications_to_generate).

By accessing this endpoint you can check and debug the `getPublicationsTree()` method for this project, delivering availible publications to the Plugin. Note again the `identifier` key of each publication, as this will be the parameter in [execute Endpoint](12_Execute_Endpoint.md) when the Plugin requests the generation of a print product.

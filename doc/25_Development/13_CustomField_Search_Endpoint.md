# CustomField Search Endpoint
Endpoint route:
```
 /pimprint-api/project/{identifier}/custom-search/{customField}
```
This endpoint is called by the plugin when a [custom field search](../10_Rendering_Projects/01_CustomFields/03_CustomField_Search.md) is executed. The selected project `identifier` and `customField` param is passed as parameter in the URL. The phrase `search` is sent as POST parameter from the InDesign plugin. 

| Parameter | Documentation                                        |
|-----------|------------------------------------------------------|
| `search`  | Search phrase `search`entered in the InDesign plugin |

 
When accessing the endpoint with a browser you can pass the `search` parameters with GET: 
```
/pimprint-api/project/gettingStarted/mySearch?search=searchPhrase
```

By accessing this endpoint you can check and debug the `search()` method described on the [custom field search page](../10_Rendering_Projects/01_CustomFields/03_CustomField_Search.md).

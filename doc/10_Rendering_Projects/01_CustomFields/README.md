# InDesign Plugin custom fields

PimPrint offers the possibility to define custom fields for each project. These fields can be used for projects where additional user input is needed.

This additional user input can be used while rendering, or as a selection of the content of the InDesign document to generate (replacement to
the [PimPrint factory field Publication](../00_FactoryFields.md#page_Publication_field)).

* [Custom field types](#page_Custom_field_types)
* [Using custom fields](#page_Using_custom_fields)
    * [Adding a custom field](#page_Adding_a_custom_field)
    * [Accessing the input in the rendering process](#page_Accessing_the_input_in_the_rendering_process)
* [Possible usages](#page_Possible_usages)

## Custom field types

PimPrint offers the following CustomFields types:

* [Input](./01_CustomField_Input.md)
* [Select](./02_CustomField_Select.md)
* [Search](./03_CustomField_Search.md)

## Using custom fields

To use a custom field in your project you have to register an instance (normal object or Symfony service), which
extends `\Mds\PimPrint\CoreBundle\InDesign\CustomField\AbstractField`.

Custom fields are displayed in the InDesign Plugin in the order you register them in your project.

You can access the input of each custom field when the rendering instructions are generated for your project
in `\Mds\PimPrint\CoreBundle\Project\AbstractProject::buildPublication()`.

### Adding a custom field

The custom field must at least have a `param` defined, which is used to identify the field and read the input from the request. The `param` must be unique for all fields added to a
rendering project.

Example of how to add a `Input` field to a project:

```php
<?php
use Mds\PimPrint\CoreBundle\InDesign\CustomField\Input;

class GettingStarted extends AbstractProject
{
    /**
     * Initializes project specific InDesign plugin form fields
     *
     * @return void
     */
    protected function initCustomFormFields(): void
    {
        $field = new Input();
        $field->setParam('myField')
              ->setLabel('Label in InDesign Plugin');
        
        $this->addCustomFormField($field);
    }
}
```

### Accessing the input in the rendering process

The value of a custom field in the generation request is identified by its `param`. In the example below the custom field has the param `myField`.

You can access the value of a custom field with:

```php
class GettingStarted extends AbstractProject
{
    /**
     * Generates InDesign Commands to build the selected publication in InDesign.
     *
     * @return void
     */
    public function buildPublication(): void
    {
       $input = $this->pluginParams->getCustomField('myField');
    }
}
```

## Possible usages

- Product search fields to give the user a search based selection of the rendered content. Suitable for projects with many hundreds of products, where the default publication
  factor field is not handy to use.
- Selection to display prices or currencies, etc.
- Definition of price date ranges.
- Manual selection of the used InDesign template to vary layouts upon generation in InDesign.
- Use values from a manual input fields in the generation process like voucher code, etc.
- Connect search fields to load data from 3rd party APIs and use this data in the rendering process.
- etc. 


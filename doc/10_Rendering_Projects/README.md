# Rendering Projects

Project services create all information displayed in InDesign plugin to select a publication to generate and create the rendering instructions to build the InDesign document.

For a basic reference how you create your own project service and use its default features, please refer to
the [PimPrint rendering project page](../01_Getting_Started/02_Project_Service.md) in the [GettingStarted Section](../01_Getting_Started/README.md).

This chapter describes following aspects of project rendering services from a more technical point of view:

1. [Configuring the InDesign plugin FactoryFields](./00_FactoryFields.md)
2. [Creating CustomField in the InDesign plugin](./01_CustomFields/README.md)
3. [Project Configuration Reference](./03_Configuration_Reference.md)

> __Topics to cover:__
> * Extending the PublicationLoader
> * Assigning InDesign templates
    >
* Default via Config
>     * Dynamic by overwriting getTemplate() method
> * Content aware document update
> * Creating and styling text with ParagraphStyles and CharacterStyles
> * Transforming and styling HTML to InDesign elements.

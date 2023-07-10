# PimPrint Overview

mds.PimPrint is an InDesign plugin that is able to generate InDesign documents by placing elements (text-frames, image-frames, table-frames, etc.) and filling them with content.
The plugin itself has no rendering logic implemented. It only provides a user interface to select the desired print product to create and is able to execute rendering instructions
generated serverside in Pimcore.

As Pimcore itself PimPrint is no "out of the box solution". PimPrint offers a framework with an PHP API to build the communication with the Plugin. PimPrint doesn't make any
requirements to the data model or uses an own data model. Therefore, PimPrint can use any data from any Pimcore and create any data interpretation logic to build up the print
product in InDesign.

Through native integration with Pimcore no middleware or data exports are required. All data for document generation in InDesign is loaded directly from the Pimcore database when
the generation process takes place, assuring up-to-date data with every generation.

With the [PimPrint-Demo](./05_PimPrint-Demo/README.md) you can see PimPrint in action in your own InDesign. For an introduction on how to create your own renderings have a look at
the [Getting Started](./01_Getting_Started/README.md) section. 

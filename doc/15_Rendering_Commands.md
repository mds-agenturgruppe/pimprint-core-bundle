# PimPrint Commands 
Rendering InDesign documents is done by the PimPrint InDesign. It sequentially executes commands send by the server.
A set of PHP classes is used to create this Commands sent to the Plugin.

The entire list of PimPrint Commands is indicated below:

| Name | Description |
|---|---|
| CheckNewPage | Command to create automatic page breaks when placed elements exceed the defined page size. |
| CopyBox | Places an InDesign template element into the generated Document, including positioning and resizing.
| ExecuteScript | Executes arbitrary InDesign JavaScript. |
| GoToPage | Jumps to a page for placing elements in. |
| GroupEnd | Creates a group of elements in InDesign with all elements sent after the starting GroupStart command. |
| GroupStart | Starts creating a element group in InDesign. All commands following a GroupStart command will be grouped together. |
| ImageBox | Places an Pimcore Asset in the document. |
| NextPage | Jumps to the next page for placing elements in. |
| OpenDocument | Opens the document to generate and template document.<br><br>Command is automatically issued when calling `AbsctactProject::startRendering` in `AbsctactProject::buildPublication` implementation. |
| PageMessage | Command to send descriptive messages from rendering process to the InDesign plugin to give notifying or verbose feedback. Messages can be shown in the generation overlay or rendered onto the page in a separate InDesign layer outside of the page bounds. |
| RemoveEmptyLayers | Executes a InDesign JavaScript to remove all empty layers from the generated document.<br>Command is automatically issued when calling `AbsctactProject::stopRendering` in `AbsctactProject::buildPublication` implementation. |
| SetLayer | Sets the active layer in InDesign. All following elements will be places in this layer. |
| SplitTable | Command automatically splits tables over multiple pages repeating head and footer rows. |
| Table | Command to create InDesign tables. With `InDesign\Html\FragmentParser` it is possible to transform HTML tables into InDesign tables.  |
| Template | Command to create page layout supporting single page and facing page documents. Registered elements will be automatically placed onto a page, when generation places elements on a new page. |
| TextBox | Command to create text boxes in InDesign. |
| Variable  | Sets an variable in InDesign that can be used when placing elements. |
| MaxValue  | Sets an variable in InDesign with the maximum value of other variables. |
| Minvalue | Sets an variable in InDesign with the minimum value of other variables. |
 
> __Topics to cover:__ 
> * Concept and usage of relative positioning in context with variables.
> * Concept of CheckNewPage Command.
> * SplitTable pre Commands for placing content variables for positioning.

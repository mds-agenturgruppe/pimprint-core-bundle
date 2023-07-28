# LocalizationDemo

PimPrint can be used to create localised InDesign documents.

A document is first created in the so-called master language. Layout adjustments can be made manually in this language. When other languages are generated, the positions and
element sizes of the master language are adopted, whereby the layout adjustments previously made are automatically transferred to the other languages.

The [LocalizationDemo]https://github.com/mds-agenturgruppe/pimprint-demo-bundle/tree/3.x/src/Project/LocalizationDemo/LocalizationProject.php shows features of PimPrint to explain
this concept and how it works.

## Development

The project service class must extend `\Mds\PimPrint\CoreBundle\Project\MasterLocaleRenderingProject`.

```php
<?php
use Mds\PimPrint\CoreBundle\Project\MasterLocaleRenderingProject;

class LocalizationProject extends MasterLocaleRenderingProject
{
}
```

Project services must use `mds.pimprint.core.master_locale_rendering_project` as `parent`.

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: false

        Mds\PimPrint\DemoBundle\Project\LocalizationDemo\LocalizationProject:
            parent: mds.pimprint.core.master_locale_rendering_project
            public: true
            autowire: true
            autoconfigure: false
```  

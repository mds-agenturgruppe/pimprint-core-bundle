<?php
/**
 * mds PimPrint
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license    https://pimprint.mds.eu/license GPLv3
 */

namespace Mds\PimPrint\CoreBundle\Service\InDesign;

use Pimcore\Model\DataObject\AbstractObject;

/**
 * Class AbstractPublicationTreeBuilder
 */
abstract class AbstractPublicationTreeBuilder
{
    /**
     * Builds recursive object tree.
     *
     * @param AbstractObject $object
     *
     * @return array
     */
    protected function buildObjectTree(AbstractObject $object): array
    {
        $tree = $this->buildTreeElementFromObject($object);
        foreach ($object->getChildren() as $child) {
            if (false === $this->showObjectInTree($child)) {
                continue;
            }
            $tree['children'][] = $this->buildObjectTree($child);
        }

        return $tree;
    }

    /**
     * Builds and publication tree element array for $object.
     *
     * @param AbstractObject $object
     *
     * @return array
     */
    protected function buildTreeElementFromObject(AbstractObject $object)
    {
        return [
            'identifier' => $object->getId(),
            'label'      => $object->getKey(),
            'children'   => [],
        ];
    }

    /**
     * Template method.
     * Returns true if $object is a renderable Publication and should be displayed in PimPrint Plugin an publication.
     *
     * This abstract implementation only checks for visibility to current user. Overwrite for project domain use.
     *
     * @param AbstractObject $object
     *
     * @return bool
     */
    protected function showObjectInTree(AbstractObject $object)
    {
        return $object->isAllowed('view');
    }
}

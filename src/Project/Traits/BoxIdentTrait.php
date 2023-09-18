<?php
/**
 * mds Agenturgruppe GmbH
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 */

namespace Mds\PimPrint\CoreBundle\Project\Traits;

/**
 * Trait BoxIdentTrait
 *
 * @package Mds\PimPrint\CoreBundle\Project\Traits
 */
trait BoxIdentTrait
{
    /**
     * Reference string for box ident generation.
     * Used to generate unique content related box idents to create coupling between Pimcore content
     * (Objects, Assets, Documents) and InDesign elements.
     * Typical usage: use Object-Ids here.
     *
     * @var string
     */
    private $boxIdentReference = '';

    /**
     * Postfix used in generic box ident generation.
     *
     * @var string
     */
    private $boxIdentGenericPostfix = '';

    /**
     * Internal member for push/pop handling
     *
     * @var array
     */
    private $lastBoxIdentReferences = [];

    /**
     * Returns $boxIdentReference.
     *
     * @return string
     * @see \Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait::$boxIdentReference
     */
    public function getBoxIdentReference(): string
    {
        return $this->boxIdentReference;
    }

    /**
     * Sets $ident as boxIdentReference for content aware updates.
     *
     * @param string $ident
     *
     * @see \Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait::$boxIdentReference
     */
    public function setBoxIdentReference(string $ident)
    {
        $this->boxIdentReference = $ident;
    }

    /**
     * Appends $ident to boxIdentReference for content aware updates.
     *
     * @param string $ident
     * @param string $prefix
     *
     * @see \Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait::$boxIdentReference
     */
    public function appendToBoxIdentReference(string $ident, string $prefix = '-')
    {
        $this->setBoxIdentReference(
            $this->getBoxIdentReference() . $prefix . $ident
        );
    }

    /**
     * Pushes $ident with optional $prefix to boxIdentReference.
     * Pushes the current boxIdentReference to reset it with popIdentReference.
     *
     * @param string $ident
     * @param string $prefix
     *
     * @see \Mds\PimPrint\CoreBundle\Project\Traits\BoxIdentTrait::$boxIdentReference
     */
    public function pushBoxIdentReference(string $ident, string $prefix = '-')
    {
        $this->lastBoxIdentReferences[] = $this->getBoxIdentReference();
        $this->appendToBoxIdentReference($ident, $prefix);
    }

    /**
     * Restores boxIdentReference to last value in references stack
     */
    public function popIdentReference()
    {
        if (empty($this->lastBoxIdentReferences)) {
            return;
        }
        $ident = array_pop($this->lastBoxIdentReferences);
        if (empty($ident)) {
            return;
        }

        $this->setBoxIdentReference($ident);
    }

    /**
     * Returns $boxIdentGenericPostfix.
     *
     * @return string
     */
    public function getBoxIdentGenericPostfix(): string
    {
        return $this->boxIdentGenericPostfix;
    }

    /**
     * Sets $boxIdentGenericPostfix.
     *
     * @param string $postfix
     */
    public function setBoxIdentGenericPostfix(string $postfix)
    {
        $this->boxIdentGenericPostfix = $postfix;
    }
}

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

namespace Mds\PimPrint\CoreBundle\InDesign\Command;

/**
 * Command to transfer the settings of the template document to the generated document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class DocumentTemplateSetup extends AbstractCommand
{
    /**
     * Command name
     *
     * @var string
     */
    const CMD = 'documentPropertiesFromTemplate';

    /**
     * Properties to set from the template document
     *
     * @var array
     */
    private array $properties = [
        'pageWidth'       => true,
        'pageHeight'      => true,
        'bleedOffset'     => true,
        'facingPages'     => true,
        'startPageNumber' => false,
        'margins'         => true,
        'pagesCount'      => false,
    ];

    /**
     * DocumentTemplateSetup constructor
     *
     * @param bool $dimensions @see \Mds\PimPrint\CoreBundle\InDesign\Command\DocumentTemplateSetup::setDimensions
     * @param bool $bleed @see \Mds\PimPrint\CoreBundle\InDesign\Command\DocumentTemplateSetup::setBleed
     * @param bool $margins @see \Mds\PimPrint\CoreBundle\InDesign\Command\DocumentTemplateSetup::setMargins
     * @param bool $facingPages @see \Mds\PimPrint\CoreBundle\InDesign\Command\DocumentTemplateSetup::setFacingPages
     *
     * @throws \Exception
     */
    public function __construct(
        bool $dimensions = true,
        bool $bleed = true,
        bool $margins = true,
        bool $facingPages = true,
    ) {
        $this->initParams($this->properties);

        $this->setDimensions($dimensions);
        $this->setBleed($bleed);
        $this->setMargins($margins);
        $this->setFacingPages($facingPages);
    }

    /**
     * Sets $transfer to transfer width and height from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setDimensions(bool $transfer): DocumentTemplateSetup
    {
        $this->setWidth($transfer);
        $this->setHeight($transfer);

        return $this;
    }

    /**
     * Sets $transfer to transfer document width from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setWidth(bool $transfer): DocumentTemplateSetup
    {
        $this->setParam('pageWidth', $transfer);

        return $this;
    }

    /**
     * Sets $transfer to transfer document height from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setHeight(bool $transfer): DocumentTemplateSetup
    {
        $this->setParam('pageHeight', $transfer);

        return $this;
    }

    /**
     * Sets $transfer to transfer document margins from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setMargins(bool $transfer): DocumentTemplateSetup
    {
        $this->setParam('margins', $transfer);

        return $this;
    }

    /**
     * Sets $transfer to transfer document bleed from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setBleed(bool $transfer): DocumentTemplateSetup
    {
        $this->setParam('bleedOffset', $transfer);

        return $this;
    }

    /**
     * Sets $transfer to transfer document facingPages from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setFacingPages(bool $transfer): DocumentTemplateSetup
    {
        $this->setParam('facingPages', $transfer);

        return $this;
    }

    /**
     * Sets $transfer to transfer document startPageNumber from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setStartNumber(bool $transfer): DocumentTemplateSetup
    {
        $this->setParam('startPageNumber', $transfer);

        return $this;
    }

    /**
     * Sets $transfer to transfer document pagesCount from template document.
     *
     * @param bool $transfer
     *
     * @return DocumentTemplateSetup
     * @throws \Exception
     */
    public function setPageCount(bool $transfer): DocumentTemplateSetup
    {
        $this->setParam('pagesCount', $transfer);

        return $this;
    }
}

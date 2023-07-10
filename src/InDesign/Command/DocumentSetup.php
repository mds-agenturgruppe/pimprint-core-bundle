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

use Mds\PimPrint\CoreBundle\InDesign\Template\AbstractTemplate;

/**
 * Command for changing settings of the generated document.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class DocumentSetup extends AbstractCommand
{
    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'documentProperties';

    /**
     * InDesign minimum page dimension
     *
     * @var float
     */
    private const MIN_DIMENSION = 0.353;

    /**
     * InDesign maximum page dimension
     *
     * @var float
     */
    private const MAX_DIMENSION = 5486.4;

    /**
     * InDesign maximum page margin
     *
     * @var float
     */
    private const MAX_MARGIN = 3527.778;

    /**
     * InDesign maximum page bleed
     *
     * @var float
     */
    private const MAX_BLEED = 152.4;

    /**
     * Available command params with default values.
     *
     * @var array
     */
    protected array $availableParams = [
        'properties' => [],
    ];

    /**
     * Properties to set to the document
     *
     * @var array
     */
    private array $properties = [
        'pageWidth'       => null,
        'pageHeight'      => null,
        'bleedOffset'     => null,
        'facingPages'     => null,
        'startPageNumber' => null,
        'pagesCount'      => null,
        'margins'         => null,
    ];

    /**
     * Margins to set to the document.
     *
     * @var array
     */
    private array $margins = [
        'top'    => null,
        'bottom' => null,
        'left'   => null,
        'right'  => null,
    ];

    /**
     * DocumentSetup constructor.
     *
     * @param AbstractTemplate|null $template
     * @param int|null              $numberOfPages
     * @param bool|null             $startOnLeftPage
     *
     * @throws \Exception
     */
    public function __construct(
        AbstractTemplate $template = null,
        ?int $numberOfPages = null,
        ?bool $startOnLeftPage = null
    ) {
        if ($template) {
            $this->setFromTemplate($template);
        }
        if ($numberOfPages) {
            $this->setNumberOfPages($numberOfPages);
        }
        if (null !== $startOnLeftPage) {
            $this->setStartOnLeftPage($startOnLeftPage);
        }
    }

    /**
     * Sets document properties from $template instance.
     *
     * @param AbstractTemplate $template
     *
     * @return void
     * @throws \Exception
     */
    public function setFromTemplate(AbstractTemplate $template): void
    {
        $this->setPageDimensions($template->getPageWidth(), $template->getPageHeight());
        $this->setMargins(
            $template::PAGE_MARGIN_TOP,
            $template::PAGE_MARGIN_BOTTOM,
            $template::PAGE_MARGIN_LEFT,
            $template::PAGE_MARGIN_RIGHT
        );
        $this->setFacingPages($template::FACING_PAGES);
        $this->setStartOnLeftPage($template::FACING_PAGE_START_ON_LEFT);
        $this->setBleed($template::PAGE_BLEED);
    }

    /**
     * Sets $width and $height document page dimensions.
     *
     * @param float $width
     * @param float $height
     *
     * @return DocumentSetup
     * @throws \Exception
     */
    public function setPageDimensions(float $width, float $height): DocumentSetup
    {
        $this->validateDimension($width);
        $this->properties['pageWidth'] = $width . 'mm';

        $this->validateDimension($height);
        $this->properties['pageHeight'] = $height . 'mm';

        return $this;
    }

    /**
     * Sets $top, $bottom, $left and $right document page margins.
     *
     * @param float $top
     * @param float $bottom
     * @param float $left
     * @param float $right
     *
     * @return DocumentSetup
     * @throws \Exception
     */
    public function setMargins(float $top, float $bottom, float $left, float $right): DocumentSetup
    {
        $this->validateMargin($top);
        $this->margins['top'] = $top . 'mm';

        $this->validateMargin($bottom);
        $this->margins['bottom'] = $bottom . 'mm';

        $this->validateMargin($left);
        $this->margins['left'] = $left . 'mm';

        $this->validateMargin($right);
        $this->margins['right'] = $right . 'mm';

        return $this;
    }

    /**
     * Sets document $facingPages
     *
     * @param bool $facingPages
     *
     * @return DocumentSetup
     */
    public function setFacingPages(bool $facingPages): DocumentSetup
    {
        $this->properties['facingPages'] = $facingPages;

        return $this;
    }

    /**
     * Sets $startOnLeftPage for facing pages
     *
     * @param bool $startOnLeftPage
     *
     * @return DocumentSetup
     */
    public function setStartOnLeftPage(bool $startOnLeftPage): DocumentSetup
    {
        $this->properties['startPageNumber'] = 1;

        if ($startOnLeftPage) {
            $this->properties['startPageNumber'] = 2;
        }

        return $this;
    }

    /**
     * Sets $bleed document bleed.
     *
     * @param float $bleed
     *
     * @return DocumentSetup
     * @throws \Exception
     */
    public function setBleed(float $bleed): DocumentSetup
    {
        if ($bleed < 0 && $bleed > self::MAX_BLEED) {
            throw new \Exception(
                sprintf(
                    "Invalid page bleed: '%s'. Page bleed must be between 0 - %s",
                    $bleed,
                    self::MAX_BLEED
                )
            );
        }
        $this->properties['bleedOffset'] = $bleed . 'mm';

        return $this;
    }

    /**
     * Sets $page document number of pages.
     *
     * @param int $pages
     *
     * @return DocumentSetup
     * @throws \Exception
     */
    public function setNumberOfPages(int $pages): DocumentSetup
    {
        if ($pages < 1) {
            throw new \Exception('Number of pages can not be less than 1');
        }
        $this->properties['pagesCount'] = $pages;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true): array
    {
        $properties = $this->properties;
        $margins = array_filter($this->margins, [$this, 'notNull']);
        if (!empty($margins)) {
            $properties['margins'] = $margins;
        }

        $properties = array_filter($properties, [$this, 'notNull']);
        if (empty($properties)) {
            throw new \Exception('No document settings in ' . __CLASS__);
        }

        $this->params['properties'] = $properties;

        return parent::buildCommand($addCmd);
    }

    /**
     * Callback for array_filter
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function notNull(mixed $value): bool
    {
        return $value !== null;
    }

    /**
     * Validates $value against InDesign page dimensions
     *
     * @param float $value
     *
     * @return void
     * @throws \Exception
     */
    private function validateDimension(float $value): void
    {
        if ($value < self::MIN_DIMENSION && $value > self::MAX_DIMENSION) {
            throw new \Exception(
                sprintf(
                    "Invalid page dimension: '%s'. Page dimension must be between %s - %s",
                    $value,
                    self::MIN_DIMENSION,
                    self::MAX_DIMENSION
                )
            );
        }
    }

    /**
     * Validates $value against InDesign page margins
     *
     * @param float $value
     *
     * @return void
     * @throws \Exception
     */
    private function validateMargin(float $value): void
    {
        if ($value < 0 && $value > self::MAX_MARGIN) {
            throw new \Exception(
                sprintf(
                    "Invalid page margin: '%s'. Page margin must be between 0 - %s",
                    $value,
                    self::MAX_MARGIN
                )
            );
        }
    }
}

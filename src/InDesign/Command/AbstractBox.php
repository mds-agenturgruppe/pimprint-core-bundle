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

use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\LayerTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ElementNameTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\PositionTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\SizeTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\VariableTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Variables\DependentInterface;
use Mds\PimPrint\CoreBundle\Project\Traits\ProjectAwareTrait;

/**
 * Abstract command with generic functionality for box placement commands.
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
abstract class AbstractBox extends AbstractCommand implements DependentInterface
{
    use ElementNameTrait;
    use LayerTrait;
    use PositionTrait;
    use SizeTrait;
    use VariableTrait;
    use ProjectAwareTrait;

    /**
     * No resize.
     *
     * @var int
     */
    const RESIZE_NO_RESIZE = 0;

    /**
     * Resize width and height.
     *
     * @var int
     */
    const RESIZE_WIDTH_HEIGHT = 1;

    /**
     * Resize width.
     *
     * @var int
     */
    const RESIZE_WIDTH = 2;

    /**
     * Resize height.
     *
     * @var int
     */
    const RESIZE_HEIGHT = 3;

    /**
     * Uses Position (left, top) from master locale. Dimensions (width, height) and fit from command.
     *
     * @var string
     */
    const USE_MASTER_LOCALE_POSITION = 'position';

    /**
     * Uses Position (left, top) and width from master locale. Height and fit from command.
     *
     * @var string
     */
    const USE_MASTER_LOCALE_WIDTH = 'width';

    /**
     * Uses Position (left, top) and height from master locale. Width and fit from command.
     *
     * @var string
     */
    const USE_MASTER_LOCALE_HEIGHT = 'height';

    /**
     * Uses Position (left, top) and dimension (width, height) from master locale. No fit is made.
     *
     * @var string
     */
    const USE_MASTER_LOCALE_ALL = 'all';

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private array $availableParams = [
        'tid'                      => null,
        'cmdfilter'                => null,
        'localized'                => false,
        'locale'                   => null,
        'useMasterLocaleDimension' => self::USE_MASTER_LOCALE_ALL,
    ];

    /**
     * Available resize values.
     *
     * @var array
     */
    protected array $availableResizes = [
        self::RESIZE_NO_RESIZE,
        self::RESIZE_WIDTH_HEIGHT,
        self::RESIZE_WIDTH,
        self::RESIZE_HEIGHT,
    ];

    /**
     * Allowed master locale modes
     *
     * @var array
     */
    private static array $allowedMasterLocaleModes = [
        self::USE_MASTER_LOCALE_ALL,
        self::USE_MASTER_LOCALE_HEIGHT,
        self::USE_MASTER_LOCALE_POSITION,
        self::USE_MASTER_LOCALE_WIDTH,
    ];

    /**
     * Initializes abstract box.
     *
     * @return void
     */
    protected function initBoxParams(): void
    {
        $this->initParams($this->availableParams);
    }

    /**
     * Sets box ident.
     *
     * @param string|null $ident
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setBoxIdent(string $ident = null): AbstractBox
    {
        $this->setParam('tid', $ident);

        return $this;
    }

    /**
     * Returns box ident.
     *
     * @return string|null
     */
    public function getBoxIdent(): ?string
    {
        try {
            return $this->getParam('tid');
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Sets box $localized.
     *
     * @param bool $localized
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setLocalized(bool $localized = true): AbstractBox
    {
        $this->setParam('localized', $localized);

        return $this;
    }

    /**
     * Returns localized flag.
     *
     * @return bool|null
     */
    public function getLocalized(): ?bool
    {
        try {
            return $this->getParam('localized');
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Sets box $locale.
     *
     * @param string $locale
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setLocale(string $locale): AbstractBox
    {
        $this->setParam('locale', $locale);

        return $this;
    }

    /**
     * Returns box locale.
     *
     * @return bool|null
     */
    public function getLocale(): ?bool
    {
        try {
            return $this->getParam('locale');
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Sets $useMasterLocaleDimension flag.
     *
     * Controls the behaviour of the placement of localized elements with reference to the master locale.
     *
     * @param string $useMasterLocaleDimension
     *
     * @return AbstractBox
     * @throws \Exception
     * @see \Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox::USE_MASTER_LOCALE_HEIGHT
     * @see \Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox::USE_MASTER_LOCALE_ALL
     * @see \Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox::USE_MASTER_LOCALE_POSITION
     * @see \Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox::USE_MASTER_LOCALE_WIDTH
     */
    public function setUseMasterLocaleDimension(string $useMasterLocaleDimension): AbstractBox
    {
        $this->setParam('useMasterLocaleDimension', $useMasterLocaleDimension);

        return $this;
    }

    /**
     * Returns box useMasterLocaleDimension mode.
     *
     * @return string|null
     */
    public function getUseMasterLocaleDimension(): ?string
    {
        try {
            return $this->getParam('useMasterLocaleDimension');
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * Sets $localized as default localized flag for all AbstractBox types.
     *
     * @param bool $localized
     *
     * @return void
     */
    public static function setDefaultLocalized(bool $localized = true): void
    {
        CopyBox::setDefaultLocalized($localized);
        TextBox::setDefaultLocalized($localized);
        ImageBox::setDefaultLocalized($localized);
        Table::setDefaultLocalized($localized);
    }

    /**
     * Sets $mode as default useMasterLocaleDimension mode for all AbstractBox types.
     *
     * @param string $mode
     *
     * @return void
     * @throws \Exception
     */
    public static function setDefaultUseMasterLocaleMode(string $mode): void
    {
        CopyBox::setDefaultUseMasterLocaleMode($mode);
        TextBox::setDefaultUseMasterLocaleMode($mode);
        ImageBox::setDefaultUseMasterLocaleMode($mode);
        Table::setDefaultUseMasterLocaleMode($mode);
    }

    /**
     * Sets the ident of the box referenced to RenderingTrait::$boxIdentReference with $ident as postfix.
     * Used to create content referenced boxes in InDesign for content aware updates.
     *
     * @param string $ident
     *
     * @return AbstractBox
     * @throws \Exception
     * @see \Mds\PimPrint\CoreBundle\Project\Traits\RenderingTrait::$boxIdentReference
     */
    public function setBoxIdentReferenced(string $ident = ''): AbstractBox
    {
        try {
            $reference = $this->getProject()
                              ->getBoxIdentReference();
        } catch (\Exception) {
            $reference = '';
        }
        $this->setBoxIdent('ID-' . $reference . $ident);

        return $this;
    }

    /**
     * Sets cmdfilter for autocommands.
     *
     * @param string $filter
     *
     * @return AbstractBox
     * @throws \Exception
     */
    public function setCmdFilter(string $filter): AbstractBox
    {
        $this->setParam('cmdfilter', $filter);

        return $this;
    }

    /**
     * Validates command
     *
     * @return void
     * @throws \Exception
     */
    protected function validate(): void
    {
        $this->validateElementNameParam();
        $this->setAutoResize();
    }

    /**
     * Validates $mode for allowed useMasterLocaleDimension
     *
     * @param string $mode
     *
     * @return void
     * @throws \Exception
     */
    protected static function validateUseMasterLocaleDimension(string $mode): void
    {
        if (false === in_array($mode, self::$allowedMasterLocaleModes)) {
            throw new \Exception(
                sprintf("Invalid useMasterLocaleDimension value '%s' in '%s'.", $mode, static::class)
            );
        }
    }
}

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

namespace Mds\PimPrint\CoreBundle\InDesign\Html;

use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractBox;
use Mds\PimPrint\CoreBundle\InDesign\Command\AbstractCommand;
use Mds\PimPrint\CoreBundle\InDesign\Command\Table;
use Mds\PimPrint\CoreBundle\InDesign\Command\TextBox;
use Mds\PimPrint\CoreBundle\InDesign\Text;

/**
 * Class FragmentParser
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Html
 */
class FragmentParser extends AbstractParser
{
    /**
     * $element parameter name in factory closure to create Text instances.
     *
     * @var string
     */
    const FACTORY_ELEMENT_TEXT = 'text';

    /**
     * $element parameter name in factory closure to create TextBox instances.
     *
     * @var string
     */
    const FACTORY_ELEMENT_TEXT_BOX = 'text-box';

    /**
     * $element parameter name in factory closure to create Table instances.
     *
     * @var string
     */
    const FACTORY_ELEMENT_TABLE = 'table';

    /**
     * List of HTML elements treated as table elements.
     *
     * @var array
     */
    protected $elementsTable = [
        'thead',
        'tfoot',
        'tbody',
        'colgroup',
        'col',
        'table',
        'tr',
        'th',
        'td',
    ];

    /**
     * Parsed boxes.
     *
     * @var AbstractBox[]
     */
    protected $boxes = [];

    /**
     * Current parsed box.
     *
     * @var AbstractBox
     */
    protected $currentBox = null;

    /**
     * FragmentParser constructor.
     *
     * @param \Closure|null $factoryClosure
     * @param Style|null    $style
     */
    public function __construct(\Closure $factoryClosure = null, Style $style = null)
    {
        parent::__construct($factoryClosure, null, $style);
    }

    /**
     * Parses $html and returns AbstractCommands for all elements to be places in InDesign document.
     *
     * @param string     $html  HTML string to parse.
     * @param Style|null $style Optional Style to apply to the parsed HTML.
     *
     * @return AbstractCommand[]
     * @throws \Exception
     */
    public function parse($html, Style $style = null)
    {
        $this->boxes = [];
        parent::parse($html, $style);
        $this->handleTextFragment();

        return $this->boxes;
    }

    /**
     * Adds stored $text as TEXT-BOX
     */
    public function handleTextFragment()
    {
        if (null == $this->text) {
            return;
        }
        $textBox = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_TEXT_BOX, null);
        if ($textBox instanceof TextBox) {
            try {
                $textBox->addText($this->text);
                $this->addBox($textBox, false);
                $this->text = null;
            } catch (\Exception $e) {
                $this->text = null;
            }
        }
    }

    /**
     * Creates returns target Text instance to add parsed ParagraphComponents.
     * For each text fragment a new Instance is created by factoryClosure.
     *
     * @return Text
     * @throws \Exception
     */
    public function getText(): Text
    {
        if (null === $this->text) {
            $this->text = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_TEXT, null);
            if (false === $this->text instanceof Text) {
                throw new \Exception('No Text instance created by factoryClosure.');
            }
        }

        return $this->text;
    }

    /**
     * {@inheritDoc}
     *
     * @param \DomElement $node
     *
     * @throws \Exception
     */
    protected function parseElementNode(\DomElement $node)
    {
        $tag = strtolower($node->tagName);
        switch ($tag) {
            case in_array($tag, $this->elementsTable):
                $this->parseTableNodes($node);
                break;
            default:
                parent::parseElementNode($node);
                break;
        }
    }

    /**
     * Parses table element $node.
     *
     * @param \DomElement $node
     *
     * @throws \Exception
     */
    protected function parseTableNodes(\DomElement $node)
    {
        $tag = strtolower($node->tagName);
        switch ($tag) {
            case in_array($tag, ['thead', 'tfoot', 'tbody', 'colgroup']):
                $this->parseNode($node);
                break;

            case 'col':
                $this->processNodeCol($node);
                break;

            case 'table':
                $this->processNodeTable($node);
                break;

            case 'tr':
                $this->processNodeTr($node);
                break;

            case 'th':
                $this->processNodeTh($node);
                break;

            case 'td':
                $this->processNodeTd($node);
                break;

            default:
                parent::parseElementNode($node);
                break;
        }
    }

    /**
     * Template Method for processing col nodes.
     *
     * @param \DomElement $node
     *
     * @throws \Exception
     */
    protected function processNodeCol(\DomElement $node)
    {
        $width = $node->getAttribute('width');
        if (empty($width)) {
            $width = 0;
        }
        $table = $this->getCurrentBox(Table::class);
        $table->addColumn($width);
        $this->parseNode($node);
    }

    /**
     * Template Method for processing table nodes.
     *
     * @param \DomElement $node
     *
     * @throws \Exception
     */
    protected function processNodeTable(\DomElement $node)
    {
        $table = $this->tableFactory($node);
        try {
            $table->getTableStyle();
        } catch (\Exception $e) {
            $table->setTableStyle($this->getNodeStyle($node, Style::TYPE_TABLE));
        }
        $this->newBox($table);
        $this->parseNode($node);
        $this->addBox($table);
    }

    /**
     * Template method for processing tr nodes.
     *
     * @param \DomElement $node
     *
     * @throws \Exception
     */
    protected function processNodeTr(\DomElement $node)
    {
        $rowType = Table::ROW_TYPE_BODY;
        if ('thead' == strtolower($node->parentNode->tagName)) {
            $rowType = Table::ROW_TYPE_HEADER;
        } elseif ('tfoot' == strtolower($node->parentNode->tagName)) {
            $rowType = Table::ROW_TYPE_FOOTER;
        }
        $table = $this->getCurrentBox(Table::class);
        $table->startRow(0, $rowType);
        $this->parseNode($node);
    }

    /**
     * Template method for processing th nodes.
     *
     * @param \DomElement $node
     *
     * @throws \Exception
     */
    protected function processNodeTh(\DomElement $node)
    {
        $this->processNodeTd($node);
    }

    /**
     * Template method for processing td nodes.
     *
     * @param \DomElement $node
     *
     * @throws \Exception
     */
    protected function processNodeTd(\DomElement $node)
    {
        $table = $this->getCurrentBox(Table::class);
        $colspan = $node->getAttribute('colspan');
        $style = $this->getNodeStyle($node, Style::TYPE_CELL);
        $table->addCell($node->textContent, null, $colspan ? ($colspan) : 1, $style);
    }

    /**
     * Returns currentBox. If $className is provided instance of currentBox is validated against $classname.
     *
     * @param string|null $className
     *
     * @return AbstractCommand
     * @throws \Exception
     */
    protected function getCurrentBox($className = null)
    {
        try {
            if (empty($this->currentBox)) {
                throw new \Exception();
            }
            if (null === $className) {
                return $this->currentBox;
            }
            if (false === $this->currentBox instanceof $className) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw new \Exception(
                sprintf(
                    "XHTML Stack error. Expected class is '%s', current class '%s'.",
                    $className,
                    get_class($this->currentBox)
                )
            );
        }

        return $this->currentBox;
    }

    /**
     * Adds $component to current box stack.
     *
     * @param AbstractBox $box
     * @param bool        $reset
     */
    protected function addBox(AbstractBox $box, bool $reset = true)
    {
        $this->boxes[] = $box;
        if (true === $reset) {
            $this->currentBox = null;
        }
    }

    /**
     * Adds $box as new currentBox.
     *
     * @param AbstractBox $box
     */
    protected function newBox(AbstractBox $box)
    {
        $this->handleTextFragment();
        if ($this->currentBox instanceof AbstractBox) {
            $this->addBox($this->currentBox);
        }
        $this->currentBox = $box;
    }

    /**
     * Factory template method for Table command.
     * Project specific instances can be integrated by overwriting method or using factoryClosure.
     *
     * @param \DOMElement $node
     *
     * @return Table
     * @throws \Exception
     */
    protected function tableFactory(\DOMElement $node)
    {
        $table = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_TABLE, $node);
        if (false === $table instanceof Table) {
            throw new \Exception(
                sprintf(
                    "No table of class '%s' created by factoryClosure.",
                    Table::class
                )
            );
        }
        $table->setParseMode(true);

        return $table;
    }
}

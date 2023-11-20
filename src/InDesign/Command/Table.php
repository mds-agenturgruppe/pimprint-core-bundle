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

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\DefaultLocalizedTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\FitTrait;
use Mds\PimPrint\CoreBundle\InDesign\Command\Traits\ImageCollectorTrait;
use Mds\PimPrint\CoreBundle\InDesign\Text;
use Mds\PimPrint\CoreBundle\InDesign\Text\Paragraph;

/**
 * Class Table
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Command
 */
class Table extends AbstractBox implements ImageCollectorInterface, ComponentInterface
{
    use FitTrait;
    use ImageCollectorTrait;
    use DefaultLocalizedTrait;

    /**
     * Command name.
     *
     * @var string
     */
    const CMD = 'table';

    /**
     * Row type for InDesign body rows.
     *
     * @var string
     */
    const ROW_TYPE_BODY = 'BODY_ROW';

    /**
     * Row type for InDesign header rows.
     *
     * @var string
     */
    const ROW_TYPE_HEADER = 'HEADER_ROW';

    /**
     * Row type for InDesign footer rows.
     *
     * @var string
     */
    const ROW_TYPE_FOOTER = 'FOOTER_ROW';

    /**
     * Don't adjust box size.
     *
     * @var int
     */
    const FIT_NO_ADJUST = 0;

    /**
     * Resizes the frame so it fits the content.
     *
     * @see https://www.indesignjs.de/extendscriptAPI/indesign-latest/#FitOptions.html
     * @var int
     */
    const FIT_FRAME_TO_CONTENT = 1;

    /**
     * Resizes the frame so it fits the content but keeps height.
     *
     * @var int
     */
    const FIT_FRAME_TO_CONTENT_HEIGHT = 2;

    /**
     * Array with all allowed fits for validation.
     *
     * @var array
     */
    protected array $allowedFits = array(
        self::FIT_NO_ADJUST,
        self::FIT_FRAME_TO_CONTENT,
        self::FIT_FRAME_TO_CONTENT_HEIGHT
    );

    /**
     * Column layout definitions.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * Content rows of table.
     *
     * @var array
     */
    protected array $rows = [];

    /**
     * Footer-row
     *
     * @var array
     */
    protected array $footer = [];

    /**
     * Temporary array for current row then adding cells sequentially.
     *
     * @var array
     */
    protected array $currentRow = [];

    /**
     * Instance of Text used when to transform cell content
     *
     * @var Text|null
     */
    protected ?Text $text = null;

    /**
     * Available command params with default values.
     *
     * @var array
     */
    private array $availableParams = [
        'fit'        => self::FIT_NO_ADJUST,
        'tableStyle' => null,
        'lineHeight' => null,
        'rowHeight'  => null,
    ];

    /**
     * Indicates HTML parsing mode. When parsingMode is on.
     * Columns are automatically added when adding cells for not existent columns.
     *
     * @var bool
     */
    protected bool $parseMode = false;

    /**
     * Table constructor.
     *
     * @param string         $elementName Name of template element.
     * @param float|int|null $left        Left position in mm.
     * @param float|int|null $top         Top position in mm.
     * @param float|int|null $width       Width of element in mm.
     * @param float|int|null $height      Height of element in mm.
     * @param string|null    $tableStyle  InDesign table style.
     * @param float|int|null $lineHeight  Default line height in mm.
     * @param int            $fit         Fit mode of table box.
     *
     * @throws \Exception
     */
    public function __construct(
        string $elementName = '',
        float|int $left = null,
        float|int $top = null,
        float|int $width = null,
        float|int $height = null,
        string $tableStyle = null,
        float|int $lineHeight = null,
        int $fit = self::FIT_NO_ADJUST
    ) {
        $this->initBoxParams();
        $this->initParams($this->availableParams);

        $this->initElementName();
        $this->initLayer();
        $this->initPosition();
        $this->initSize();

        $this->setElementName($elementName);
        $this->setLeft($left);
        $this->setTop($top);
        $this->setWidth($width);
        $this->setHeight($height);
        if (null !== $tableStyle) {
            $this->setTableStyle($tableStyle);
        }
        if (null !== $lineHeight) {
            $this->setLineHeight($lineHeight);
        }
        $this->setFit($fit);
        $this->setResize(self::RESIZE_WIDTH_HEIGHT);
    }

    /**
     * Sets table style.
     *
     * @param string $tableStyle
     *
     * @return Table
     * @throws \Exception
     */
    public function setTableStyle(string $tableStyle): Table
    {
        $this->setParam('tableStyle', $tableStyle);

        return $this;
    }

    /**
     * Returns table style. If no style is set an exception is thrown.
     *
     * @return string
     * @throws \Exception
     */
    public function getTableStyle(): string
    {
        $style = $this->getParam('tableStyle');
        if (empty($style)) {
            throw new \Exception('No tableStyle set.');
        }

        return $style;
    }

    /**
     * Sets lineHeight in table cells.
     *
     * @param float|int|null $lineHeight
     *
     * @return Table
     * @throws \Exception
     */
    public function setLineHeight(float|int|null $lineHeight): Table
    {
        $this->setParam('lineHeight', $lineHeight);

        return $this;
    }

    /**
     * Sets the default row height in table.
     * This height is used, when a row is added without a explicit height.
     *
     * @param float|int $height Default row height in mm.
     *
     * @return Table
     * @throws \Exception
     */
    public function setRowHeight(float|int $height): Table
    {
        $this->setParam('rowHeight', $height);

        return $this;
    }

    /**
     * Sets parsing mode.
     *
     * @param bool $parseMode
     *
     * @return Table
     */
    public function setParseMode(bool $parseMode): Table
    {
        $this->parseMode = $parseMode;

        return $this;
    }

    /**
     * Returns Text instance.
     * If none is set a new instance is created via textFaactory
     *
     * @return Text
     */
    protected function getText(): Text
    {
        if (null === $this->text) {
            $this->text = $this->textFactory();
        }
        $this->text->clear();

        return $this->text;
    }

    /**
     * Sets Text instance.
     *
     * @param Text $text
     *
     * @return Table
     */
    public function setText(Text $text): Table
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Factory template method.
     * Factory can be overwritten to have project specific Text instances created automatically.
     *
     * @return Text
     */
    protected function textFactory(): Text
    {
        return new Text();
    }

    /**
     * Clears all content (columns, rows and footer) from table.
     *
     * @return Table
     */
    public function clear(): Table
    {
        $this->clearColumns();
        $this->clearFooter();
        $this->collectedImages = [];

        return $this;
    }

    /**
     * Removes all columns and rows from table.
     *
     * @return Table
     */
    public function clearColumns(): Table
    {
        $this->columns = [];
        $this->clearRows();

        return $this;
    }

    /**
     * Clears all rows in table.
     *
     * @return Table
     */
    public function clearRows(): Table
    {
        $this->rows = [];

        return $this;
    }

    /**
     * Clears footer in table.
     *
     * @return Table
     */
    public function clearFooter(): Table
    {
        $this->footer = [];

        return $this;
    }

    /**
     * Adds a column with $width, $style and ident to the table.
     *
     *
     * @param float|int       $width Width of the column.
     * @param int|string|null $ident Optional ident to be able to identify a cell when adding content.
     * @param string          $style Optional InDesign Style of the column
     *
     * @return Table
     * @throws \Exception
     */
    public function addColumn(float|int $width, int|string $ident = null, string $style = ''): Table
    {
        if (null === $ident) {
            $ident = $this->setUpAutoIdent($this->columns, $ident);
        } else {
            if (false === $this->parseMode) {
                if (is_numeric($ident)) {
                    throw new \Exception('No numeric idents are allowed for named columns.');
                }
            }
        }
        $this->columns[$ident] = [
            'width' => $width,
            'style' => $style,
        ];

        return $this;
    }

    /**
     * Generates column ident for automatic ident mode for $elements.
     *
     * @param array       $elements
     * @param string|null $ident
     *
     * @return int|string
     */
    protected function setUpAutoIdent(array $elements, string $ident = null): int|string
    {
        if (null !== $ident) {
            return $ident;
        }
        $idents = [];
        foreach (array_keys($elements) as $ident) {
            if (false === is_numeric($ident)) {
                continue;
            }
            $idents[$ident] = true;
        }

        return count($idents) + 1;
    }

    /**
     * Returns true if column $ident is defined in table. Otherwise, false is returned.
     *
     * @param string $ident
     *
     * @return bool
     */
    public function hasColumn(string $ident): bool
    {
        return isset($this->columns[$ident]);
    }

    /**
     * Starts a row for sequential cell adding.
     *
     * @param float|null $height         Optional explicit row height
     * @param string     $type           InDesign table row type. Use ROW_TYPE class constants.
     * @param bool       $useExactHeight Use min or exact height
     *
     * @return Table
     * @throws \Exception
     */
    public function startRow(
        float $height = null,
        string $type = self::ROW_TYPE_BODY,
        bool $useExactHeight = false
    ): Table {
        $this->assertRowType($type);
        $this->endRow();

        if (null === $height) {
            $height = $this->getParam('rowHeight');
        }
        $this->currentRow = [
            'height'      => $height,
            'exactHeight' => $useExactHeight ? 1 : 0,
            'type'        => $type,
            'columns'     => [],
        ];

        return $this;
    }

    /**
     * Ends the current row.
     *
     * @return Table
     */
    protected function endRow(): Table
    {
        if (empty($this->currentRow)) {
            return $this;
        }

        $this->rows[] = $this->currentRow;
        $this->currentRow = [];

        return $this;
    }

    /**
     * Closes current row without adding it to the table.
     *
     * @return Table
     */
    public function abortRow(): Table
    {
        $this->currentRow = [];

        return $this;
    }

    /**
     * Adds a cell to the current row when adding cells in sequential mode.
     *
     * @param float|int|string|Paragraph|Text $content     Cell content: String or InDesign Text or Paragraph instance.
     * @param int|string|null                 $ident       Optional ident of the column to set.
     * @param int                             $colspan     Optional colspan
     * @param string|null                     $style       Optional cell style.
     * @param bool                            $appendStyle If true, $style is appended to the default column style,
     *
     * @return Table
     * @throws \Exception
     * @throws FilesystemException
     */
    public function addCell(
        float|Text|Paragraph|int|string $content,
        int|string $ident = null,
        int $colspan = 1,
        string $style = null,
        bool $appendStyle = false
    ): Table {
        if (empty($this->currentRow)) {
            throw new \Exception('No row started. Use startRow() before adding cells.');
        }
        $ident = $this->setUpAutoIdent($this->currentRow['columns'], $ident);
        if (false === isset($this->columns[$ident])) {
            if (false === $this->parseMode) {
                if (is_numeric($ident)) {
                    throw new \Exception(sprintf('Column number #%s not defined in table.', $ident));
                } else {
                    throw new \Exception(sprintf("Column with ident '%s' not defined in table.", $ident));
                }
            }
            $this->addColumn(0, $ident);
        }
        if (false === isset($this->currentRow['columns'][$ident])) {
            if (count($this->currentRow['columns']) == count($this->columns)) {
                throw new \Exception('Row can not contain more columns than defined in table.');
            }
        }
        $cellContent = $this->createCellContent($content);
        $cell = [
            'colspan' => $colspan,
            'values'  => $cellContent->buildCommand(),
        ];
        $this->addCollectedImages($cellContent);
        if (null === $style) {
            $style = $this->columns[$ident]['style'];
        } elseif ($appendStyle) {
            $style = $this->columns[$ident]['style'] . $style;
        }
        if (false === empty($style)) {
            $cell['style'] = $style;
        }
        $this->currentRow['columns'][$ident] = $cell;

        return $this;
    }

    /**
     * Converts $content to Text if not a Text or Paragraph.
     *
     * @param float|int|string|Paragraph|Text $content
     *
     * @return Text|Paragraph
     * @throws \Exception|FilesystemException
     */
    protected function createCellContent(float|Text|Paragraph|int|string $content): Text|Paragraph
    {
        if ($content instanceof Text) {
            return $content;
        }
        if ($content instanceof Paragraph) {
            return $this->getText()
                        ->addParagraph($content);
        }
        if (is_numeric($content)) {
            $content = (string)$content;
        }
        if (is_string($content)) {
            return $this->getText()
                        ->addString($content);
        }
        throw new \Exception('Cell content must be string|int|float|Text|Paragraph.');
    }

    /**
     * Returns true and only true if $type is a correct InDesign table row type.
     *
     * @param string $type
     *
     * @return bool
     * @throws \Exception
     */
    private function assertRowType(string $type): bool
    {
        switch ($type) {
            case self::ROW_TYPE_BODY:
            case self::ROW_TYPE_FOOTER:
            case self::ROW_TYPE_HEADER:
                return true;

            default:
                throw new \Exception(
                    sprintf(
                        "Invalid table row type '%s'. Use '%s' ROW_TYPE_ constants.",
                        $type,
                        self::class
                    )
                );
        }
    }

    /**
     * Returns ident of command when used as compound.
     *
     * @return string
     */
    public function getComponentIdent(): string
    {
        return 'table';
    }

    /**
     * Returns true if component can be used multiple times in the same command.
     *
     * @return bool
     */
    public function isMultipleComponent(): bool
    {
        return false;
    }

    /**
     * Builds command array that is sent as JSON to InDesign.
     *
     * @param bool $addCmd
     *
     * @return array
     * @throws \Exception
     */
    public function buildCommand(bool $addCmd = true): array
    {
        $this->endRow();
        if (0 === count($this->rows)) {
            throw new \Exception('Table must contain at least one row.');
        }

        $columnOrder = [];
        $columns = [];
        $rows = [];
        foreach ($this->columns as $ident => $column) {
            $columnOrder[] = $ident;
            $columns[] = $column;
        }

        foreach ($this->rows as $row) {
            $data = [
                'height'      => $row['height'],
                'exactHeight' => $row['exactHeight'],
                'type'        => $row['type'],
                'cols'        => []
            ];
            foreach ($columnOrder as $ident) {
                if (isset($row['columns'][$ident])) {
                    $data['cols'][] = $row['columns'][$ident];
                } else {
                    $data['cols'][] = [];
                }
            }
            $rows[] = $data;
        }

        $values = [
            'fit'         => $this->getParam('fit'),
            'tableStyle'  => $this->getParam('tableStyle'),
            'columnCount' => count($this->columns),
            'lineHeight'  => $this->getParam('lineHeight'),
            'columnsInfo' => $columns,
            'rows'        => $rows,
        ];
        if (empty($values['tableStyle'])) {
            unset($values['tableStyle']);
        }

        $this->params['values'] = $values;

        $this->removeParam('fit');
        $this->removeParam('tableStyle');
        $this->removeParam('lineHeight');
        $this->removeParam('rowHeight');

        return parent::buildCommand(true);
    }
}

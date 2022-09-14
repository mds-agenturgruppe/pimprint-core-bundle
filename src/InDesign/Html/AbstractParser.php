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

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\Command\ImageBox;
use Mds\PimPrint\CoreBundle\InDesign\Html\Traits\ParserFactoryTrait;
use Mds\PimPrint\CoreBundle\InDesign\Text;
use Mds\PimPrint\CoreBundle\InDesign\Text\Characters;
use Mds\PimPrint\CoreBundle\InDesign\Text\Paragraph;
use Mds\PimPrint\CoreBundle\InDesign\Text\ParagraphComponent;
use Mds\PimPrint\CoreBundle\InDesign\Traits\MissingAssetNotifierTrait;
use Pimcore\Model\Asset;

/**
 * Class AbstractParser
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Html
 */
abstract class AbstractParser
{
    use ParserFactoryTrait;
    use MissingAssetNotifierTrait;

    /**
     * $element parameter name in factory closure to create Style instances.
     *
     * @var string
     */
    const FACTORY_ELEMENT_STYLE = 'style';

    /**
     * $element parameter name in factory closure to create Character instances.
     *
     * @var string
     */
    const FACTORY_ELEMENT_CHARACTERS = 'characters';

    /**
     * $element parameter name in factory closure to create Paragraph instances.
     *
     * @var string
     */
    const FACTORY_ELEMENT_PARAGRAPH = 'paragraph';

    /**
     * $element parameter name in factory closure to create ImageBox instances.
     *
     * @var string
     */
    const FACTORY_ELEMENT_IMAGE = 'image';

    /**
     * $element parameter name in factory closure to load assets from img tag nodes.
     *
     * @var string
     */
    const FACTORY_ELEMENT_IMG_ASSET = 'img-asset';

    /**
     * List of HTML elements treated as block elements.
     *
     * @var array
     */
    protected array $elementsBlock = [
        'address',
        'article',
        'aside',
        'blockquote',
        'div',
        'figcaption',
        'figure',
        'footer',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'header',
        'hgroup',
        'p',
        'pre',
        'section',
        'ul',
        'ol',
        'li',
        'dl',
        'dd',
        'dt',
    ];

    /**
     * List of HTML elements treated as inline elements.
     *
     * @var array
     */
    protected array $elementsInline = [
        'a',
        'abbr',
        'b',
        'cite',
        'code',
        'dfn',
        'em',
        'i',
        'kbd',
        'q',
        'samp',
        'small',
        'span',
        'sub',
        'sup',
        'strong',
        'var',
    ];

    /**
     * List of HTML elements treated as line breaks.
     * @var array
     */
    protected array $elementsLineBreak = ['br', 'hr'];

    /**
     * List of HTML elements where styling pseudo class :first :last is supported.
     *
     * @var array
     */
    protected array $elementsFirstLastPseudo = [
        'ol' => 'li',
        'ul' => 'li'
    ];

    /**
     * Helper member for first last pseudo.
     *
     * @var array
     */
    protected array $currentPseudo = [
        'tag'     => null,
        'counter' => null,
        'amount'  => null,
    ];

    /**
     * Text instance parsed ParagraphComponents are added to.
     *
     * @var Text|null
     */
    protected ?Text $text = null;

    /**
     * Used HTML style instance.
     *
     * @var Style|null
     */
    protected ?Style $style = null;

    /**
     * Parsed paragraph components
     *
     * @var ParagraphComponent[]
     */
    protected array $paragraphComponents = [];

    /**
     * Indicates of inline class attributes should be used to as InDesign paragraph and character styles.
     *
     * @var bool
     */
    protected bool $useInlineStyle = true;

    /**
     * Returns target Text instance.
     *
     * @return Text
     * @throws \Exception
     */
    abstract public function getText(): Text;

    /**
     * AbstractParser constructor.
     *
     * @param \Closure|null $factoryClosure
     * @param Text|null     $text
     * @param Style|null    $style
     */
    public function __construct(\Closure $factoryClosure = null, Text $text = null, Style $style = null)
    {
        $this->setFactoryClosure($factoryClosure);
        if (null !== $text) {
            $this->setText($text);
        }
        if (null !== $style) {
            $this->setStyle($style);
        }
    }

    /**
     * Returns HTML style instance.
     * If none is set new style instance with no styling is created by the factory.
     *
     * @return Style
     */
    public function getStyle(): Style
    {
        if (null === $this->style) {
            $this->style = $this->styleFactory();
        }

        return $this->style;
    }

    /**
     * Sets HTML style instance.
     *
     * @param Style $style
     *
     * @return AbstractParser
     */
    public function setStyle(Style $style): AbstractParser
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Sets useInlineStyle.
     * If $useInlineStyle is true class attributes of tags are uses as InDesign paragraph and character styles.
     * If no class attribute is found or $useInlineStyle is false, the definition in Style class is used.
     *
     * @param bool $useInlineStyle
     *
     * @return AbstractParser
     */
    public function setUseInlineStyle(bool $useInlineStyle): AbstractParser
    {
        $this->useInlineStyle = $useInlineStyle;

        return $this;
    }

    /**
     * Parses $html and adds content as Paragraphs to Text instance.
     *
     * @param string     $html  HTML strings to parse.
     * @param Style|null $style Optional Style to apply to the parsed HTML.
     *
     * @return AbstractParser|array
     * @throws FilesystemException
     * @throws \Exception
     */
    public function parse(string $html, Style $style = null): AbstractParser|array
    {
        $html = $this->sanitiseHtml($html);
        if (null !== $style) {
            $this->setStyle($style);
        }
        $this->paragraphComponents = [];
        try {
            $xml = new \DOMDocument('1.0', 'UTF-8');
            $xml->loadHTML('<?xml version="1.0" encoding="UTF-8"?><html><body>' . $html . '</body></html>');
            if ($xml->documentElement) {
                foreach ($xml->documentElement->getElementsByTagName('body') as $body) {
                    $this->parseNode($body);
                    break;
                }
                $this->endBlock();
            } else {
                throw new \Exception();
            }
        } catch (\DOMException $exception) {
            $paragraph = $this->paragraphFactory();
            $paragraph->setText('Text not XHTML compliant:[%BR%] ' . $html);
            $this->getText()
                 ->addParagraph($paragraph);
        }

        return $this;
    }

    /**
     * Removes unwanted chars in $string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function sanitiseHtml(string $string): string
    {
        $string = str_replace(["\r", "\n", "\t"], '', $string);
        $string = preg_replace('#\s{2,}#', '', $string);

        return $string;
    }

    /**
     * Iterates over all children in $node.
     *
     * @param \DomNode $node
     *
     * @return void
     * @throws \Exception|FilesystemException
     */
    protected function parseNode(\DomNode $node): void
    {
        foreach ($node->childNodes as $child) {
            /* @var $child \DomElement */
            switch ($child->nodeType) {
                case XML_ELEMENT_NODE:
                    $this->parseElementNode($child);
                    break;
                case XML_TEXT_NODE:
                    $this->parseTextNode($child);
                    break;
            }
        }
    }

    /**
     * Iterates over all children in $node.
     *
     * @param \DomNode $node
     *
     * @return void
     * @throws \Exception
     */
    protected function parseInlineNode(\DomNode $node): void
    {
        foreach ($node->childNodes as $child) {
            /* @var $child \DomElement */
            switch ($child->nodeType) {
                case XML_ELEMENT_NODE:
                    $tag = strtolower($child->tagName);
                    switch ($tag) {
                        case in_array($tag, $this->elementsLineBreak):
                            $characters = $this->charactersFactory();
                            $characters->setText(PHP_EOL);
                            $this->addComponent($characters);
                            break;
                        case in_array($tag, $this->elementsInline):
                            $this->parseInlineNode($child);
                            break;
                    }
                    break;
                case XML_TEXT_NODE:
                    $characters = $this->charactersFactory();
                    $characters->setText($child->textContent)
                               ->setStyle($this->getNodeStyle($node, Style::TYPE_CHARACTER));
                    $this->addComponent($characters);
                    break;
            }
        }
    }

    /**
     * Parses $node.
     *
     * @param \DomElement $node
     *
     * @return void
     * @throws \Exception|FilesystemException
     */
    protected function parseElementNode(\DomElement $node): void
    {
        $tag = strtolower($node->tagName);
        switch ($tag) {
            case in_array($tag, $this->elementsLineBreak):
                $characters = $this->charactersFactory();
                $characters->setText(PHP_EOL);
                $this->addComponent($characters);
                break;

            case 'img':
                try {
                    $command = $this->createImageCommand($node);
                    $this->addComponent($command);
                } catch (\Exception $exception) {
                    $this->notifyMissingAsset($exception->getMessage(), $exception->getCode());
                }
                break;

            case in_array($tag, $this->elementsInline):
                $this->parseInlineNode($node);
                break;

            case in_array($tag, $this->elementsBlock):
                $this->endBlock();
                if (isset($this->elementsFirstLastPseudo[$tag])) {
                    $this->currentPseudo['tag'] = $this->elementsFirstLastPseudo[$tag];
                    $this->currentPseudo['counter'] = 0;
                    $this->currentPseudo['amount'] = count($node->childNodes);
                }
                $pseudoStyle = '';
                if ($tag == $this->currentPseudo['tag']) {
                    $this->currentPseudo['counter']++;
                    if (1 == $this->currentPseudo['counter']) {
                        $pseudoStyle = ':first';
                    } elseif ($this->currentPseudo['counter'] == $this->currentPseudo['amount']) {
                        $this->currentPseudo['tag'] = $this->currentPseudo['counter'] = null;
                        $pseudoStyle = ':last';
                    }
                }
                $this->parseNode($node);
                try {
                    $this->getText()
                         ->addParagraph(
                             $this->createParagraph(
                                 $this->paragraphComponents,
                                 $this->getNodeStyle($node, Style::TYPE_PARAGRAPH, $pseudoStyle)
                             )
                         );
                } catch (\Exception $exception) {
                    $this->paragraphComponents = [];
                }

                $this->paragraphComponents = [];
                break;
        }
    }

    /**
     * Adds Characters component for text $node.
     *
     * @param \DomNode $node
     *
     * @return void
     */
    protected function parseTextNode(\DomNode $node): void
    {
        $characters = $this->charactersFactory();
        $characters->setText($node->textContent);

        $this->addComponent($characters);
    }

    /**
     * Returns class attribute of $node or style definition for $type in HTML\Style instance.
     *
     * @param \DomNode $node
     * @param string   $type
     * @param string   $pseudo
     *
     * @return string
     * @throws \Exception
     */
    protected function getNodeStyle(\DomNode $node, string $type, string $pseudo = ''): string
    {
        $class = $node->getAttribute('class');
        if ($this->useInlineStyle && false === empty($class)) {
            return $class;
        }

        $style = $this->getStyle()
                      ->getStyle($node->tagName . $pseudo, $type);
        if (false === empty($style)) {
            return $style;
        }

        return $this->getStyle()
                    ->getStyle($node->tagName, $type);
    }

    /**
     * Parses tag style attribute string $style and returns lower cased key-value array.
     *
     * @param string $style
     *
     * @return array
     */
    protected function parseStyleAttribute(string $style): array
    {
        $styles = [];
        foreach (explode(';', $style) as $value) {
            $value = explode('=', $value);
            if (count($value) == 2) {
                $styles[trim(strtolower($value[0]))] = trim(strtolower($value[1]));
            }
        }

        return $styles;
    }

    /**
     * Adds $component to current components stack.
     *
     * @param ParagraphComponent $component
     *
     * @return void
     */
    protected function addComponent(ParagraphComponent $component): void
    {
        $this->paragraphComponents[] = $component;
    }

    /**
     * Adds parsed currentChars as paragraph to paragraphComponents.
     *
     * @return void
     */
    protected function endBlock(): void
    {
        try {
            $this->getText()
                 ->addParagraph(
                     $this->createParagraph($this->paragraphComponents)
                 );
            $this->paragraphComponents = [];
        } catch (\Exception $exception) {
            $this->paragraphComponents = [];
        }
    }

    /**
     * Creates a new paragraph with all $characters and $style.
     * If no content is inside $components an exception is thrown.
     *
     * @param ParagraphComponent[] $components
     * @param string|null          $style
     *
     * @return Paragraph
     * @throws \Exception
     */
    protected function createParagraph(array $components, string $style = null): Paragraph
    {
        if (empty($components)) {
            throw new \Exception('No components.');
        }
        $paragraph = $this->paragraphFactory();
        if (null !== $style) {
            $paragraph->setParagraphStyle($style);
        }

        $content = '';
        foreach ($components as $element) {
            if ($element instanceof Characters) {
                $content .= $element->getText();
            } elseif ($element instanceof ImageBox) {
                $content .= 'image';
            }
            $paragraph->addComponent($element);
        }
        if (0 == strlen($content)) {
            throw new \Exception('No content found in data.');
        }

        return $paragraph;
    }

    /**
     * Creates a ImageBox command from $node.
     *
     * @param \DomElement $node
     *
     * @return ImageBox
     * @throws \Exception|FilesystemException
     */
    protected function createImageCommand(\DomElement $node): ImageBox
    {
        $asset = $this->loadAssetForImgTag($node);
        if (false === $asset instanceof Asset) {
            throw new \Exception(
                sprintf(
                    "No asset found for img tag '%s'.",
                    $node->ownerDocument->saveHTML($node)
                )
            );
        }
        $image = $this->imageFactory($node, $asset);
        $image->setAsset($asset);

        $paramsMap = [
            'class'  => 'setElementName',
            'width'  => 'setWidth',
            'height' => 'setHeight',
            'data-top'    => 'setTop',
            'data-left'   => 'setLeft',
            'data-fit'    => 'setFit',
        ];
        foreach ($paramsMap as $attribute => $method) {
            $param = $node->getAttribute($attribute);
            if (empty($param)) {
                continue;
            }
            $image->$method($param);
        }

        return $image;
    }

    /**
     * Loads Asset for HTML img tag.
     * Project specific implementations can be integrated by overwriting method or using factoryClosure
     *
     * @param \DomElement $node
     *
     * @return Asset|null
     * @throws \Exception
     */
    protected function loadAssetForImgTag(\DomElement $node): ?Asset
    {
        $asset = $this->factoryClosure->call($this, self::FACTORY_ELEMENT_IMG_ASSET, $node);
        if ($asset instanceof Asset) {
            return $asset;
        }
        $type = $node->getAttribute('pimcore_type');
        $assetId = $node->getAttribute('pimcore_id');
        if ('asset' == $type && false === empty($assetId)) {
            $asset = Asset::getById($assetId);
            if (false === $asset instanceof Asset) {
                throw new \Exception(
                    sprintf(
                        'No asset found for asset id: %s.',
                        $assetId
                    ),
                    $assetId
                );
            }

            return $asset;
        }
        $filePath = $node->getAttribute('src');
        if (empty($filePath)) {
            throw new \Exception(
                sprintf(
                    'No src found in tag: %s.',
                    $node->ownerDocument->saveHTML($node)
                )
            );
        }

        return Asset::getByPath($filePath);
    }
}

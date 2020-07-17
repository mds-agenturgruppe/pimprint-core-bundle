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

namespace Mds\PimPrint\CoreBundle\Service;

/**
 * Class SpecialChars
 *
 * @see     www.absatzsetzer.de/downloads/Zeichen.pdf
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class SpecialChars
{
    /**
     * Automatische Seitenzahl A ⇧⌘⌥N ^N ~N
     *
     * @var int
     */
    const AUTO_PAGE_NUMBER = 0x0018;

    /**
     * Abschnittsmarke (Link) M ^x ~x
     *
     * @var int
     */
    const SECTION_MARKER = 0x0019;

    /**
     * Leerraum (Link) \s
     *
     * @var int
     */
    const SPACE = 0x0020;

    /**
     * Aufzählungszeichen • ⌥ü ^8 ~8
     *
     * @var int
     */
    const BULLET_CHARACTER = 0x2022;

    /**
     * Absatzende (Link) ¶ ↩ ^p \r
     *
     * @var int
     */
    const END_OF_PARAGRAPH = 0x000D;

    /**
     * Harter Zeilenumbruch (Link) ¬ ⌘↩ ^n \n
     *
     * @var int
     */
    const FORCED_LINE_BREAK = 0x000A;

    /**
     * Copyrightsymbol © ⌥G ^2 ~2
     *
     * @var int
     */
    const SYMBOL_COPYRIGHT = 0x00A9;

    /**
     * Grad-Zeichen ° ⇧^ ° °
     *
     * @var int
     */
    const SYMBOL_DEGREE = 0x00B0;

    /**
     * Auslassungszeichen (Ellipse) … ⌥ . ^e ~e
     *
     * @var int
     */
    const ELLIPSIS = 0x2026;

    /**
     * Abschnittszeichen (-marke) ¶ ^7 ~7
     *
     * @var int
     */
    const SYMBOL_PARAGRAPH = 0x00B6;

    /**
     * Symbol für eingetragene Marke ® ⌥R ^r ~r
     *
     * @var int
     */
    const SYMBOL_REGISTERED_TRADEMARK = 0x00AE;

    /**
     * Paragraphenzeichen § ⇧3 ^6 ~6
     *
     * @var int
     */
    const SYMBOL_SECTION = 0x00A7;

    /**
     * Symbol für Marke ™ ⌥⇧D ^d ~d
     *
     * @var int
     */
    const SYMBOL_TRADEMARK = 0x2122;

    /**
     * Geviertstrich — ^_ ~_
     *
     * @var int
     */
    const EM_DASH = 0x2014;

    /**
     * 1/2-Geviertstrich – ^> ~>
     *
     * @var int
     */
    const EN_DASH = 0x2013;

    /**
     * Bedingter Trennstrich ohne Trennung ⇧⌘- ^- ~-
     *
     * @var int
     */
    const HYPHEN_DISCRETIONARY = 0x00AD;

    /**
     * Geschützter Trennstrich - ⌥⌘- ^~ ~~
     *
     * @var int
     */
    const HYPHEN_NON_BREAKING = 0x2011;

    /**
     * Geviert-Leerraum ⇧⌘M ^m ~m
     *
     * @var int
     */
    const EM_SPACE = 0x2003;

    /**
     * 1/2-Geviert-Leerraum ⇧⌘N ^= ~=
     *
     * @var int
     */
    const EN_SPACE = 0x2002;

    /**
     * Geschützter Leerraum ⌥⌘X ^S ~S
     *
     * @var int
     */
    const NON_BREAKING_SPACE = 0x00A0;

    /**
     * Geschützter Leerraum (feste Breite)(Link) ^s ~s
     *
     * @var int
     */
    const NON_BREAKING_SPACE_FIXED_WIDTH = 0x202F;

    /**
     * 1/24-Geviert-Leerraum ^| ~|
     *
     * @var int
     */
    const HAIR_SPACE = 0x200A;

    /**
     * Sechstelgeviert Leerraum ^% ~%
     *
     * @var int
     */
    const SIXTH_SPACE = 0x2006;

    /**
     * Achtelgeviert-Leerraum ⇧⌘⌥M ^< ~<
     *
     * @var int
     */
    const THIN_SPACE = 0x2009;

    /**
     * Viertelgeviert-Leerraum ^4 ~4
     *
     * @var int
     */
    const QUATER_SPACE = 0x2005;

    /**
     * Drittelgeviert-Leerraum ^3 ~3
     *
     * @var int
     */
    const THIRD_SPACE = 0x2004;

    /**
     * Interpunktionsleerzeichen ^. ~.
     *
     * @var int
     */
    const PUNTUATION_SPACE = 0x2008;

    /**
     * Ziffernleerzeichen ^/ ~/
     *
     * @var int
     */
    const FIGURE_SPACE = 0x2007;

    /**
     * Ausgleichsleerzeichen (Link) ^f ~f
     *
     * @var int
     */
    const FLUSH_SPACE = 0x2001;

    /**
     * Öffnendes Anführungszeichen „ ⇧2* | ⇧⌥W ^{ ~{
     *
     * @var int
     */
    const DOUBLE_LEFT_QUOTATION_MARK = 0x201E;

    /**
     * Schließendes Anführungszeichen n“ ⇧2* ^} ~}
     *
     * @var int
     */
    const DOUBLE_RIGHT_QUOTATION_MARK = 0x201C;

    /**
     * Anführungszeichen 66 “ ⌥2 ^} ~}
     *
     * @var int
     */
    const DOUBLE_LEFT_QUOTATION_MARK2 = 0x201C;

    /**
     * Anführungszeichen 99 ” ⇧⌥2 ^} ~}
     *
     * @var int
     */
    const DOUBLE_RIGHT_QUOTATION_MARK2 = 0x201D;

    /**
     * Öffnendes einf. Anführungszeichen ‘ ⌥#* ^[ ~[
     *
     * @var int
     */
    const SINGLE_LEFT_QUOTATION_MARK = 0x2018;

    /**
     * Schließendes einf. Anführungszeichen ’ ⇧⌥#* ^] ~]
     *
     * @var int
     */
    const SINGLE_RIGHT_QUOTATION_MARK = 0x2019;

    /**
     * Tiefgestelltes Anführungszeichen 9 ‚ ⇧#* |⌥s
     *
     * @var int
     */
    const SINGE_LOW_9_QUOTATION_MARK = 0x201A;

    /**
     * Gerades einfaches Anführungszeichen ' ctrl ß ^' ~'
     *
     * @var int
     */
    const APOSTROPHE = 0x0027;

    /**
     * Gerades doppeltes Anführungszeichen " ⇧ctrl ß ^" ~"
     *
     * @var int
     */
    const QUOTATION_MARK = 0x0022;

    /**
     * Bedingter Zeilen umbruch | ^k ~k
     *
     * @var int
     */
    const DISCRETIONARY_LINE_BREAK = 0x200B;

    /**
     * Tabulator für Einzug rechts ⇧⇥ ^y ~y
     *
     * @var int
     */
    const RIGHT_INDENT_TAB = 0x0008;

    /**
     * Tabulator ⇥ ^t ~t
     *
     * @var int
     */
    const TABULATOR = 0x0009;

    /**
     * Einzug bis hierhin ⌘´ ^i ~i
     *
     * @var int
     */
    const INDENT_TO_HERE = 0x0007;

    /**
     * Verschachteltes Format hier beenden \ ^h ~h
     *
     * @var int
     */
    const END_NESTED_STYLE_HERE = 0x0003;

    /**
     * Verbindung unterdrücken ^j ~j
     * @var int
     */
    const NONJOINER_LINK = 0x200C;

    /**
     * Verankertes Objekt (Link) ¥ ^a ~a
     *
     * @var int
     */
    const ANCHORED_OBJEKT = 0xFFFC;

    /**
     * Caret-Zeichen ^ ^^ \^
     *
     * @var int
     */
    const CARET_CHARACTE = 0x005E;

    /**
     * Indexmarke (Link) 􀏒U ^I ~I
     *
     * @var int
     */
    const INDEX_MARKER = 0xFEFF;

    /**
     * Returns utf8 char for $number.
     *
     * @param int $number
     *
     * @return string
     */
    public function utf8($number)
    {
        if ($number <= 0x7F) {
            return chr($number);
        }
        if ($number <= 0x7FF) {
            return chr(($number >> 6) + 192) .
                chr(($number & 63) + 128);
        }
        if ($number <= 0xFFFF) {
            return chr(($number >> 12) + 224) .
                chr((($number >> 6) & 63) + 128) .
                chr(($number & 63) + 128);
        }
        if ($number <= 0x1FFFFF) {
            return chr(($number >> 18) + 240) .
                chr((($number >> 12) & 63) + 128) .
                chr((($number >> 6) & 63) + 128) .
                chr(($number & 63) + 128);
        }

        return '';
    }
}

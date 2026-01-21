<?php
/**
 * mds. Agenturgruppe GmbH
 *
 * This source file is available under the terms of the
 * mds. Commercial License (MCL)
 *
 * Full copyright and license information is available in
 * LICENSE.md, which is distributed with this source code.
 *
 * @copyright Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license   mds. Commercial License (MCL)
 */

namespace Mds\PimPrint\CoreBundle\InDesign\Html;

use Mds\PimPrint\CoreBundle\InDesign\Text;

/**
 * Class TextParser
 *
 * @package Mds\PimPrint\CoreBundle\InDesign\Html
 */
class TextParser extends AbstractParser
{
    /**
     * Returns a target Text instance.
     *
     * @return Text
     * @throws \Exception
     */
    public function getText(): Text
    {
        if (!$this->text instanceof Text) {
            throw new \Exception('No target Text instance set.');
        }

        return $this->text;
    }

    /**
     * Sets target Text instance.
     *
     * @param Text $text
     *
     * @return TextParser
     */
    public function setText(Text $text): TextParser
    {
        $this->text = $text;

        return $this;
    }
}

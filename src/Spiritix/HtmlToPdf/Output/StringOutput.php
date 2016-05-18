<?php
/**
 * This file is part of the spiritix/html-to-pdf package.
 *
 * @copyright Copyright (c) Matthias Isler <mi@matthias-isler.ch>
 * @license   MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiritix\HtmlToPdf\Output;

/**
 * Output handler for getting the PDF contents as a string.
 *
 * @package Spiritix\HtmlToPdf\Output
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
class StringOutput extends AbstractOutput
{
    /**
     * Returns the PDF contents.
     *
     * @return string
     */
    public function get()
    {
        return $this->getPdfData();
    }
}
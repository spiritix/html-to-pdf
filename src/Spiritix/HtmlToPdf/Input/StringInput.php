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

namespace Spiritix\HtmlToPdf\Input;

/**
 * Input handler for providing HTML markup as a string.
 *
 * @package Spiritix\HtmlToPdf\Input
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
class StringInput extends AbstractInput
{
    /**
     * Set the HTML markup.
     *
     * @param string $html HTML markup
     *
     * @throws InputException If input is not a string or empty
     * @throws InputException If input is not valid HTML
     */
    public function setHtml($html)
    {
        if (!is_string($html) || empty($html)) {
            throw new InputException('Input is empty or not a string');
        }

        if ($html === strip_tags($html)) {
            throw new InputException('Input must be valid HTML markup');
        }

        $this->html = $html;
    }
}
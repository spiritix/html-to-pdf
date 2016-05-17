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
 * TODO
 *
 * @package Spiritix\HtmlToPdf\Input
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
class StringInput implements InputInterface
{
    /**
     * The HTML markup.
     *
     * @var null|string
     */
    private $html = null;

    /**
     * Set the HTML markup.
     *
     * @param string $html HTML markup
     *
     * @throws InputException If input is not a valid string
     */
    public function setHtml($html)
    {
        if (!is_string($html) || empty($html)) {
            throw new InputException('Input must be an HTML string');
        }

        $this->html = $html;
    }

    /**
     * Returns the HTML markup.
     *
     * @throws InputException If input has not yet been set
     *
     * @return string
     */
    public function getHtml()
    {
        if ($this->html === null) {
            throw new InputException('Input has not yet been set');
        }

        return $this->html;
    }
}
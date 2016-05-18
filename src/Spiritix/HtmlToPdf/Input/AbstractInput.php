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
 * Abstract input handler.
 *
 * @package Spiritix\HtmlToPdf\Input
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
abstract class AbstractInput implements InputInterface
{
    /**
     * The HTML markup.
     *
     * @var null|string
     */
    protected $html = null;

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
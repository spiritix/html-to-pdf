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
 * Abstract output handler.
 *
 * @package Spiritix\HtmlToPdf\Output
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
abstract class AbstractOutput implements OutputInterface
{
    /**
     * Contains the PDF binary data.
     *
     * @var null|string
     */
    private $pdfData = null;

    /**
     * Must accept the PDF binary data as an argument.
     *
     * @param string $data The binary PDF data
     */
    public function setPdfData($data)
    {
        $this->pdfData = $data;
    }

    /**
     * Returns the PDF binary data.
     *
     * @throws OutputException If data has not yet been set
     *
     * @return string
     */
    protected function getPdfData()
    {
        if ($this->pdfData === null) {
            throw new OutputException('PDF data has not yet been set');
        }

        return $this->pdfData;
    }
}
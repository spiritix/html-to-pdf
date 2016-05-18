<?php

namespace Spiritix\HtmlToPdf\Tests\Output;

use Spiritix\HtmlToPdf\Output\EmbedOutput;
use Spiritix\HtmlToPdf\Tests\TestCase;

class EmbedOutputTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testEmbed()
    {
        $pdfData = $this->getPdfSampleData();

        $output = new EmbedOutput();
        $output->setPdfData($pdfData);

        ob_start();
        $output->embed('sample.pdf', false);
        $data = ob_get_clean();

        $this->assertEquals($pdfData, $data);
    }
}
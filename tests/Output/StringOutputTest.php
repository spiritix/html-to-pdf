<?php

namespace Spiritix\HtmlToPdf\Tests\Output;

use Spiritix\HtmlToPdf\Output\StringOutput;
use Spiritix\HtmlToPdf\Tests\TestCase;

class StringOutputTest extends TestCase
{
    public function testGet()
    {
        $pdfData = $this->getPdfSampleData();

        $output = new StringOutput();
        $output->setPdfData($pdfData);

        $this->assertEquals($pdfData, $output->get());
    }
}
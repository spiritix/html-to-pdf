<?php

namespace Spiritix\HtmlToPdf\Tests\Output;

use Spiritix\HtmlToPdf\Output\FileOutput;
use Spiritix\HtmlToPdf\Tests\TestCase;

class FileOutputTest extends TestCase
{
    public function testStore()
    {
        $pdfData = $this->getPdfSampleData();

        $output = new FileOutput();
        $output->setPdfData($pdfData);

        $url = '/tmp/sample.pdf';
        $output->store($url);

        $this->assertEquals(file_get_contents($url), $pdfData);
    }
}
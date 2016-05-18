<?php

namespace Spiritix\HtmlToPdf\Tests\Output;

use Spiritix\HtmlToPdf\Output\DownloadOutput;
use Spiritix\HtmlToPdf\Tests\TestCase;

class DownloadOutputTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testDownload()
    {
        $pdfData = $this->getPdfSampleData();

        $output = new DownloadOutput();
        $output->setPdfData($pdfData);

        ob_start();
        $output->download('sample.pdf', false);
        $data = ob_get_clean();

        $this->assertEquals($pdfData, $data);
    }
}
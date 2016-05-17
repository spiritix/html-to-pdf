<?php

namespace Spiritix\HtmlToPdf\Tests;

use Spiritix\HtmlToPdf\HtmlToPdf;

class HtmlToPdfTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanceCreation()
    {
        $htmlToPdf = new HtmlToPdf();
        $this->assertTrue($htmlToPdf instanceof HtmlToPdf);
    }
}
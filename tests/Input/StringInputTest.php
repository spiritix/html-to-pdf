<?php

namespace Spiritix\HtmlToPdf\Tests\Input;

use Spiritix\HtmlToPdf\Input\StringInput;
use Spiritix\HtmlToPdf\Tests\TestCase;

class StringInputTest extends TestCase
{
    public function testSetHtml()
    {
        $html = '<h1>Hello</h1>';

        $input = new StringInput();
        $input->setHtml($html);

        $this->assertEquals($html, $input->getHtml());
    }
}
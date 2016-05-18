<?php

namespace Spiritix\HtmlToPdf\Tests\Input;

use Spiritix\HtmlToPdf\Input\UrlInput;
use Spiritix\HtmlToPdf\Tests\TestCase;

class UrlInputTest extends TestCase
{
    public function testSetUrl()
    {
        $input = new UrlInput();
        $input->setUrl('https://www.google.com');

        $this->assertContains('Google', $input->getHtml());
    }
}
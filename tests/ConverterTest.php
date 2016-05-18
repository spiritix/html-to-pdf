<?php

namespace Spiritix\HtmlToPdf\Tests;

use Spiritix\HtmlToPdf\Converter;
use Spiritix\HtmlToPdf\Input\StringInput;
use Spiritix\HtmlToPdf\Output\StringOutput;

class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private $converter;

    public function setUp()
    {
        parent::setUp();

        $input = new StringInput();
        $input->setHtml('<h1>Hello</h1>');

        $this->converter = new Converter($input, new StringOutput());
    }

    public function testOptions()
    {
        $this->converter->setOption('R', '500');
        $this->converter->setOption('margin-top', '100');

        $this->converter->setOptions([
            'B' => '50',
            'margin-left' => '10',
        ]);

        $value = $this->converter->getOption('R');
        $this->assertEquals('500', $value);

        $value = $this->converter->getOption('margin-top');
        $this->assertEquals('100', $value);

        $options = $this->converter->getOptions();
        $this->assertEquals([
            'R' => '500',
            'margin-top' => '100',
            'B' => '50',
            'margin-left' => '10',
        ], $options);
    }

    public function testConvert()
    {
        $output = $this->converter->convert();

        $this->assertInstanceOf(StringOutput::class, $output);
    }
}
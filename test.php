<?php

require __DIR__ . '/vendor/autoload.php';

use Spiritix\HtmlToPdf\HtmlToPdf;

$htmlToPdf = new HtmlToPdf();
$htmlToPdf->setBinPath(dirname(__FILE__) . '/vendor/');

echo $htmlToPdf->setOption('zoom', 2)
    ->run(HtmlToPdf::MODE_STRING, 'http://www.google.com');
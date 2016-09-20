# HtmlToPdf

Convert HTML markup into beautiful PDF files using the famous wkhtmltopdf library.

[![Build Status](https://travis-ci.org/spiritix/html-to-pdf.svg?branch=master)](https://travis-ci.org/spiritix/html-to-pdf)
[![Code Climate](https://codeclimate.com/github/spiritix/html-to-pdf/badges/gpa.svg)](https://codeclimate.com/github/spiritix/html-to-pdf)
[![Total Downloads](https://poser.pugx.org/spiritix/html-to-pdf/d/total.svg)](https://packagist.org/packages/spiritix/html-to-pdf)
[![Latest Stable Version](https://poser.pugx.org/spiritix/html-to-pdf/v/stable.svg)](https://packagist.org/packages/spiritix/html-to-pdf)
[![Latest Unstable Version](https://poser.pugx.org/spiritix/html-to-pdf/v/unstable.svg)](https://packagist.org/packages/spiritix/html-to-pdf)
[![License](https://poser.pugx.org/spiritix/html-to-pdf/license.svg)](https://packagist.org/packages/spiritix/html-to-pdf)

## Features

- Does not require any complex installation or configuration, works out of the box
- Offers all [options](http://wkhtmltopdf.org/usage/wkhtmltopdf.txt) of wkhtmltopdf
- Possibility to get the HTML from an external URL
- Possibility to download, embed or save the generated PDF files (or even get their contents as a string)
- PHP7 and HHVM ready

## Requirements

- PHP 5.5+
- Enabled program execution functions (proc_open)
- Enabled 'fopen' wrappers (in order to use the URL to PDF functionality)
- Unix based operating system (Windows support ~~will~~ may be added in the future)

## Installation

HtmlToPdf can be installed via [Composer](http://getcomposer.org) by requiring the
`spiritix/html-to-pdf` package in your project's `composer.json`.

```sh
php composer.phar require spiritix/html-to-pdf
```

## Usage

The usage of this library is very simple. 
You just need a converter instance, pass an input and an output handler to it and set some options if you like.
After running the conversion, the converter will provide you with the output handler instance.
Now you may use it's specific functionality to get your PDF file.

```php
use Spiritix\HtmlToPdf\Converter;
use Spiritix\HtmlToPdf\Input\UrlInput;
use Spiritix\HtmlToPdf\Output\DownloadOutput;

$input = new UrlInput();
$input->setUrl('https://www.google.com');

$converter = new Converter($input, new DownloadOutput());

$converter->setOption('n');
$converter->setOption('d', '300');

$converter->setOptions([
    'no-background',
    'margin-bottom' => '100',
    'margin-top' => '100',
]);

$output = $converter->convert();
$output->download();
```

### Input handlers

The following input handlers are available:

- StringInput - Provide the PDF contents as a string
- UrlInput - Fetch the PDF contents from an URL

### Output handlers

The following output handlers are available:

- StringOutput - Get the PDF contents as a string
- FileOutput - Store the PDF to your file system
- DownloadOutput - Force the browser to download the PDF file
- EmbedOutput - Force the browser to embed the PDF file

## Options

    General Options
        allow                   <path>      Allow the file or files from the specified folder to be loaded (repeatable)
    b,  book*                               Set the options one would usually set when printing a book
        collate                             Collate when printing multiple copies
        cookie                  <name> <value>     Set an additional cookie (repeatable)
        cookie-jar              <path>      Read and write cookies from and to the supplied cookie jar file
        copies                  <number>    Number of copies to print into the pdf file (default 1)
        cover*                  <url>       Use html document as cover. It will be inserted before the toc with no headers and footers
        custom-header           <name> <value>     Set an additional HTTP header (repeatable)
        debug-javascript                    Show javascript debugging output
    H,  default-header*                     Add a default header, with the name of the page to the left, and the page number to the right, this is short for: header-left='[webpage]' header-right='[page]/[toPage]' top 2cm header-line
        disable-external-links*             Do no make links to remote web pages
        disable-internal-links*             Do no make local links
    n,  disable-javascript                  Do not allow web pages to run javascript
        disable-pdf-compression*            Do not use lossless compression on pdf objects
        disable-smart-shrinking*            Disable the intelligent shrinking strategy used by WebKit that makes the pixel/dpi ratio none constant
        disallow-local-file-access          Do not allowed conversion of a local file to read in other local files, unless explecitily allowed with allow
    d,  dpi                     <dpi>       Change the dpi explicitly (this has no effect on X11 based systems)
        enable-plugins                      Enable installed plugins (such as flash
        encoding                <encoding>  Set the default text encoding, for input
        forms*                              Turn HTML form fields into pdf form fields
    g,  grayscale                           PDF will be generated in grayscale
        ignore-load-errors                  Ignore pages that claimes to have encountered an error during loading
    l,  lowquality                          Generates lower quality pdf/ps. Useful to shrink the result document space
    B,  margin-bottom           <unitreal>  Set the page bottom margin (default 10mm)
    L,  margin-left             <unitreal>  Set the page left margin (default 10mm)
    R,  margin-right            <unitreal>  Set the page right margin (default 10mm)
    T,  margin-top              <unitreal>  Set the page top margin (default 10mm)
        minimum-font-size       <int>       Minimum font size (default 5)
        no-background                       Do not print background
    O,  orientation             <orientation>     Set orientation to Landscape or Portrait
        page-height             <unitreal>  Page height (default unit millimeter)
        page-offset*            <offset>    Set the starting page number (default 1)
    s,  page-size               <size>      Set paper size to: A4, Letter, etc.
        page-width              <unitreal>  Page width (default unit millimeter)
        password                <password>  HTTP Authentication password
        post                    <name> <value>    Add an additional post field (repeatable)
        post-file               <name> <path>     Post an aditional file (repeatable)
        print-media-type*                   Use print media-type instead of screen
    p,  proxy                   <proxy>     Use a proxy
    q,  quiet                               Be less verbose
        redirect-delay          <msec>      Wait some milliseconds for js-redirects (default 200)
        replace*                <name> <value>     Replace [name] with value in header and footer (repeatable)
        stop-slow-scripts                   Stop slow running javascripts
        title                   <text>      The title of the generated pdf file (The title of the first document is used if not specified)
    t,  toc*                                Insert a table of content in the beginning of the document
        use-xserver*                        Use the X server (some plugins and other stuff might not work without X11)
        user-style-sheet        <url>       Specify a user style sheet, to load with every page
        username                <username>  HTTP Authentication username
        zoom                    <float>     Use this zoom factor (default 1)
    
    Headers And Footer Options
        footer-center*           <text>     Centered footer text
        footer-font-name*        <name>     Set footer font name (default Arial)
        footer-font-size*        <size>     Set footer font size (default 11)
        footer-html*             <url>      Adds a html footer
        footer-left*             <text>     Left aligned footer text
        footer-line*                        Display line above the footer
        footer-right*            <text>     Right aligned footer text
        footer-spacing*          <real>     Spacing between footer and content in mm (default 0)
        header-center*           <text>     Centered header text
        header-font-name*        <name>     Set header font name (default Arial)
        header-font-size*        <size>     Set header font size (default 11)
        header-html*             <url>      Adds a html header
        header-left*             <text>     Left aligned header text
        header-line*                        Display line below the header
        header-right*            <text>     Right aligned header text
        header-spacing*          <real>     Spacing between header and content in mm (default 0)
    
    Table Of Content Options
        toc-depth*               <level>    Set the depth of the toc (default 3)
        toc-disable-back-links*             Do not link from section header to toc
        toc-disable-links*                  Do not link from toc to sections
        toc-font-name*           <name>     Set the font used for the toc (default Arial)
        toc-header-font-name*    <name>     The font of the toc header (if unset use toc-font-name)
        toc-header-font-size*    <size>     The font size of the toc header (default 15)
        toc-header-text*         <text>     The header text of the toc (default Table Of Contents)
        toc-l1-font-size*        <size>     Set the font size on level 1 of the toc (default 12)
        toc-l1-indentation*      <num>      Set indentation on level 1 of the toc (default 0)
        toc-l2-font-size*        <size>     Set the font size on level 2 of the toc (default 10)
        toc-l2-indentation*      <num>      Set indentation on level 2 of the toc (default 20)
        toc-l3-font-size*        <size>     Set the font size on level 3 of the toc (default 8)
        toc-l3-indentation*      <num>      Set indentation on level 3 of the toc (default 40)
        toc-l4-font-size*        <size>     Set the font size on level 4 of the toc (default 6)
        toc-l4-indentation*      <num>      Set indentation on level 4 of the toc (default 60)
        toc-l5-font-size*        <size>     Set the font size on level 5 of the toc (default 4)
        toc-l5-indentation*      <num>      Set indentation on level 5 of the toc (default 80)
        toc-l6-font-size*        <size>     Set the font size on level 6 of the toc (default 2)
        toc-l6-indentation*      <num>      Set indentation on level 6 of the toc (default 100)
        toc-l7-font-size*        <size>     Set the font size on level 7 of the toc (default 0)
        toc-l7-indentation*      <num>      Set indentation on level 7 of the toc (default 120)
        toc-no-dots*                        Do not use dots, in the toc
    
    Outline Options
        dump-outline*            <file>     Dump the outline to a file
        outline*                            Put an outline into the pdf
        outline-depth*           <level>    Set the depth of the outline (default 4)
    
    Options marked * may not work on some servers.

## Known issues and limitations

- Does not work on Windows based systems

## Contributing

Contributions in any form are welcome.
Please consider the following guidelines before submitting pull requests:

- **Coding standard** - It's mostly [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) with some differences. 
- **Add tests!** - Your PR won't be accepted if it doesn't have tests.
- **Create feature branches** - I won't pull from your master branch.

## License

HtmlToPdf is free software distributed under the terms of the MIT license.
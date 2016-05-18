<?php
/**
 * This file is part of the spiritix/html-to-pdf package.
 *
 * @copyright Copyright (c) Matthias Isler <mi@matthias-isler.ch>
 * @license   MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spiritix\HtmlToPdf\Input;

/**
 * Input handler for fetching HTML markup from an URL.
 *
 * @package Spiritix\HtmlToPdf\Input
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
class UrlInput extends AbstractInput
{
    /**
     * Set the URL from which the HTML markup should be fetched.
     *
     * @param string $url Absolute URL
     *
     * @throws InputException If fopen wrappers are disabled
     * @throws InputException If input is not a valid URL
     * @throws InputException If URL could not be fetched
     * @throws InputException If URL does not return valid HTML
     */
    public function setUrl($url)
    {
        if (!ini_get('allow_url_fopen')) {
            throw new InputException('Fopen wrappers are disabled');
        }

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            throw new InputException('Input is not a valid URL');
        }

        $content = file_get_contents($url);

        if ($content === false) {
            throw new InputException('Could not fetch URL');
        }

        if ($content === strip_tags($content)) {
            throw new InputException('URL does not return valid HTML markup');
        }

        $this->html = $content;
    }
}
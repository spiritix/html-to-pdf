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

namespace Spiritix\HtmlToPdf;

/**
 * Convert HTML markup into beautiful PDF files using the famous wkhtmltopdf library.
 *
 * @todo Add support for Windows
 * @todo Write unit tests
 *
 * @package Spiritix\HtmlToPdf
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
class HtmlToPdf
{
    /**
     * Fore browser to download file
     */
    const MODE_DOWNLOAD = 1;

    /**
     * Return PDF source as string
     */
    const MODE_STRING = 2;

    /**
     * Try to embed PDF in browser
     */
    const MODE_EMBEDDED = 3;

    /**
     * Save output to a file
     */
    const MODE_SAVE = 4;

    /**
     * Prefix for shortcut options
     */
    const SHELL_COMMAND_SHORT = '-';

    /**
     * Prefix for regular options
     */
    const SHELL_COMMAND_LONG = '--';

    /**
     * Path to Intel library
     */
    const LIBRARY_INTEL = 'h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386';

    /**
     * Path to AMD library
     */
    const LIBRARY_AMD = 'h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64';

    /**
     * Contains all options
     *
     * @var array
     */
    private $options = array();

    /**
     * Contains path to binary files
     *
     * @var null|string
     */
    private $binPath = null;

    /**
     * Contains input, must be valid URL or HTML markup
     *
     * @var null|string
     */
    private $input = null;

    /**
     * Filename of PDF to be generated
     *
     * @var null|string
     */
    private $fileName = null;

    /**
     * Performs some basic environment checks and provides a shortcut setter for options
     *
     * @param array $options Multiple valid options
     *
     * @throws HtmlToPdfException If shell functions are disabled
     * @throws HtmlToPdfException If OS is not Unix based
     */
    public function __construct($options = array())
    {
        $disabled = explode(', ', ini_get('disable_functions'));
        if (!function_exists('proc_open') || in_array('proc_open', $disabled)) {

            throw new HtmlToPdfException(__CLASS__ . ' requires shell functions to be enabled');
        }

        if (DIRECTORY_SEPARATOR != '/') {
            throw new HtmlToPdfException(__CLASS__ . ' requires a Unix based operating system');
        }

        $this->setOptions($options);
    }

    /**
     * Sets a single option
     *
     * @param string $key Option key
     * @param string $value Option value
     *
     * @throws HtmlToPdfException If option key is empty
     * @throws HtmlToPdfException If option appears to be a shell command
     *
     * @return HtmlToPdf
     */
    public function setOption($key, $value = '')
    {
        if (empty($key)) {
            throw new HtmlToPdfException('Option key must not be empty');
        }
        if (substr($key, 0, 1) == self::SHELL_COMMAND_SHORT) {
            throw new HtmlToPdfException('Please do not provide shell commands as options');
        }

        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Set multiple options at once
     *
     * @param array $options Multiple valid options
     * @throws HtmlToPdfException If options are empty
     *
     * @return HtmlToPdf
     */
    public function setOptions($options = array())
    {
        if (!is_array($options)) {
            throw new HtmlToPdfException('Options must be an array');
        }

        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        return $this;
    }

    /**
     * Returns all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns a single option
     *
     * @param string $key Option key
     * @throws HtmlToPdfException If option has not been set
     *
     * @return string Option value
     */
    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        throw new HtmlToPdfException('Option "' . $key . '" has not been set');
    }

    /**
     * Set path to binary files
     *
     * Since the binary files are provided as a composer requirement,
     * they are available in the 'vendor' folder which must be provided here.
     *
     * @param string $path Absolute path to vendor dir
     * @throws HtmlToPdfException If path is not absolute
     *
     * @return HtmlToPdf
     */
    public function setBinPath($path)
    {
        $realPath = realpath($path);

        if ($realPath === false) {
            throw new HtmlToPdfException('Binary path must be absolute');
        }

        $this->_binPath = $realPath . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Returns path to binary files
     *
     * @throws HtmlToPdfException If path has not been set
     * @return null|string
     */
    public function getBinPath()
    {
        if (!is_dir($this->binPath)) {
            throw new HtmlToPdfException('Binary path has not been set');
        }

        return $this->binPath;
    }

    /**
     * Set the target name of the PDF file
     *
     * @param string $fileName Name of PDF file
     * @return HtmlToPdf
     */
    public function setFileName($fileName = '')
    {
        $this->_fileName = $fileName;
        return $this;
    }

    /**
     * Returns the name of the target PDF file
     *
     * @return null|string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Sets input URL or HTML
     *
     * @param string $input Valid URL or HTML markup
     *
     * @throws HtmlToPdfException If input is empty
     * @throws HtmlToPdfException If target URL could not be loaded
     * @throws HtmlToPdfException If input is not valid HTML markup
     *
     * @return HtmlToPdf
     */
    public function setInput($input)
    {
        if (empty($input)) {
            throw new HtmlToPdfException('Input must must not be empty');
        }

        if (filter_var($input, FILTER_VALIDATE_URL) !== FALSE) {
            $input = file_get_contents($input);

            if (empty($input)) {
                throw new HtmlToPdfException('Unable to fetch content from provided URL');
            }
        }

        if ($input == strip_tags($input)) {
            throw new HtmlToPdfException('Input must be either a valid URL or HTML markup');
        }

        $this->_input = $input;
        return $this;
    }

    /**
     * Returns input HTML
     *
     * This method does never return an input URL since the class immediately fetches it into HTML.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Main entry point, executes the converter
     *
     * @param string $mode Target mode
     * @param null|string $input Input URL or HTML markup, overrides input provided using setInput
     * @param null|string $fileName Target filename, overrides file name provided using setFileName
     *
     * @throws HtmlToPdfException If mode is not valid
     *
     * @return void|string HTML markup if mode set to MODE_STRING
     */
    public function run($mode, $input = null, $fileName = null)
    {
        if ($input) {
            $this->setInput($input);
        }

        if ($fileName) {
            $this->setFileName($fileName);
        }

        $fileName = $this->getFileName();
        $output = $this->render();

        switch ($mode) {

            case self::MODE_DOWNLOAD:
                $this->modeDownload($fileName, $output);
                break;

            case self::MODE_EMBEDDED:
                $this->modeEmbedded($fileName, $output);
                break;

            case self::MODE_SAVE:
                $this->modeSave($fileName, $output);
                break;

            case self::MODE_STRING:
                return $this->modeString($output);

            default:
                throw new HtmlToPdfException('Mode is not valid');
        }
    }

    /**
     * Execute shell command
     *
     * @param string $command Shell command
     * @param string $input HTML markup
     *
     * @return array Stream result
     */
    protected function executeShell($command, $input = '')
    {
        $result = array();

        $proc = proc_open(
            $command,
            array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            ),
            $pipes
        );

        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $result['output'] = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $result['error'] = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $result['result'] = (int) proc_close($proc);

        return $result;
    }

    /**
     * Builds a shell command
     *
     * @return string Shell command
     */
    private function buildCommand()
    {
        $options = '';
        foreach ($this->options as $key => $value) {

            if (mb_strlen($key) == 1) {
                $key = self::SHELL_COMMAND_SHORT . $key;
            }
            else {
                $key = self::SHELL_COMMAND_LONG . $key;
            }

            $key = escapeshellcmd(trim($key));
            $value = escapeshellarg(trim($value));

            $options .= $key . ' ' . $value . ' ';
        }

        $command = $this->getLibrary() . ' ' . $options . ' - -';
        return $command;
    }

    /**
     * Detects the kernel type and returns the path to the binary
     *
     * @throws HtmlToPdfException If path to binaries has not been set
     * @return string Path to library
     */
    private function getLibrary()
    {
        $system = $this->executeShell('grep -i amd /proc/cpuinfo');
        $library = (!empty($system['output'])) ? self::LIBRARY_AMD : self::LIBRARY_INTEL;

        $path = $this->getBinPath() . $library;

        if (!file_exists($path)) {
            throw new HtmlToPdfException('Path to binaries seems not to be configured');
        }

        return $path;
    }

    /**
     * Converts HTML into PDF using shell
     *
     * @throws HtmlToPdfException If shell reported an error
     * @throws HtmlToPdfException If shell did not return any output
     *
     * @return string PDF source code
     */
    private function render()
    {
        $input = $this->getInput();
        $command = $this->buildCommand();

        $result = $this->executeShell($command, $input);

        if (strpos(mb_strtolower($result['error']), 'error')) {
            throw new HtmlToPdfException('Shell error: ' . $result['error']);
        }
        if ($result['result'] > 1) {
            throw new HtmlToPdfException('Shell error: ' . $result['result']);
        }
        if (mb_strlen($result['output']) === 0) {
            throw new HtmlToPdfException('No data returned');
        }

        return $result['output'];
    }

    /**
     * Force browser to download the PDF file
     *
     * @param string $fileName PDF file name
     * @param string $output PDF source code
     *
     * @throws HtmlToPdfException If headers have already been sent
     * @throws HtmlToPdfException If filename has not been set
     */
    private function modeDownload($fileName, $output)
    {
        if (headers_sent()) {
            throw new HtmlToPdfException('Headers have already been sent');
        }
        if (empty($fileName)) {
            throw new HtmlToPdfException('Please specify a filename');
        }

        header("Content-Description: File Transfer");
        header("Cache-Control: public; must-revalidate, max-age=0");
        header("Pragme: public");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate('D, d m Y H:i:s') . " GMT");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octec-stream", false);
        header("Content-Type: application/download", false);
        header("Content-Type: application/pdf", false);
        header('Content-Disposition: attachment; filename="' . basename($fileName) .'";');
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . mb_strlen($output));

        die($output);
    }

    /**
     * Force browser to display the PDF embedded
     *
     * @param string $fileName PDF file name
     * @param string $output PDF source code
     *
     * @throws HtmlToPdfException If headers have already been sent
     * @throws HtmlToPdfException If filename has not been set
     */
    private function modeEmbedded($fileName, $output)
    {
        if (headers_sent()) {
            throw new HtmlToPdfException('Headers have already been sent');
        }
        if (empty($fileName)) {
            throw new HtmlToPdfException('Please specify a filename');
        }

        header('Content-type: application/pdf');
        header('Cache-control: public, must-revalidate, max-age=0');
        header('Pragme: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d m Y H:i:s') . ' GMT');
        header('Content-Length: ' . mb_strlen($output));
        header('Content-Disposition: inline; filename="' . basename($fileName) .'";');

        die($output);
    }

    /**
     * Saves PDF to a file
     *
     * @param string $fileName PDF file name
     * @param string $output PDF source code
     *
     * @throws HtmlToPdfException If target directory is not writable
     * @throws HtmlToPdfException If target file is not writable
     */
    private function modeSave($fileName, $output)
    {
        $targetDir = dirname($fileName);

        if (!is_writeable($targetDir) || empty($fileName)) {
            throw new HtmlToPdfException('Target directory "' . $targetDir . '" is not writable');
        }

        if (!file_put_contents($fileName, $output)) {
            throw new HtmlToPdfException('File "' . $fileName . '" could not be saved');
        }
    }

    /**
     * Returns PDF source code as string
     *
     * @param string $output PDF source code
     * @return string
     */
    private function modeString($output)
    {
        return $output;
    }
}
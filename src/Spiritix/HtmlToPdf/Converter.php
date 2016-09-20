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

use Spiritix\HtmlToPdf\Input\InputInterface;
use Spiritix\HtmlToPdf\Output\OutputInterface;

/**
 * The actual HTML to PDF converter.
 *
 * @package Spiritix\HtmlToPdf
 * @author  Matthias Isler <mi@matthias-isler.ch>
 */
class Converter
{
    /**
     * Prefix for shortcut options.
     */
    const SHELL_COMMAND_PREFIX_SHORT = '-';

    /**
     * Prefix for regular options.
     */
    const SHELL_COMMAND_PREFIX_REGULAR = '--';

    /**
     * Relative path to the Intel binary.
     */
    const PATH_BINARY_INTEL = 'h4cc/wkhtmltopdf-i386/bin/wkhtmltopdf-i386';

    /**
     * Relative path to the AMD library,
     */
    const PATH_BINARY_AMD = 'h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64';

    /**
     * Input instance.
     *
     * @var InputInterface
     */
    private $input;

    /**
     * Output instance.
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * The options provided to the binary library.
     *
     * @var array
     */
    private $options = [];

    /**
     * Initialize converter.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $options Shortcut for setting multiple options
     */
    public function __construct(InputInterface $input, OutputInterface $output, $options = [])
    {
        $this->input = $input;
        $this->output = $output;

        $this->checkRequirements();

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Returns all options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns a single option.
     *
     * @param string $key Option key
     *
     * @throws ConverterException If option has not yet been set
     *
     * @return string Option value
     */
    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        throw new ConverterException('Option "' . $key . '" has not been set');
    }

    /**
     * Sets a single option.
     *
     * @param string $key   Option key, must not contain prefix
     * @param string $value Option value
     *
     * @throws ConverterException If option key is empty
     * @throws ConverterException If option key was provided with prefix
     *
     * @return Converter
     */
    public function setOption($key, $value = '')
    {
        if (!is_string($key) || empty($key)) {
            throw new ConverterException('Option key must not be empty');
        }

        if (substr($key, 0, 1) === self::SHELL_COMMAND_PREFIX_SHORT) {
            throw new ConverterException('Provide options without prefix');
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Set multiple options at once.
     *
     * @param array $options Multiple options
     *
     * @throws ConverterException If options are empty or invalid
     *
     * @return Converter
     */
    public function setOptions($options)
    {
        if (!is_array($options) || empty($options)) {
            throw new ConverterException('Provided options are invalid');
        }

        foreach ($options as $key => $value) {

            // Convert key-only options to regular ones
            if (is_numeric($key) && !empty($value)) {
                $key = $value;
                $value = '';
            }

            $this->setOption($key, $value);
        }

        return $this;
    }

    /**
     * Runs the conversion.
     *
     * @throws ConverterException If a binary error occurred
     * @throws ConverterException If a shell error occurred
     * @throws ConverterException If no data was returned
     *
     * @return OutputInterface
     */
    public function convert()
    {
        $result = $this->executeShellCommand($this->buildCommand(), $this->input->getHtml());

        if (strpos(mb_strtolower($result['error']), 'error') !== false) {
            throw new ConverterException('Binary error: ' . $result['error']);
        }

        if ($result['result'] > 1) {
            throw new ConverterException('Shell error: ' . $result['result']);
        }

        if (mb_strlen($result['output']) === 0) {
            throw new ConverterException('No data returned');
        }

        $this->output->setPdfData($result['output']);

        return $this->output;
    }

    /**
     * Checks if the host system meets all requirements for this library.
     *
     * @throws ConverterException If program execution functions are disabled
     * @throws ConverterException If system is Windows based
     */
    private function checkRequirements()
    {
        $disabled = explode(', ', ini_get('disable_functions'));

        if (!function_exists('proc_open') || in_array('proc_open', $disabled)) {
            throw new ConverterException('HtmlToPdf requires program execution functions to be enabled');
        }

        if (DIRECTORY_SEPARATOR !== '/') {
            throw new ConverterException('HtmlToPdf requires a Unix based system');
        }
    }

    /**
     * Builds the shell command for calling the binary.
     *
     * @return string
     */
    private function buildCommand()
    {
        $optionsString = '';
        foreach ($this->options as $key => $value) {

            if (mb_strlen($key) == 1) {
                $key = self::SHELL_COMMAND_PREFIX_SHORT . $key;
            }
            else {
                $key = self::SHELL_COMMAND_PREFIX_REGULAR . $key;
            }

            $key = escapeshellcmd(trim($key));
            $value = trim($value);

            $optionsString .= $key . (empty($value) ? '' : ' ' . escapeshellarg($value)) . ' ';
        }

        $command = $this->getBinaryPath() . ' ' . $optionsString . ' - -';

        return $command;
    }

    /**
     * Executes a shell command.
     *
     * @param string $command The command to be executed
     * @param string $input   The data to be provided through the input stream
     *
     * @return array
     */
    protected function executeShellCommand($command, $input = '')
    {
        $result = [];

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
     * Returns the absolute path to the binary.
     *
     * @todo The hardcoded paths to the vendor folders are not optimal.
     *
     * @throws ConverterException If binary could not be found
     *
     * @return string
     */
    private function getBinaryPath()
    {
        $system = $this->executeShellCommand('uname -a | grep 64');
        $library = (!empty($system['output'])) ? self::PATH_BINARY_AMD : self::PATH_BINARY_INTEL;

        // First of all we try a path assuming that this library is installed as a package
        $binaryPath = dirname(__FILE__) . '/../../../../../' . $library;

        // If this doesn't work, we try the vendors of this package
        if (!file_exists($binaryPath)) {
            $binaryPath = dirname(__FILE__) . '/../../../vendor/' . $library;
        }

        if (!file_exists($binaryPath)) {
            throw new ConverterException('Binary could not be found');
        }

        return $binaryPath;
    }
}
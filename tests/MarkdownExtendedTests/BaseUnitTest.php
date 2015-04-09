<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests;

class BaseUnitTest
    extends \PHPUnit_Framework_TestCase
{

    /**
     * Validates the paths getters of this class
     */
    public function testPaths()
    {
        $this->assertFileExists(
            $this->getPath(array(dirname(__DIR__), 'bootstrap.php')),
            '[internal test] getPath() to "tests/bootstrap.php"'
        );
        $this->assertFileExists(
            $this->getPath(array($this->getBasePath(), 'composer.json')),
            '[internal test] getBasePath() to "composer.json"'
        );
        $this->assertFileExists(
            $this->getResourcePath('test.md'),
            '[internal test] getResourcePath() to "tests/test.md" as a string'
        );
        $this->assertFileExists(
            $this->getResourcePath(array('test.md')),
            '[internal test] getResourcePath() to "tests/test.md" as an array'
        );
    }

    /**
     * Gets a wel-formatted path with environment-compliant directory separator
     *
     * @param array $parts
     * @return string
     */
    public static function getPath(array $parts)
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array_map(function($p){
                return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $p);
            }, $parts)
        );
    }

    /**
     * Get the path to the root of the package
     *
     * @return string
     */
    public function getBasePath()
    {
        return dirname(dirname(__DIR__));
    }

    /**
     * Get the tests test file path
     *
     * @return  string
     */
    public function getResourcePath($path)
    {
        $_paths = array($this->getBasePath(), 'tests');
        if (is_array($path)) {
            $_paths = array_merge($_paths, $path);
        } else {
            $_paths[] = $path;
        }
        return $this->getPath($_paths);
    }

    /**
     * Strip whitespaces between tags in a string
     *
     * @param   string  $content
     * @return  string
     */
    public function stripWhitespaces($content = '')
    {
        return trim(preg_replace('~>\s+<~', '><', $content));
    }

    /**
     * Strip new lines in a content
     * @param string $content
     * @return mixed
     */
    public function stripNewLines($content = '')
    {
        return str_replace("\n", ' ', $content);
    }

    /**
     * Alias of stripWhitespaces & stripNewLines
     */
    public function stripWhitespaceAndNewLines($content = '')
    {
        return $this->stripNewLines($this->stripWhitespaces($content));
    }

}

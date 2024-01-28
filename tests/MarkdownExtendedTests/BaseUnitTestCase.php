<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests;

class BaseUnitTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Validates the paths getters of this class
     */
    public function pathsExist()
    {
        $this->assertFileExists(
            $this->getPath([dirname(__DIR__), 'bootstrap.php']),
            '[internal test] getPath() to "tests/bootstrap.php"'
        );
        $this->assertFileExists(
            $this->getPath([$this->getBasePath(), 'composer.json']),
            '[internal test] getBasePath() to "composer.json"'
        );
        $this->assertFileExists(
            $this->getResourcePath('test.md'),
            '[internal test] getResourcePath() to "tests/test.md" as a string'
        );
        $this->assertFileExists(
            $this->getResourcePath(['test.md']),
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
            array_map(function ($p) {
                return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
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
        $_paths = [$this->getBasePath(), 'tests'];
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

    /**
     * Strip local base path from a content
     */
    public function cleanupBasePath($content = '')
    {
        return str_replace($this->getBasePath() . DIRECTORY_SEPARATOR, '', $content);
    }

    /**
     * Gets temporary directory
     */
    public function getTempDir()
    {
        return $this->getPath([$this->getBasePath(), 'tmp']);
    }

    /**
     * Cleanup temporary directory
     */
    public function flushTempDir()
    {
        $tmp = $this->getTempDir();
        exec('rm -rf '.$tmp);
        mkdir($tmp);
    }

    /**
     * Tests if a file exists in tmp dir by file name or regex mask
     */
    public function tempFileExists($file)
    {
        $dir = new \DirectoryIterator($this->getTempDir());
        foreach ($dir as $f) {
            if (0 !== preg_match('#^'.$file.'$#i', $f->getFilename())) {
                return true;
            }
        }
        return false;
    }
}

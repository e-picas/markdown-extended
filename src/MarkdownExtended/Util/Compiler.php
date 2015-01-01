<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use \Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles the whole markdown into a phar
 *
 * Taken "as is" from Composer (<http://github.com/composer/composer>)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @package MarkdownExtended\Util
 */
class Compiler
{

    public $root_dir;

    protected $_logs = array();

    static $phar_file = 'markdown-extended.phar';

    static $phar_loaders = array(
        'autoload.php',
        'composer/autoload_namespaces.php',
        'composer/autoload_classmap.php',
        'composer/autoload_real.php',
        'composer/autoload_psr4.php',
        'composer/include_paths.php',
        'composer/ClassLoader.php',
    );

    public function getDefaultFinder()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->exclude('bin')
            ->exclude('build')
            ->exclude('demo')
            ->exclude('phpdoc')
            ->exclude('phpdoc-2')
            ->exclude('tests')
            ->exclude('tmp')
            ->exclude('vendor')
            ->notName('CONTRIBUTING.md')
            ->notName('Compiler.php')
            ->notName('SplClassLoader.php')
            ->notName('sami.config.php')
            ->in($this->root_dir)
        ;
        return $finder;
    }

    /**
     * Compiles app into a single phar file
     *
     * @param  string            $pharFile The full path to the file to create
     */
    public function compile($pharFile = 'markdown-extended.phar', $root_dir = null)
    {
        if (is_null($root_dir)) {
            $this->root_dir = __DIR__.'/../../..';
        } else {
            $this->root_dir = $root_dir;
        }

        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'mde.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = $this->getDefaultFinder();
        $finder
            ->name('*.php')
        ;
        foreach ($finder as $file) {
            $this->__addFile($phar, $file);
        }

        $finder = $this->getDefaultFinder();
        $finder
            ->name('*.json')
            ->name('*.ini')
            ->name('*.man')
            ->name('*.md')
            ->name('*.html')
        ;
        foreach ($finder as $file) {
            $this->__addFile($phar, $file, false);
        }

        $vendor_path = $this->root_dir.'/vendor/';
        foreach (self::$phar_loaders as $_loader) {
            if (file_exists($vendor_path.$_loader)) {
                $this->__addFile($phar, new \SplFileInfo($vendor_path.$_loader));
            } else {
                $this->_logs[] = sprintf('!! - Loader file "%s" not found and not added!', $_loader);
            }
        }

        // global binary
        $this->__addBin($phar);

        // Stubs
        $phar->setStub($this->__getStub());

        $phar->stopBuffering();

        $this->__addFile($phar, new \SplFileInfo($this->root_dir.'/LICENSE'), false);

        unset($phar);
        
        return $this->_logs;
    }

    private function __addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR, '', $file->getRealPath());

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->__stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }

        $this->_logs[] = sprintf('Adding file "%s" (length %d)', $path, strlen($content));
        $phar->addFromString($path, $content);
    }

    private function __addBin($phar, $binary = 'bin/markdown-extended')
    {
        $content = file_get_contents($this->root_dir.'/bin/markdown-extended');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);

        $this->_logs[] = sprintf('Adding file "%s" (length %d)', $binary, strlen($content));
        $phar->addFromString($binary, $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function __stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    private function __getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('mde.phar');
define('MDE_PHAR', true);
require 'phar://mde.phar/bin/markdown-extended';

__HALT_COMPILER();
EOF;
    }
}

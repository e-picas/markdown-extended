<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedDev;

use Symfony\Component\Finder\Finder;
use MarkdownExtended\Util\Helper;

/**
 * The Compiler class compiles the whole markdown into a phar
 *
 * Largely inspired from Composer (<http://github.com/composer/composer>)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Compiler
{
    public $root_dir;

    protected $_logs = [];

    public const PHAR_FILE = 'markdown-extended.phar';

    public const PHAR_NAME = 'mde.phar';

    public function getDefaultFinder()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->exclude('bin')
            ->exclude('build')
            ->exclude('dev')
            ->exclude('doc')
            ->exclude('demo')
            ->exclude('phpdoc')
            ->exclude('tests')
            ->exclude('tmp')
            ->exclude('vendor')
            ->exclude('src/MarkdownExtendedDev')
            ->notName('CONTRIBUTING.md')
            ->notName('.sami.php')
            ->in($this->root_dir)
        ;
        return $finder;
    }

    public function compile($pharFile = self::PHAR_FILE, $root_dir = null)
    {
        if (is_null($root_dir)) {
            $this->root_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
        } else {
            $this->root_dir = rtrim($root_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, self::PHAR_NAME);
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
            ->name('*.tpl')
            ->name('LICENSE')
        ;
        foreach ($finder as $file) {
            $this->__addFile($phar, $file, false);
        }

        // global binary
        $this->__addBin($phar);

        // add the __stub
        $phar->setStub($this->__getStub());

        $phar->stopBuffering();
        unset($phar);
        return $this->_logs;
    }

    private function __addFile($phar, $file, $strip = true)
    {
        $path = str_replace($this->root_dir, '', $file->getRealPath());

        $content = Helper::readFile($file);
        if ($strip) {
            $content = $this->__stripWhitespace($content);
        }

        $this->_logs[] = sprintf('Adding file "%s" (length %d)', $path, strlen($content));
        $phar->addFromString($path, $content);
        return $this;
    }

    private function __addBin($phar, $binary = 'bin/markdown-extended')
    {
        $content = Helper::readFile($this->root_dir.'/bin/markdown-extended');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);

        $this->_logs[] = sprintf('Adding binary file "%s" from source (length %d)', $binary, strlen($content));
        $phar->addFromString($binary, $content);
        return $this;
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
            } elseif (in_array($token[0], [T_COMMENT, T_DOC_COMMENT])) {
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
        $name = self::PHAR_NAME;
        return <<<EOF
#!/usr/bin/env php
<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Phar::mapPhar('{$name}');
define('MDE_PHAR', true);
require 'phar://{$name}/bin/markdown-extended';

__HALT_COMPILER();
EOF;
    }
}

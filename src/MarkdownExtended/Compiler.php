<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended;

use \Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles the whole markdown into a phar
 *
 * Taken "as is" from Composer (<http://github.com/composer/composer>)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class Compiler
{

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

    /**
     * Compiles app into a single phar file
     *
     * @param  string            $pharFile The full path to the file to create
     */
    public function compile($pharFile = 'markdown-extended.phar', $root_dir = null)
    {
        if (is_null($root_dir)) {
            $root_dir = __DIR__.'/../..';
        }
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'mde.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('tests')
            ->exclude('vendor')
            ->exclude('demo')
            ->notName('Compiler.php')
            ->notName('SplClassLoader.php')
            ->in($root_dir)
        ;
        foreach ($finder as $file) {
            $this->__addFile($phar, $file);
        }

        $finder = new Finder();
        $finder->files()
            ->name('*.json')
            ->name('*.ini')
            ->in($root_dir)
        ;
        foreach ($finder as $file) {
            $this->__addFile($phar, $file, false);
        }

        $vendor_path = $root_dir.'/vendor/';
        foreach (self::$phar_loaders as $_loader) {
            if (file_exists($vendor_path.$_loader)) {
                $this->__addFile($phar, new \SplFileInfo($vendor_path.$_loader));
            } else {
                $this->_logs[] = sprintf('!! - Loader file "%s" not found and not added!', $_loader);
            }
        }
        $this->__addBin($phar);

        // Stubs
        $phar->setStub($this->__getStub());

        $phar->stopBuffering();

        $this->__addFile($phar, new \SplFileInfo($root_dir.'/LICENSE'), false);

        unset($phar);
        
        return $this->_logs;
    }

    private function __addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());

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
        $content = file_get_contents(__DIR__.'/../../bin/markdown-extended');
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
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */

Phar::mapPhar('mde.phar');

require 'phar://mde.phar/bin/markdown-extended';

__HALT_COMPILER();
EOF;
    }
}

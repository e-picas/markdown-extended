<?php
// https://gist.github.com/piwi/0e7f1560365162134725

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * SplClassLoader implementation that implements the technical interoperability
 * standards for PHP 5.3 namespaces and class names.
 *
 * http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1
 *
 * Example which loads classes for the Doctrine Common package in the
 * Doctrine\Common namespace:
 *
 *     $classLoader = new SplClassLoader('Doctrine\Common', '/path/to/doctrine');
 *     $classLoader->register();
 *
 * The result is something like:
 *
 *     class_exists('Doctrine\Common\ExistingClass');      // => true
 *     class_exists('Doctrine\Common\NonExistingClass');   // => E_COMPILE_ERROR
 *                                                         // as the `require(file)` will fail
 *
 * Same example as above with graceful failures if a class does not exist:
 *
 *     $classLoader = new SplClassLoader(
 *          'Doctrine\Common', '/path/to/doctrine', SplClassLoader::FAIL_GRACEFULLY);
 *     $classLoader->register();
 *
 * The result is something like:
 *
 *     class_exists('Doctrine\Common\ExistingClass');      // => true
 *     class_exists('Doctrine\Common\NonExistingClass');   // => false
 *
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Pierre Cassat <me@e-piwi.fr>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Roman S. Borschel <roman@code-factory.org>
 * @author Matthew Weier O'Phinney <matthew@zend.com>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Fabien Potencier <fabien.potencier@symfony-project.org>
 */
class SplClassLoader
{

    const FAIL_GRACEFULLY           = 2;
    const FAIL_WITH_ERROR           = 1;

    private $_fileExtension         = '.php';
    private $_namespaceSeparator    = '\\';
    private $_namespace;
    private $_includePath;
    private $_failureFlag;

    /**
     * Creates a new `SplClassLoader` that loads classes of the
     * specified namespace with a hand on failure management.
     *
     * @param   string  $ns             The namespace to use.
     * @param   string  $includePath    The path to search namespace's classes
     * @param   int     $failure        Defines loading failure behavior
     */
    public function __construct($ns = null, $includePath = null, $failure = self::FAIL_WITH_ERROR)
    {
        $this
            ->setNamespace($ns)
            ->setIncludePath($includePath)
            ->setFailureFlag($failure);
    }

    /**
     * Sets the namespace of this class loader.
     *
     * @param   string  $ns The namespace to use.
     * @return  $this
     */
    public function setNamespace($ns)
    {
        $this->_namespace = $ns;
        return $this;
    }

    /**
     * Gets the namespace of this class loader.
     *
     * @return string $namespace
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Sets the namespace separator used by classes in the namespace of this class loader.
     *
     * @param   string  $sep    The separator to use.
     * @return  $this
     */
    public function setNamespaceSeparator($sep)
    {
        $this->_namespaceSeparator = $sep;
        return $this;
    }

    /**
     * Gets the namespace separator used by classes in the namespace of this class loader.
     *
     * @return string $namespaceSeparator
     */
    public function getNamespaceSeparator()
    {
        return $this->_namespaceSeparator;
    }

    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     *
     * @param   string  $includePath
     * @return  $this
     * @throws  \InvalidArgumentException if the path does not exist or is not a directory
     */
    public function setIncludePath($includePath)
    {
        if (!file_exists($includePath)) {
            throw new \InvalidArgumentException(
                sprintf('Path "%s" declared as namespace\'s "%s" include path does not exist!', $includePath, $this->getNamespace())
            );
        }
        if (!is_dir($includePath)) {
            throw new \InvalidArgumentException(
                sprintf('Path "%s" declared as namespace\'s "%s" include path is not a directory!', $includePath, $this->getNamespace())
            );
        }
        $this->_includePath = realpath($includePath);
        return $this;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string $includePath
     */
    public function getIncludePath()
    {
        return $this->_includePath;
    }

    /**
     * Sets the file extension of class files in the namespace of this class loader.
     *
     * @param   string $fileExtension The file extension to use, with or without leading dot
     * @return  $this
     */
    public function setFileExtension($fileExtension)
    {
        $this->_fileExtension = ($fileExtension{0}=='.' ? '' : '.') . $fileExtension;
        return $this;
    }

    /**
     * Gets the file extension of class files in the namespace of this class loader.
     *
     * @return string $fileExtension
     */
    public function getFileExtension()
    {
        return $this->_fileExtension;
    }

    /**
     * Sets the failure flag of the namespace loader.
     *
     * @param   int     $failureFlag    Must be one of the class `FAIL_` constants
     * @return  $this
     * @throws  \InvalidArgumentException if defined flag is not correct
     */
    public function setFailureFlag($failureFlag)
    {
        if (!in_array($failureFlag, array(self::FAIL_GRACEFULLY, self::FAIL_WITH_ERROR))) {
            throw new \InvalidArgumentException(
                'The failure flag of an SplClassLoader instance must be one of class "FAIL_" constants!'
            );
        }
        $this->_failureFlag = $failureFlag;
        return $this;
    }

    /**
     * Gets the failure flag of the namespace loader.
     *
     * @return int $failureFlag
     */
    public function getFailureFlag()
    {
        return $this->_failureFlag;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     *
     * @return void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Constructs the file path of a class.
     *
     * @param   string  $className The name of the class to load.
     * @return  null|string
     */
    public function resolveFileName($className)
    {
        if (
            null === $this->getNamespace() ||
            $this->getNamespace().$this->getNamespaceSeparator() === substr($className, 0, strlen($this->getNamespace().$this->getNamespaceSeparator()))
        ) {
            $fileName   = '';
            $namespace  = '';
            if (false !== ($lastNsPos = strripos($className, $this->getNamespaceSeparator()))) {
                $namespace  = substr($className, 0, $lastNsPos);
                $className  = substr($className, $lastNsPos + 1);
                $fileName   = str_replace($this->getNamespaceSeparator(), DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName  .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->getFileExtension();
            return ($this->getIncludePath() !== null ? $this->getIncludePath() . DIRECTORY_SEPARATOR : '') . $fileName;
        }
        return null;
    }

    /**
     * Checks if the given class file exists.
     *
     * @param   string  $classFile File path
     * @return  boolean
     */
    public function classFileExists($classFile)
    {
        if ( file_exists($classFile) ) {
            return true;
        }
        foreach (explode(PATH_SEPARATOR,get_include_path()) as $path) {
            if ( file_exists($path) && file_exists($path.DIRECTORY_SEPARATOR.$classFile) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Loads the given class or interface.
     *
     * @param   string  $className The name of the class to load.
     * @return  void
     */
    public function loadClass($className)
    {
        if ($filePath = $this->resolveFileName($className)) {
            if ($this->getFailureFlag() & self::FAIL_GRACEFULLY) {
                if ($this->classFileExists($filePath)) {
                    require_once $filePath;
                }
            } else {
                require_once $filePath;
            }
        }
    }

}

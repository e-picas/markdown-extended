<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Console;

use \MarkdownExtended\Util\Registry;
use \MarkdownExtended\Util\Helper;

/**
 * A class to manage command line options based on a set of definitions
 */
class UserInput
{

    const NEGATE_SUFFIX             = 'no-';
    public static $NEGATE_VAL       = '_negate';
    public static $NEGATE_INFO      = 'This option can be negated by "--no-%s".';

    /**
     * Use this when concerned option does NOT have any argument (i.e. for flags)
     */
    const ARG_NULL      = 1;

    /**
     * Use this when concerned option can accept an argument but this is not required (i.e. a default value is defined)
     *
     * The optional argument MUST be written separated to the option by an equal sign.
     */
    const ARG_OPTIONAL  = 2;

    /**
     * Use this when concerned option REQUIRES an argument
     *
     * The equal sign between the option and its argument is not required.
     */
    const ARG_REQUIRED  = 4;

    /**
     * Use this to define a boolean option (i.e. 1 or 0)
     *
     * This is the default for option with no argument (i.e. `self::ARG_NULL`)
     */
    const TYPE_BOOL     = 1;

    /**
     * Use this to define an option as a string
     */
    const TYPE_STRING   = 2;

    /**
     * Use this do define an option as a file path
     *
     * An error will be thrown if the option only have this type and its value
     * can not be found in file system.
     */
    const TYPE_PATH     = 4;

    /**
     * Use this to define an option as a list item
     *
     * Using this type, you are REQUIRED to define a `list` argument in
     * the option's definition.
     */
    const TYPE_LISTITEM = 8;

    protected $options;
    protected $options_indexed;

    public function __construct(array $definitions)
    {
        $this->options          = new Registry;
        $this->options_indexed  = array();

        $counter = 0;
        foreach ($definitions as $name=>$item) {
            $option = new UserOption($item, $name);
            $this->options->set($counter, $option);
            $this->options_indexed[$name] = $counter;
            if ($option->has('shortcut')) {
                $this->options_indexed[$option->get('shortcut')] = $counter;
            }
            $counter++;
        }
    }

    /**
     * Gets an option object by name or shortcut
     *
     * @param $name
     *
     * @return \MarkdownExtended\Console\UserOption
     */
    public function getOption($name)
    {
        $name = rtrim($name, ':');
        return array_key_exists($name, $this->options_indexed) ?
            $this->options->get($this->options_indexed[$name]) : null;
    }

    /**
     * Gets the array of options indexed by their names
     *
     * @return array
     */
    public function getIndexedOptions()
    {
        $options = array();
        foreach ($this->options->getAll() as $item) {
            $options[$item->get('name')] = $item;
        }
        return $options;
    }

    /**
     * Gets an array of a value of each option, indexed by their names
     *
     * @param string $name
     *
     * @return array
     */
    public function getFilteredOptions($name)
    {
        $data = array();
        foreach ($this->getIndexedOptions() as $i=>$item) {
            $data[$i] = $item->get($name);
        }
        return $data;
    }

    /**
     * Gets an automatic information of a set of options
     *
     * @return array
     */
    public function getOptionsInfo()
    {
        $info = array();
        foreach ($this->options->getAll() as $name=>$item) {
            list($_index, $_data) = $item->getInfo();
            $info[$_index] = $_data;
        }
        return $info;
    }

    /**
     * Gets an automatic synopsis of a set of options
     *
     * @return array
     */
    public function getOptionsSynopsis()
    {
        $info = array();
        foreach ($this->options->getAll() as $name=>$item) {
            $info[] = $item->getSynopsis();
        }
        return $info;
    }

    /**
     * Parse the command line user options based on CLI options definitions
     *
     * @return  object
     */
    public function parseOptions()
    {
        // extract from definitions
        $short_options      = $this->getFilteredOptions('short_option');
        $long_options       = $this->getFilteredOptions('long_option');
        $default_options    = $this->getFilteredOptions('_default');

        // treat CLI options
        $options = getopt(join('', array_values($short_options)), $long_options);

        foreach ($options as $var=>$val) {
            if ($option = $this->getOption($var)) {
                if ($option->get('name') !== $var) {
                    $options[$option->get('name')] = $val;
                    unset($options[$var]);
                }
                $options[$option->get('name')] = $option->validateUserValue($val);
            }
        }

        // extract remaining options
        $argv = self::getSanitizedUserInput();
        array_shift($argv);
        foreach ($argv as $i=>$arg) {
            if (array_key_exists(trim($arg, '-'), $options) || in_array($arg, $options, true)) {
                unset($argv[$i]);
            } elseif (
                preg_match('/^[-]+([a-zA-Z]+)$/', $arg, $matches) ||
                preg_match('/^[-]+([a-zA-Z]+)=(.*)/', $arg, $matches)
            ) {
                foreach (str_split($matches[1]) as $letter) {
                    if ($option = $this->getOption($letter)) {
                        $arg = str_replace($letter, '', $arg);
                    }
                }
                if (strlen($arg)===1 || count($matches)>1) {
                    unset($argv[$i]);
                } else {
                    $argv[$i] = $arg;
                }
            } elseif (substr(trim($arg, '-'), 0, strlen(self::NEGATE_SUFFIX)) === self::NEGATE_SUFFIX) {
                $index = substr(trim($arg, '-'), strlen(self::NEGATE_SUFFIX));
                if ($option = $this->getOption($index)) {
                    $options[$index] = $option->validateUserValue(self::$NEGATE_VAL);
                    unset($argv[$i]);
                }
            }
        }
        // last run for unknown options
        foreach ($argv as $i=>$arg) {
            if (substr($arg, 0, 1) === '-') {
                throw new \InvalidArgumentException(
                    sprintf('Unknown option "%s"', trim($arg, '-'))
                );
            }
        }

/*/
        echo Helper::debug(array_values($short_options), 'short options', false);
        echo Helper::debug(array_values($long_options), 'long options', false);
        echo Helper::debug($default_options, 'defaults', false);
        echo Helper::debug($options, 'input user options', false);
        echo Helper::debug($argv, 'remaining arguments', false);
        echo Helper::debug(array_merge($default_options, $options), 'final full options', false);
//*/

        // standard object to return
        $obj            = new \StdClass();
        $obj->options   = array_merge($default_options, $options);
        $obj->remain    = $argv;
        $obj->original  = self::getSanitizedUserInput();
        return $obj;
    }

    /**
     * Gets the input array of options and arguments
     *
     * @return array
     */
    public static function getSanitizedUserInput()
    {
        return array_map(function($item) {
            return filter_var($item, FILTER_UNSAFE_RAW);
        }, $_SERVER['argv']);
    }

}

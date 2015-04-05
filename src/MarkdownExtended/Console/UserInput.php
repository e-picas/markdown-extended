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

use \MarkdownExtended\Util\Helper;

class UserInput
{

    const NEGATE_SUFFIX             = 'no-';
    protected static $NEGATE_VAL    = '_negate';
    protected static $NEGATE_INFO   = 'This option can be prohibited by "--no-%s".';

    const ARG_NULL      = 1;
    const ARG_OPTIONAL  = 2;
    const ARG_REQUIRED  = 4;

    const TYPE_BOOL     = 1;
    const TYPE_STRING   = 2;
    const TYPE_PATH     = 4;
    const TYPE_LISTITEM = 8;

    public static function prepareOptionDefinition(array $item, $name)
    {
        $item['name'] = $name;

        if (!isset($item['argument'])) {
            throw new \InvalidArgumentException(
                sprintf('Option "%s" must define if its argument is required, optional or null.', $name)
            );
        }

        $arg_suffix = '';
        switch ($item['argument']) {
            case self::ARG_OPTIONAL: $arg_suffix = '::'; break;
            case self::ARG_REQUIRED: $arg_suffix = ':'; break;
        }
        $item['long_option']    = $name . $arg_suffix;
        $item['short_option']   = !empty($item['shortcut']) ? $item['shortcut'] . $arg_suffix : null;

        if (!isset($item['type'])) {
            if ($item['argument'] === self::ARG_NULL) {
                $item['type'] = self::TYPE_BOOL;
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Option "%s" must define its type', $name)
                );
            }
        }

        if (!isset($item['default'])) {
            if ($item['type'] === self::TYPE_BOOL) {
                $item['default'] = false;
            }
        }

        if ($item['argument'] === self::ARG_OPTIONAL) {
            $item['_default'] = false;
        } else {
            $item['_default'] = isset($item['default']) ? $item['default'] : null;
        }

        if (!isset($item['negate'])) {
            $item['negate'] = false;
        }

        if (($item['type'] & self::TYPE_LISTITEM) && !isset($item['list'])) {
            throw new \InvalidArgumentException(
                sprintf('Option "%s" must define its list of available values', $name)
            );
        }

        return $item;
    }

    public static function validateOptionValue($value, $definition)
    {
        // treat negation first
        if ($value === self::$NEGATE_VAL && $definition['negate'] === true) {
            return false;
        }

        // be sure to have a value when required
        if ($definition['argument'] === self::ARG_REQUIRED && empty($value)) {
            throw new \InvalidArgumentException(
                sprintf('Option "%s" requires a value', $definition['name'])
            );
        }

        // inverse default `getopt()` return when no argument (which is `false`)
        if (($definition['type'] & self::TYPE_BOOL) && $value===false) {
            return true;
        }

        // return the default value if not boolean and just `false`
        if (!($definition['type'] & self::TYPE_BOOL) && $value===false) {
            return $definition['default'];
        }

        // validate a file path if needed
        if (($definition['type'] & self::TYPE_PATH) && !file_exists($value)) {
            throw new \InvalidArgumentException(
                sprintf('Option "%s" must be a valid file path', $definition['name'])
            );
        }

        // validate a list item if needed
        if (($definition['type'] & self::TYPE_LISTITEM) && !in_array($value, $definition['list'], true)) {
            throw new \InvalidArgumentException(
                sprintf('Option "%s" must be a value in "%s" (got "%s")',
                    $definition['name'], implode('", "', $definition['list']), $value)
            );
        }

        return $value;
    }

    /**
     * Parse the command line user options based on CLI options definitions
     *
     * @param   array $definitions
     * @return  object
     */
    public static function parseOptions(array $definitions)
    {
        // be sure to have fully qualified definitions
        foreach ($definitions as $name=>$item) {
            $definitions[$name] = self::prepareOptionDefinition($item, $name);
        }

        // extract from definitions
        $short_options = array_filter(array_map(function($item) {
            return $item['short_option'];
        }, $definitions));
        $long_options = array_filter(array_map(function($item) {
            return $item['long_option'];
        }, $definitions));
        $default_options = array_map(function($item) {
            return $item['_default'];
        }, $definitions);
        $options_stack = array_flip(array_map(function($item) {
            return isset($item['shortcut']) ? $item['shortcut'] : $item['name'];
        }, $definitions));

        // treat CLI options
        $options = getopt(join('', array_values($short_options)), $long_options);

/*/
        echo Helper::debug(array_values($short_options), 'short options', false);
        echo Helper::debug(array_values($long_options), 'long options', false);
        echo Helper::debug($default_options, 'defaults', false);
        echo Helper::debug($options_stack, 'options stack', false);
//*/

        foreach ($options as $var=>$val) {
            $index = array_key_exists($var, $options_stack) ? $options_stack[$var] : $var;
            if ($index !== $var) {
                $options[$index] = $val;
                unset($options[$var]);
            }
            if (array_key_exists($index, $definitions)) {
                $options[$index] = self::validateOptionValue($val, $definitions[$index]);
            }
        }

        // extract remaining options
        $argv = $_SERVER['argv'];
        array_shift($argv);
        foreach ($argv as $i=>$arg) {
            if (array_key_exists(trim($arg, '-'), $options) || in_array($arg, $options, true)) {
                unset($argv[$i]);
            } elseif (
                preg_match('/^-([a-zA-Z]+)$/', $arg, $matches) ||
                preg_match('/^-([a-zA-Z]+)=(.*)/', $arg, $matches)
            ) {
                foreach (str_split($matches[1]) as $letter) {
                    if (array_key_exists($letter, $options_stack)) {
                        $arg = str_replace($letter, '', $arg);
                    }
                }
                if (strlen($arg)===1 || count($matches)>1) {
                    unset($argv[$i]);
                } else {
                    $argv[$i] = $arg;
                }
            } elseif (substr(trim($arg, '-'), 0, strlen(self::NEGATE_SUFFIX)) == self::NEGATE_SUFFIX) {
                $index = substr(trim($arg, '-'), strlen(self::NEGATE_SUFFIX));
                if (array_key_exists($index, $definitions)) {
                    $options[$index] = self::validateOptionValue(self::$NEGATE_VAL, $definitions[$index]);
                    unset($argv[$i]);
                }
            }
        }
        // last run for unknown options
        foreach ($argv as $i=>$arg) {
            if (substr($arg, 0, 1)=='-') {
                throw new \InvalidArgumentException(
                    sprintf('Unknown option "%s"', trim($arg, '-'))
                );
            }
        }

        // standard object to return
        $obj            = new \StdClass();
        $obj->options   = array_merge($default_options, $options);
        $obj->remain    = $argv;
        $obj->original  = $_SERVER['argv'];
        return $obj;
    }

    public static function getOptionsInfo(array $definitions)
    {
        $info = array();

        foreach ($definitions as $name=>$item) {
            $item = self::prepareOptionDefinition($item, $name);
            $index = '';
            if (!empty($item['shortcut'])) {
                $index .= '-' . $item['shortcut'] . ', ';
            }
            $index .= '--' . $item['name'] . self::getOptionArgumentString($item);
            if ($item['negate'] === true) {
                $info[$index] = array(
                    $item['description'],
                    sprintf(self::$NEGATE_INFO, $name)
                );
            } else {
                $info[$index] = $item['description'];
            }
        }

        return $info;
    }

    public static function getOptionsSynopsis(array $definitions)
    {
        $info = array();

        foreach ($definitions as $name=>$item) {
            $item = self::prepareOptionDefinition($item, $name);
            $str = '[--' . $item['name'];
            if (!empty($item['shortcut'])) {
                $str .= '|-' . $item['shortcut'];
            }
            $str .= self::getOptionArgumentString($item) . ']';
            $info[] = $str;
        }

        return $info;
    }

    protected static function getOptionArgumentString($item)
    {
        $str = '';
        if (self::ARG_NULL !== $item['argument']) {
            $str .= self::ARG_OPTIONAL == $item['argument'] ?
                ' (=' : ' =';
            if ($item['type'] & self::TYPE_PATH) {
                $str .= 'PATH';
            } elseif ($item['type'] & self::TYPE_STRING) {
                $str .= 'STRING';
            } else {
                $str .= 'true/false';
            }
            $str .= self::ARG_OPTIONAL == $item['argument'] ?
                ')' : '';
        }
        return $str;
    }

}

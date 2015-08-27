<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Console;

use MarkdownExtended\Exception\InvalidArgumentException;
use MarkdownExtended\Exception\UnexpectedValueException;

/**
 * A class to manage one command line option based on a definition
 *
 * The `$item` definition array may follow these rules:
 *
 * -    'description' (required) is the option description used in help string
 * -    'argument' (required) is the argument behavior: null, optional or required
 *      (use the `ARG_` class constants)
 * -    'type' (optional) is the argument type (use the `TYPE_` class constants) ;
 *      it defaults to the boolean type if the argument is null and is required otherwise ;
 *      you can use multiple types using bitwise notation: `TYPE_1 | TYPE_2`
 * -    'default' (optional) is the default value of the option ; it defaults to
 *      `false` if the argument is optional or the type is boolean and `null` otherwise
 * -    'default_arg' (optional) is the default argument value used when the argument
 *      is optional and the option is used ; it defaults to 'default'
 * -    'shortcut' (optional) is the one-letter option shortcut
 * -    'negate' (optional) allows the option to be negated using option `--no-OPTION` ;
 *      this will reset the default value of the option to `false` when the negate option
 *      is used
 * -    'list' (required for the LISTITEM type) defines the allowed list of option values
 *
 */
class UserOption
{
    /**
     * @var \MarkdownExtended\Util\Registry
     */
    protected $data;

    /**
     * Organize and cleanup an option definition
     *
     * @param array $item
     * @param string $name
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException if the option does not define a description,
     *              an argument type, a type and its argument is required or the option's argument is a list item but no list is defined
     */
    public function __construct(array $item, $name)
    {
        $this->data = $item;
        $this->set('name', $name);

        if (!$this->has('description')) {
            throw new UnexpectedValueException(
                sprintf('Option "%s" must define a description.', $this->get('name'))
            );
        }
        if (!$this->has('argument')) {
            throw new UnexpectedValueException(
                sprintf('Option "%s" must define if its argument is required, optional or null.', $this->get('name'))
            );
        }
        if (!$this->has('type')) {
            if ($this->get('argument') === UserInput::ARG_NULL) {
                $this->set('type', UserInput::TYPE_BOOL);
            } else {
                throw new UnexpectedValueException(
                    sprintf('Option "%s" must define its type', $this->get('name'))
                );
            }
        }
        if (($this->get('type') & UserInput::TYPE_LISTITEM) && !$this->has('list')) {
            throw new UnexpectedValueException(
                sprintf('Option "%s" must define the list of available values', $this->get('name'))
            );
        }

        $this->rebuildDefinition();
    }

    /**
     * Rebuild options elements
     */
    protected function rebuildDefinition()
    {
        $arg_suffix = '';
        switch ($this->get('argument')) {
            case UserInput::ARG_OPTIONAL: $arg_suffix = '::'; break;
            case UserInput::ARG_REQUIRED: $arg_suffix = ':'; break;
        }
        $this->set('long_option',  $this->get('name') . $arg_suffix);
        $this->set('short_option', $this->has('shortcut') ? $this->get('shortcut') . $arg_suffix : null);

        if (!$this->has('default')) {
            if ($this->get('type') === UserInput::TYPE_BOOL) {
                $this->set('default', false);
            }
        }

        $this->set('_default', $this->has('default') ? $this->get('default') : (
            $this->get('argument') === UserInput::ARG_OPTIONAL ? false : null
        ));

        $this->set('_default_arg', $this->has('default_arg') ?
            $this->get('default_arg') : $this->get('_default')
        );

        if (!$this->has('negate')) {
            $this->set('negate', false);
        }
    }

    /**
     * Validate a user CLI option value
     *
     * @param   string  $value
     *
     * @return  bool
     *
     * @throws \MarkdownExtended\Exception\InvalidArgumentException if the option requires an argument and does not received one
     *              and if the option's type validation fails
     */
    public function validateUserValue($value)
    {
        // treat negation first
        if ($value === UserInput::$NEGATE_VAL && $this->get('negate') === true) {
            return false;
        }

        // be sure to have a value when required
        if ($this->get('argument') === UserInput::ARG_REQUIRED && empty($value)) {
            throw new InvalidArgumentException(
                sprintf('Option "%s" requires a value', $this->get('name'))
            );
        }

        // return the default_arg value if not boolean and just `false`
        if ($this->get('type') > UserInput::TYPE_BOOL && $value === false) {
            return $this->get('_default_arg');
        }

        // inverse default `getopt()` return when no argument (which is `false`)
        if (($this->get('type') & UserInput::TYPE_BOOL) && $value === false) {
            return true;
        }

        // validate a file path if needed
        if (($this->get('type') & UserInput::TYPE_PATH) && !file_exists($value)) {
            throw new InvalidArgumentException(
                sprintf('Option "%s" must be a valid file path', $this->get('name'))
            );
        }

        // validate a list item if needed
        if (
            ($this->get('type') & UserInput::TYPE_LISTITEM) &&
            !in_array($value, $this->get('list'), true)
        ) {
            throw new InvalidArgumentException(
                sprintf('Option "%s" must be a value in "%s" (got "%s")',
                    $this->get('name'), implode('", "', $this->get('list')), $value)
            );
        }

        return $value;
    }

    /**
     * Gets an automatic information about the option
     *
     * @return array
     */
    public function getInfo()
    {
        $index = '';
        if ($this->has('shortcut')) {
            $index .= '-' . $this->get('shortcut') . ', ';
        }
        $index .= '--' . $this->get('name') . $this->getArgumentString();

        if ($this->get('negate') === true) {
            $data = array(
                $this->get('description'),
                sprintf(UserInput::$NEGATE_INFO, $this->get('name'))
            );
        } else {
            $data = $this->get('description');
        }

        return array($index, $data);
    }

    /**
     * Gets an automatic synopsis of the option
     *
     * @return array
     */
    public function getSynopsis()
    {
        $str = '[--' . $this->get('name');
        if ($this->has('shortcut')) {
            $str .= '|-' . $this->get('shortcut');
        }
        $str .= $this->getArgumentString();
        if ($this->get('negate') === true) {
            $str .= ' / --' . UserInput::NEGATE_SUFFIX . '-' . $this->get('name');
        }
        $str .= ']';
        return $str;
    }

    /**
     * Gets an option value type as string
     *
     * @return string
     */
    public function getArgumentString()
    {
        $str = '';
        if (UserInput::ARG_NULL !== $this->get('argument')) {
            $str .= UserInput::ARG_OPTIONAL == $this->get('argument') ?
                '[=' : '=';
            if ($this->get('type') & UserInput::TYPE_PATH) {
                $str .= '<path>';
            } elseif ($this->get('type') & UserInput::TYPE_STRING) {
                $str .= '<string>';
            } else {
                $str .= 'true/false';
            }
            $str .= UserInput::ARG_OPTIONAL == $this->get('argument') ?
                ']' : '';
        }
        return $str;
    }

    /**
     * Alias of `$this->data->...`
     *
     * @param $name
     * @return mixed
     */
    public function has($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Alias of `$this->data->...`
     *
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->has($name) ? $this->data[$name] : null;
    }

    /**
     * Alias of `$this->data->...`
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }
}

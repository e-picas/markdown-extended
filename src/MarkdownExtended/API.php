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

use \MarkdownExtended\Exception as MDE_Exception;

/**
 * The API defines all required interfaces
 */
class API
{

    /**
     * @var     string
     */
    const MDE_NAMESPACE = 'MarkdownExtended';
    
    /**
     * @var     string
     */
    const KERNEL_INTERFACE = '\MarkdownExtended\API\KernelInterface';
    
    /**
     * @var     string
     */
    const CONTENT_INTERFACE = '\MarkdownExtended\API\ContentInterface';
    
    /**
     * @var     string
     */
    const OUTPUT_FORMAT_INTERFACE = '\MarkdownExtended\API\OutputFormatInterface';
    
    /**
     * @var     string
     */
    const OUTPUT_FORMAT_HELPER_INTERFACE = '\MarkdownExtended\API\OutputFormatHelperInterface';
    
    /**
     * @var     string
     */
    const PARSER_INTERFACE = '\MarkdownExtended\API\ParserInterface';
    
    /**
     * @var     string
     */
    const TEMPLATER_INTERFACE = '\MarkdownExtended\API\TemplaterInterface';

    /**
     * @var     string
     */
    const GRAMMAR_GAMUT_INTERFACE = '\MarkdownExtended\API\GamutInterface';

    /**
     * Get the current API
     *
     * @return  array
     */
    private static function __getApi()
    {
        return array(
            'kernel'                =>self::KERNEL_INTERFACE,
            'content'               =>self::CONTENT_INTERFACE,
            'output_format'         =>self::OUTPUT_FORMAT_INTERFACE,
            'output_format_helper'  =>self::OUTPUT_FORMAT_HELPER_INTERFACE,
            'parser'                =>self::PARSER_INTERFACE,
            'templater'             =>self::TEMPLATER_INTERFACE,
            'grammar\filter'        =>self::GRAMMAR_GAMUT_INTERFACE,
            'grammar\tool'          =>self::GRAMMAR_GAMUT_INTERFACE,
        );
    }

    /**
     * Validate an API object checking its implemented interfaces
     *
     * @param   string/object   $object
     * @param   string          $type
     * @param   bool            $bool
     * @return  bool
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException
     */
    public static function isValid($object, $type = null, $bool = false)
    {
        $ok         = false;
        $api        = self::__getApi();
        $class_name = self::getAbsoluteClassname(
            is_object($object) ? get_class($object) : $object
        );
        if (!empty($class_name) && class_exists($class_name)) {
            if (is_null($type)) {
                $type = self::getRelativeClassname($class_name);
            }
            $type = strtolower($type);
            if (array_key_exists($type, $api) && !is_null($api[$type])) {
                $api_interface  = $api[$type];
                $interfaces     = class_implements($class_name);
                $ok = (in_array($api_interface, $interfaces) || in_array(trim($api_interface, '\\'), $interfaces));
                if (false===$ok && true!==$bool) {
                    throw new MDE_Exception\UnexpectedValueException(
                        sprintf('Class "%s" must implement API interface "%s"!', $class_name, $api_interface)
                    );
                }
            } elseif (array_key_exists($type, $api) && is_null($api[$type])) {
                $ok = true;
            } elseif (true!==$bool) {
                throw new MDE_Exception\UnexpectedValueException(
                    sprintf('API type "%s" (guessed from class "%s") does not exist!', $type, $class_name)
                );
            }
        }
        return $ok;
    }
    
    /**
     * Get a class name without the current namespace if present
     *
     * @param   string  $class_name
     * @return  string
     */
    public static function getRelativeClassname($class_name)
    {
        if (strstr($class_name, self::MDE_NAMESPACE)) {
            return trim(
                str_replace(self::MDE_NAMESPACE.'\\', '', $class_name)
            , '\\');
        }
        return $class_name;
    }

    /**
     * Get a class name with the current namespace
     *
     * @param   string  $class_name
     * @return  string
     */
    public static function getAbsoluteClassname($class_name)
    {
        if (!strstr($class_name, self::MDE_NAMESPACE)) {
            return '\\'.self::MDE_NAMESPACE.'\\'.$class_name;
        }
        return $class_name;
    }

}

// Endfile

<?php
/**
 * PHP Extended Markdown
 * Copyright (c) 2004-2012 Pierre Cassat
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

/**
 *
 */
class Markdown_Builder_Documentation extends Markdown_Builder
{

	protected $base_path;

	public function __construct( $base_path=null )
	{
		if (empty($base_path) && defined('MARKDOWN_EXTENDED_DIR'))
			$base_path = MARKDOWN_EXTENDED_DIR;

		if (!empty($base_path)) {
			if (@file_exists($base_path) && @is_dir($base_path)) {
				$this->base_path = rtrim($base_path, '/').'/';
			} else {
				throw new InvalidArgumentException(sprintf(
  	  		"Base path for documentation must be an existing directory, <%s> given!", $base_path
		    ));
			}
		}
	}
	
	public function build( $class=null, $method=null )
	{
		Markdown_Extended::load($class);
		$class = new ReflectionClass($class);
		$content = self::analyze($class);
		return self::parseMDE($content);
	}

	public function analyze( $class )
	{
		$class_name = trim($class->getName());
		$class_doc = self::buildClassString( $class );

		// Properties
		$class_properties=array();
		foreach( $class->getProperties() as $property){
			$class_properties[] = self::buildClassPropertyString($property, $class);
		}
		if (!empty($class_properties))
			$class_properties = join("\n", $class_properties);
		else
			$class_properties = '*None*';

		// Constants
		$class_constants=array();
		foreach( $class->getConstants() as $constant_name=>$constant_value){
			$class_constants[] = self::buildConstantString($constant_name, $constant_value);
		}
		if (!empty($class_constants))
			$class_constants = join("\n", $class_constants);
		else
			$class_constants = '*None*';

		// Methods
		$class_methods=array();
		foreach( $class->getMethods() as $method){
			$class_methods[] = self::buildMethodString($method, $class);
		}
		if (!empty($class_methods))
			$class_methods = join("\n", $class_methods);
		else
			$class_methods = '*None*';

		$file_content = <<<EOT
# $class_name

$class_doc

## Constants

$class_constants

## Properties

$class_properties

## Methods

$class_methods

EOT;
		return $file_content;
	}

// -------------------------------
// TEMPLATES
// -------------------------------

	public function buildClassString( $class )
	{
		$class_name = $class->getName();
		$class_file = $class->getFileName();
		$start_line = $class->getStartLine();
		$end_line = $class->getEndLine();

		$class_doc = trim(self::stripCommentTags( $class->getDocComment() ));

		$type_class = array();
		if ($class->isAbstract()) $type_class[] = 'abstract';
		if ($class->isFinal()) $type_class[] = 'final';
		if ($class->isInterface()) $type_class[] = 'interface';
		else $type_class[] = 'class';
		$type_class = join(' ', $type_class);

		$posttype_class = '';
		$parent_class = $class->getParentClass();
		if (!empty($parent_class)) 
			$posttype_class .= 'extends ['.$parent_class->getName().']('
				.self::buildUrl($parent_class->getName()).')';

		$implements = $class->getInterfaceNames();
		if(!empty($implements)) {
			$posttype_class .= ' implements ';
			foreach($implements as $_implt)
				$posttype_class .= ' ['.$_implt.']('.self::buildUrl($_implt).')';
		}
		
		return <<<EOT
> *$type_class* **$class_name** $posttype_class
> (lines $start_line to $end_line - file *$class_file*)

**$class_doc**

EOT;
	}
	
	public function buildMethodString( $method, $class )
	{
		$method_name = trim($method->name);

		$type_method = array();
		if ($method->isAbstract()) $type_method[] = 'abstract';
		if ($method->isConstructor()) $type_method[] = 'constructor';
		if ($method->isDestructor()) $type_method[] = 'destructor';
		if ($method->isFinal()) $type_method[] = 'final';
		if ($method->isPrivate()) $type_method[] = 'private';
		if ($method->isProtected()) $type_method[] = 'protected';
		if ($method->isPublic()) $type_method[] = 'public';
		if ($method->isStatic()) $type_method[] = 'static';
		$type_method = join(' ', $type_method);

		$line_num = $method->getStartLine();

		$method_arguments = array();
		foreach($method->getParameters() as $argument) {
			$method_arguments[] = self::buildParameterString( $argument, $method );
		}
		$method_arguments = join(' , ', $method_arguments);
		
		$method_doc = trim(self::stripCommentTags( $method->getDocComment() ));

		return <<<EOT
### $method_name

> (line $line_num) *$type_method* **$method_name** ( $method_arguments )

$method_doc

EOT;
	}
	
	public function buildClassPropertyString( $property, $class )
	{
		$property_name = $property->getName();

		$type_property = array();
		if ($property->isPrivate()) $type_property[] = 'private';
		if ($property->isProtected()) $type_property[] = 'protected';
		if ($property->isPublic()) $type_property[] = 'public';
		if ($property->isStatic()) $type_property[] = 'static';
		$type_property = join(' ', $type_property);

		if ($property->isPublic())
			$property_value = var_export($property->getValue( $class ), 1);

		$property_doc = trim(self::stripCommentTags( $property->getDocComment() ));

		return <<<EOT
### $property_name

> *$type_property* **\$$property_name** = $property_value

$property_doc

EOT;
	}
	
	public function buildParameterString( $argument, $method )
	{
		if ($argument->isArray()) $arg_type = '(array)';
		else $arg_type = '';

		$arg_str = ($argument->isPassedByReference() ? '&' : '').'$'.$argument->getName();

		if ($argument->isOptional()) $arg_default = '[=*'.var_export($argument->getDefaultValue(),1).'*]';
		else $arg_default = '';

		return <<<EOT
$arg_type **$arg_str** $arg_default
EOT;
	}
	
	public function buildConstantString($constant_name, $constant_value)
	{
		$constant_value = var_export($constant_value, 1);
		return <<<EOT

> **$constant_name** = $constant_value

EOT;
	}

// -------------------------------
// INTERNAL PROCESS
// -------------------------------

	protected function stripCommentTags( $text )
	{
		return preg_replace('/^[ \t]*[\/]?[\*]+[\/]?(.?)/im', "$1", $text);
	}
	
	protected function parseMDE( $text )
	{
		return Markdown($text);
	}

	protected function buildUrl($str)
	{
		$base_uri = preg_replace('/(class=[^=|.]*)/', '', $_SERVER['REQUEST_URI']);
		return $base_uri.(strpos($base_uri, '?') ? '&' : '?').'class='.$str;
	}
	
}

// Endfile
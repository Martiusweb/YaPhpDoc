<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A decorator wraps a token instance and adds or overrides methods provided
 * by the token class adding it extra features, in order to ease the output
 * of the token instance in the generated documentation.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Abstract
{
	/**
	 * Decorated token
	 * @var YaPhpDoc_Token_Abstract
	 */
	private $_token;
	
	/**
	 * Creates the decorator object.
	 * 
	 * @param YaPhpDoc_Token_Abstract $token
	 */
	public function __construct(YaPhpDoc_Token_Abstract $token)
	{
		$this->_token = $token;
	}
	
	/**
	 * Returns the token $token wrapped into a decorator of type $type.
	 * 
	 * A decorator type is the namespace of the decorator, for instance :
	 * "YaPhpDoc_Generator_Decorator_Html" or "MyExtension_Decorator_Latex".
	 * 
	 * @param string $type
	 * @param YaPhpDoc_Token_Abstract $token
	 * @return YaPhpDoc_Generator_Decorator_Abstract
	 */
	public static function getDecorator($type, YaPhpDoc_Token_Abstract $token)
	{
		# classname prefix is the decorator type
		$classname = $type.'_'.$token->getTokenType();

		# Tries to find a specific class according to the token type or
		# choose default one.
		if(!YaPhpDoc_Tool_Loader::classExists($classname))
		{
			$classname = $type.'_Token';
		}
		
		$decorator = new $classname($token);
		
		return $decorator;
	}
	
	/**
	 * The magic allows to call methods from the token which are not
	 * overriden by the decorator.
	 * 
	 * The method is just called, there is no check performed.
	 * 
	 * @param string $func
	 * @param array $args
	 * @return mixed
	 */
	public function __call($func, $args)
	{
		return call_user_func_array(array($this->_token, $func), $args);	
	}
}
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
 * It performs a transparent binding between the token methods and its
 * modifier, which is a static class containing logic of overriden methods for
 * an output format.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Token
{
	/**
	 * Decorated token
	 * @var YaPhpDoc_Token_Abstract
	 */
	protected $_token;
	
	/**
	 * Decorator type
	 * @var string
	 */
	private $_type;

	/**
	 * The decorator implementation.
	 * @var YaPhpDoc_Generator_Decorator_Modifier
	 */
	protected $_modifier;
	
	/**
	 * Creates the decorator object.
	 * 
	 * @param YaPhpDoc_Token_Abstract $token
	 */
	public function __construct($type, YaPhpDoc_Token_Abstract $token)
	{
		$this->_type = $type;
		$this->_token = $token;
		
		$this->_loadModifier();
	}
	
	/**
	 * Returns the token $token wrapped into a decorator of type $type.
	 * 
	 * A decorator type is the namespace of the decorator, for instance :
	 * "YaPhpDoc_Generator_Decorator_Html" or "MyExtension_Decorator_Latex".
	 * 
	 * @param string $type
	 * @param YaPhpDoc_Token_Abstract|YaPhpDoc_Token_Structure_Collection_Abstract $token
	 * @return YaPhpDoc_Generator_Decorator_Token
	 */
	public static function getDecorator($type, $token)
	{
		if($token instanceof YaPhpDoc_Token_Structure_Abstract)
			$classname = 'YaPhpDoc_Generator_Decorator_Structure';
		elseif($token instanceof YaPhpDoc_Token_Abstract)
			$classname = 'YaPhpDoc_Generator_Decorator_Token';
		elseif($token instanceof YaPhpDoc_Token_Structure_Collection_Abstract)
			$classname = 'YaPhpDoc_Generator_Decorator_Collection';
		
		$decorator = new $classname($type, $token);
		
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
		if(is_callable(array($this->_modifier, $func)))
		{
			// Adds the token as first parameter
			array_unshift($args, $this->_token);
			return call_user_func_array(array($this->_modifier, $func), $args);
		}
		
		return call_user_func_array(array($this->_token, $func), $args);	
	}
	
	/**
	 * Decorates a token or an array.
	 * 
	 * @param YaPhpDoc_Token_Abstract|YaPhpDoc_Token_Abstract[] $children
	 * @return YaPhpDoc_Generator_Decorator_Token|YaPhpDoc_Generator_Decorator_Token[]
	 */
	protected function _decorate($children)
	{
		if(is_array($children))
		{
			$decorated = array();
			foreach($children as $k => $v)
				$decorated[$k] = $this->_decorate($v);
		}
		else
		{
			$decorated = YaPhpDoc_Generator_Decorator_Token
				::getDecorator($this->_type, $children);
		}
		
		return $decorated;
	}
	
	/**
	 * Tries to find a modifier for the token, the modifier can be
	 * {DecoratorType}_{TokenType} or {DecoratorType}_Token for generic one
	 * if the specific does not exists.
	 * 
	 * @return void
	 */
	private function _loadModifier()
	{
		$modifier = $this->_type.'_'.ucfirst($this->_token->getTokenType());		
		if(!YaPhpDoc_Tool_Loader::classExists($modifier))
		{
			$modifier = $this->_type.'_Token';
		}
		$this->_modifier = $modifier;
	}
}
<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a class attribute (or parameter), static or not.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_ClassAttribute extends YaPhpDoc_Token_Var
{
	/**
	 * Visibility (YaPhpDoc_Token_Class::V_PUBLIC|V_PROTECTED|V_PRIVATE)
	 * @var int
	 */
	protected $_visibility = YaPhpDoc_Token_Class::V_PUBLIC;
	
	/**
	 * Scope (true if static).
	 * @var bool
	 */
	protected $_static = false;
	
	/**
	 * Parameter constructor.
	 * 
	 * @param YaPhpDoc_Token_Class $parent
	 * @return YaPhpDoc_Token_Param
	 */
	public function __construct(YaPhpDoc_Token_Class $parent)
	{
		parent::__construct($parent, 'classAttribute', 'unknown');
	}
	
	/**
	 * Parses the class Attribute.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_ClassAttribute
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		parent::parse($tokensIterator);
		
		$this->_static = $this->getParser()->isStatic();
		
		if($this->getParser()->isPrivate())
			$this->_visibility = YaPhpDoc_Token_Class::V_PRIVATE;
		elseif($this->getParser()->isProtected())
			$this->_visibility = YaPhpDoc_Token_Class::V_PROTECTED;
		else
			$this->_visibility = YaPhpDoc_Token_Class::V_PUBLIC;
		
		return $this;
	}
	
	/**
	 * Set the parameter name.
	 * 
	 * @param string $name
	 * @return YaPhpDoc_Token_Param
	 */
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	
	/**
	 * Returns the attribute visibility (value is a constant
	 * YaPhpDoc_Token_Class::V_PUBLIC|V_PROTECTED|V_PRIVATE).
	 * 
	 * @return int
	 */	
	public function getVisibility()
	{
		return $this->_visibility;
	}
	
	/**
	 * Returns the attribute scope (true if static).
	 * @return bool
	 */
	public function isStatic()
	{
		return $this->_static;
	}
}
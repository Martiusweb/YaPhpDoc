<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a method.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Method extends YaPhpDoc_Token_Function
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
	 * True if the method is abstract.
	 * @var bool
	 */
	protected $_abstract = false;
	
	/**
	 * True if the method is final.
	 * @var bool
	 */
	protected $_final = false;
	
	/**
	 * Constructor of a class or object method.
	 * @param YaPhpDoc_Token_Class $parent
	 */
	public function __construct(YaPhpDoc_Token_Class $parent)
	{
		parent::__construct($parent);
		$this->_tokenType = 'method';
	}
	
	/**
	 * Parses a method.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Method
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		parent::parse($tokensIterator);
		
		# Adds scope and visibility attributes.
		$this->_static = $this->getParser()->isStatic();
		
		if($this->getParser()->isPrivate())
			$this->_visibility = YaPhpDoc_Token_Class::V_PRIVATE;
		elseif($this->getParser()->isProtected())
			$this->_visibility = YaPhpDoc_Token_Class::V_PROTECTED;
		else
			$this->_visibility = YaPhpDoc_Token_Class::V_PUBLIC;
		
		$this->_abstract = $this->getParser()->isAbstract();
		$this->_final = $this->getParser()->isFinal();
		
		return $this;
	}
	
	/**
	 * Returns the method visibility (value is a constant
	 * YaPhpDoc_Token_Class::V_PUBLIC|V_PROTECTED|V_PRIVATE).
	 * 
	 * @return int
	 */	
	public function getVisibility()
	{
		return $this->_visibility;
	}
	
	/**
	 * Returns the method scope (true if static).
	 * @return bool
	 */
	public function isStatic()
	{
		return $this->_static;
	}
	
	/**
	 * Returns true if the method is abstract.
	 * @return bool
	 */
	public function isAbstract()
	{
		return $this->_abstract;
	}
	
	/**
	 * Returns true if the method is final.
	 * @return bool
	 */
	public function isFinal()
	{
		return $this->_final;
	}
}
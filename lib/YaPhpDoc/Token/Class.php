<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a class.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Class extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * True if the class is abstract.
	 * @var bool
	 */
	protected $_abstract = false;
	
	/**
	 * True if the class is final.
	 * @var bool
	 */
	protected $_final = false;
	
	/**
	 * Parent class.
	 * @var string|NULL
	 */
	protected $_extends;
	
	/**
	 * Implemented interfaces.
	 * @var array
	 */
	protected $_implements = array();
	
	/**
	 * Function constructor.
	 * 
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct('unknown', T_CLASS, $parent);
	}
	
	/**
	 * Parse the function.
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_Class
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		# Determine context
		if($this->getParser()->isAbstract())
			$this->_abstract = true;
		elseif($this->getParser()->isFinal())
			$this->_final = true;
		
		// TODO parse class
		
		return $this;
	}
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : 
	 * 
	 * @todo tags for class
	 *  
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Class
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		return $this;
	}
	
	/**
	 * Returns true if the class is abstract.
	 * @return bool
	 */
	public function isAbstract()
	{
		return $this->_abstract;
	}
	
	/**
	 * Returns true if the class is final.
	 * @return bool
	 */
	public function isFinal()
	{
		return $this->_final;
	}
	
	/**
	 * Returns the parent class (if exists).
	 * @return string|NULL
	 */
	public function getParentClass()
	{
		return $this->_extends;
	}
	
	/**
	 * Returns an array of interfaces implemented by the class.
	 * 
	 * @return string[]
	 */
	public function getInterfaces()
	{
		return $this->_implements;
	}
}
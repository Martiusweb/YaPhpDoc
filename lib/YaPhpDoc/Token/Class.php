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
	 * Constant representing a public class member. 
	 * @var int
	 */
	const V_PUBLIC = 100;
	
	/**
	 * Constant representing a protected class member. 
	 * @var int
	 */
	const V_PROTECTED = 101;
	
	/**
	 * Constant representing a private class member. 
	 * @var int
	 */
	const V_PRIVATE = 102;
	
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
	 * Class attributes.
	 * @var YaPhpDoc_Token_ClassAttribute[]
	 */
	protected $_attributes = array();
	
	/**
	 * Methods.
	 * @var YaPhpDoc_Token_Method[]
	 */
	protected $_methods = array();
	
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
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Class
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		if($tokensIterator->current()->isClassOrInterface())
		{
			$tokensIterator->next();
			
			# Determine context
			if($this->getParser()->isAbstract())
				$this->_abstract = true;
			elseif($this->getParser()->isFinal())
				$this->_final = true;
			
			$in_class_definition = true;
			$in_extends_definition = false;
			$in_implements_definition = false;
			$nested_level = 0;
			$docblock = null;
			while($tokensIterator->valid())
			{
				$token = $tokensIterator->current();
				/* @var $token YaPhpDoc_Tokenizer_Token */
				
				if($token->isWhitespace())
				{
					$tokensIterator->next();
					continue;
				}
				
				if($token->isDocBlock())
				{
					$docblock = new YaPhpDoc_Token_Docblock($this);
					$docblock->parse($tokensIterator);
				}
				elseif($in_class_definition)
				{
					if($in_extends_definition)
					{
						if($token->isConstantString())
						{
							$this->_extends = $token->getStringContent();
							$in_extends_definition = false;
						}
					}
					elseif($token->isExtends())
						$in_extends_definition = true;
						
					if($in_implements_definition)
					{
						if($token->isConstantString())
						{
							$this->_implements[] = $token->getStringContent();
						}
					}
					elseif($token->isImplements())
						$in_implements_definition = true;
					
					if(!$in_extends_definition && !$in_implements_definition)
					{
						if($token->isConstantString()) # Class definition
							$this->_name = $token->getStringContent();
					}
					
					if($token->getType() == '{')
					{
						$in_extends_definition = false;
						$in_implements_definition = false;
						$in_class_definition = false;
						++$nested_level;
					}
				}
				elseif($nested_level > 0)
				{
					if($token->getType() == '}')
					{
						--$nested_level;
						if($nested_level == 0)
							break;
					}
					elseif($token->isPublic())
					{
						$this->getParser()->setPublic();
					}
					elseif($token->isProtected())
					{
						$this->getParser()->setProtected();
					}
					elseif($token->isPrivate())
					{
						$this->getParser()->setPrivate();
					}
					elseif($token->isAbstract())
					{
						$this->getParser()->setAbstract();
					}
					elseif($token->isStatic())
					{
						$this->getParser()->setStatic();
					}
					elseif($token->isFinal())
					{
						$this->getParser()->setFinal();
					}
					elseif($token->isFunction())
					{
						$method = new YaPhpDoc_Token_Method($this);
						$method->parse($tokensIterator);
						if($docblock !== null)
						{
							$method->setStandardTags($docblock);
							$docblock = null;
						}
						$this->_methods[] = $method;
						$this->addChild($method);
					}
					elseif($token->isVariable())
					{
						$attribute = new YaPhpDoc_Token_ClassAttribute($this);
						$attribute->parse($tokensIterator);
						if($docblock !== null)
						{
							$attribute->setStandardTags($docblock);
							$docblock = null;
						}
						$this->_attributes[] = $attribute;
						$this->addChild($attribute);
					}
				}
				$tokensIterator->next();
			}
		}
		
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
	
	/**
	 * Returns the class attributes (subset of token children).
	 * @return YaPhpDoc_Token_ClassAttribute[]
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	/**
	 * Returns the class methods (subset of token children).
	 * @return YaPhpDoc_Token_Method[]
	 */
	public function getMethods()
	{
		return $this->_methods;
	}
}
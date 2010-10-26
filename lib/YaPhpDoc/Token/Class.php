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
	 * True if we are parsing interface implemented by this class.
	 * @var bool
	 */
	protected $_parseImplements = false;
	
	/**
	 * True if we are parsing the parent class.
	 * @var bool
	 */
	protected $_parseExtends = false;
	
	/**
	 * Nested block level.
	 * @var int
	 */
	protected $_nested = 0;
	
	/**
	 * Function constructor.
	 * 
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct($parent, 'class', 'unknown');
	}
	
	/**
	 * @see YaPhpDoc/Token/YaPhpDoc_Token_Abstract#_parseContext($token)
	 */
	protected function _parseContext(YaPhpDoc_Tokenizer_Token $token)
	{
		if($token->isPublic())
		{
			$this->getParser()->setPublic();
			return true;
		}
		elseif($token->isProtected())
		{
			$this->getParser()->setProtected();
			return true;
		}
		elseif($token->isPrivate())
		{
			$this->getParser()->setPrivate();
			return true;
		}
		elseif($token->isStatic())
		{
			$this->getParser()->setStatic();
			return true;
		}
		
		parent::_parseContext($token);
		return false;
	}
	
	/**
	 * Parse the function.
	 * 
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Class
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		echo "begin class parse\n";
		if($this->getParser()->isAbstract())
				$this->_abstract = true;
		elseif($this->getParser()->isFinal())
			$this->_final = true;
		
		$this->_addTokenCallback('constantString', array($this, '_parseName'));
		$this->_addTokenCallback('extends', array($this, '_parseExtends'));
		$this->_addTokenCallback('implements', array($this, '_parseImplements'));
		$this->_addTokenCallback('{', array($this, '_parseLeftBrace'));
		$this->_addTokenCallback('}', array($this, '_parseRightBrace'));
		
		parent::parse($tokensIterator);
		echo "end class parse\n";
		return $this;
	}
	
	/**
	 * Parses the class name, implemented interfaces and parent class.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseName(YaPhpDoc_Tokenizer_Token $token)
	{
		if($this->_parseExtends)
		{
			$this->_extends = $token->getStringContent();
			# Multiple inheritance does not exists in php !
			$this->_parseExtends = false;
		}
		elseif($this->_parseImplements)
		{
			$this->_implements[] = $token->getStringContent();
		}
		else
			$this->_name = $token->getStringContent();
	}
	
	/**
	 * Find the parent class.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseExtends(YaPhpDoc_Tokenizer_Token $token)
	{
		$this->_parseImplements = false;
		$this->_parseExtends = true;
	}

	/**
	 * Find implemented interfaces.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseImplements(YaPhpDoc_Tokenizer_Token $token)
	{
		$this->_parseImplements = true;
		$this->_parseExtends = false;
	}
	
	/**
	 * Behavior when a "{" is found.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseLeftBrace(YaPhpDoc_Tokenizer_Token $token)
	{
		++$this->_nested;
		$this->_addParsableTokenType('function', 'method');
		$this->_addParsableTokenType('variable', 'classAttribute');
	}
	
	/**
	 * Behavior when a "}" is found.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseRightBrace(YaPhpDoc_Tokenizer_Token $token)
	{
		--$this->_nested;
		if($this->_nested == 0)
			$this->_breakParsing();
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
		return $this->getChildrenByType('classAttribute');
	}
}
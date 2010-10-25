<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a variable-like token (global or constant).
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Var extends YaPhpDoc_Token_Abstract
{
	/**
	 * Value of the variable
	 * @var string|NULL
	 */
	protected $_value;
	
	/**
	 * Type of the variable
	 * @var string|NULL
	 */
	protected $_type;
	
	/**
	 * Parses the variable.
	 * 
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Var
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$this->_addTokenCallback(';', array($this, '_breakParsing'));
		$this->_addTokensIteratorCallback('=', array($this, '_parseValue'));
		$this->_addTokenCallback('constantString', array($this, '_parseType'));
		$this->_addTokenCallback('variable', array($this, '_parseVariable'));
		
		parent::parse($tokensIterator);
		
		return $this;
	}
	
	/**
	 * Parse the variable type.
	 * @return void
	 */
	protected function _parseType(YaPhpDoc_Tokenizer_Token $token)
	{
		$this->setType($token->getStringContent());
	}
	
	/**
	 * Parse the variable name.
	 * @return void
	 */
	protected function _parseVariable(YaPhpDoc_Tokenizer_Token $token)
	{
		$this->setName($token->getContent());
	}
	
	/**
	 * Finds the variable value.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return void
	 */
	protected function _parseValue(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$token = $tokensIterator->current();
		if($token->isConstantValue())
				$this->_value = $token->getConstantContent();
		elseif($token->isArray())
		{
			$array = YaPhpDoc_Token_Abstract::getToken($this->getParser(), 'array', $this, $this->getName());
			$array->parse($tokensIterator);
			$this->_value = $array->getArrayString();
		}
	}
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : var (variable type and desc if available)
	 * 
	 * @see YaPhpDoc_Token_Abstract#setStandardTags
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Global
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		$this->appendDescription($docblock->getContent());
		if($var = $docblock->getTags('var'))
		{
			/* @var $var YaPhpDoc_Tag_Var */
			$this->_type = $var->getType();
			$this->appendDescription($var->getComment());
		}
		
		parent::setStandardTags($docblock);
	}
	
	/**
	 * Returns the default value of the parameter.
	 * @return string|NULL
	 */
	public function getDefaultValue()
	{
		return $this->_value;
	}
	
	/**
	 * Set the parameter type.
	 * 
	 * @param string $type
	 * @return YaPhpDoc_Token_Param
	 */
	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}
	
	/**
	 * Returns the variable type if known. Else, returns null.
	 * @return string|nULL
	 */
	public function getType()
	{
		return $this->_type;
	}
}
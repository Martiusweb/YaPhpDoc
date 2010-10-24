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
		$in_default_value = false;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
			
			if($token->getType() == '=')
			{
				$this->_parseValue($tokensIterator);
			}
			elseif($token->isConstantString())
			{
				$this->setType($token->getStringContent());
			}
			elseif($token->isVariable())
			{
				$this->setName($token->getContent());
			}
			elseif($token->getType() == ';')
			{
				break;
			}
			
			$tokensIterator->next();
		}
		
		return $this;
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
			$array = new YaPhpDoc_Token_Array($this->getName(), $this);
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
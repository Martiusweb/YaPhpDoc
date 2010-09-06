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
			if($in_default_value)
			{
				if($token->isConstantValue())
				{
					$this->setDefaultValue($token->getConstantContent());
					$in_default_value = false;
				}
				elseif($token->isArray())
				{
					$array = new YaPhpDoc_Token_Array($this->getName(), $this);
					$array->parse($tokensIterator);
					$this->setDefaultValue($array->getArrayString());
					unset($array);
					$in_default_value = false;
				}
			}
			elseif($token->isConstantString())
			{
				$this->setType($token->getStringContent());
			}
			elseif($token->isVariable())
			{
				$this->setName($token->getContent());
			}
			elseif($token->getType() == '=')
			{
				$in_default_value = true;
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
	 * Set standard tags if available from given dockblock.
	 * Tags are : var (variable type and desc if available)
	 * 
	 * @see YaPhpDoc_Token_Abstract#setStandardTags
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Global
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		$this->setDescription($docblock->getContent());
		if($var = $docblock->getTags('var'))
		{
			$var = array_pop($var);
			if(preg_match('`(.*?)(?:\s|$)(.*)`', $var, $matches))
			{
				$this->_type = $matches[1];
				if(!empty($matches[2]))
					$this->setDescription($matches[2]);
			}
		}
		
		parent::setStandardTags($docblock);
	}
	
	/**
	 * Returns the variable value if known. Else, returns null.
	 * @return string|NULL
	 */
	public function getValue()
	{
		return $this->_value;
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
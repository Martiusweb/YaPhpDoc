<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a constant.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Const extends YaPhpDoc_Token_Var
{
	/**
	 * Constant constructor.
	 * 
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct('unknown', T_CONST, $parent);
	}
	
	/**
	 * Parses the constant.
	 * 
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Const
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		if($tokensIterator->current()->isConst())
		{
			$is_const = false;
			$is_define = false;
			$is_name = false;
			$is_value = false;
			while($tokensIterator->valid())
			{
				$token = $tokensIterator->current();
				/* @var $token YaPhpDoc_Tokenizer_Token */
				
				# Skip whitespaces
				if($token->isWhitespace())
				{
					$tokensIterator->next();
					continue;
				}
				
				if($token->getTypeId() == T_CONST)
				{
					$is_const = true;
					$is_name = true;
				}
				elseif($token->getTypeId() == T_STRING
					&& $token->getContent() == 'define')
				{
					$is_define = true;
				}
				elseif($is_define)
				{
					if($token->getType() == '(')
						$is_name = true;
					elseif($is_name)
					{
						if($token->isConstantString())
							$this->_name = $token->getStringContent();
						elseif($token->getType() == ',')
						{
							$is_name = false;
							$is_value = true;
						}
					}
					elseif($is_value)
					{
						if($token->isConstantValue())
							$this->_value = $token->getConstantContent();
						elseif($token->isArray())
						{
							$array = new YaPhpDoc_Token_Array($this->getName(), $this);
							$array->parse($tokensIterator);
							$this->_value = $array->getArrayString();
							unset($array);
						}
						elseif($token->getType() == ')')
						{
							$is_value = false;
							$is_define = false;
						}
					}
				}
				elseif($is_const)
				{
					if($is_name)
					{
						if($token->isConstantString())
						{
							$this->_name = $token->getStringContent();
						}
						elseif($token->getType() == '=')
						{
							$is_name = false;
							$is_value = true;
						}
					}
					elseif($is_value)
					{
						if($token->isConstantValue())
							$this->_value = $token->getConstantContent();
						$is_value = false;
						$is_const = false;
					}
				}
				elseif($token->getType() == ';')
					break;
				
				$tokensIterator->next();
			}	
		}
		return $this;
	}
}
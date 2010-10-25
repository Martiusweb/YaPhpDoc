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
		parent::__construct($parent, 'const', 'unknown');
	}
	
	/**
	 * Returns the constant value as a string.
	 * @return string
	 */
	public function getValue()
	{
		return $this->_value;
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
					$this->_parseConst($tokensIterator);
				}
				elseif($token->getTypeId() == T_STRING
					&& $token->getContent() == 'define')
				{
					$this->_parseDefine($tokensIterator);
				}
				elseif($token->getType() == ';')
					break;

				$tokensIterator->next();
			}
		}
		return $this;
	}

	/**
	 * Parses a constant defined with define() primitive.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return void
	 */
	protected function _parseDefine(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$is_name = false;
		$is_value = false;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
			
			if($is_name)
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
				if(!$this->_parseValue($tokensIterator)
					&& $token->getType() == ')')
					break;
			}
			elseif($token->getType() == '(')
				$is_name = true;
			
			$tokensIterator->next();
		}
	}
	
	/**
	 * Parse a constant defined with const.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return void
	 */
	protected function _parseConst(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$is_name = true;
		$is_value = false;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
			if($is_value)
			{
				if($token->isConstantValue())
					$this->_value = $token->getConstantContent();
				break;
			} 
			elseif($token->isConstantString())
			{
				$this->_name = $token->getStringContent();
			}
			elseif($token->getType() == '=')
			{
				$is_value = true;
			}
			
			$tokensIterator->next();
		}
	}
}
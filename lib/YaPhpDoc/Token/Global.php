<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Global token
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Global extends YaPhpDoc_Token_Var
{
	/**
	 * Constructor of the global token.
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct('unknown', T_GLOBAL, $parent);
	}
	
	/**
	 * Parses the global : try to find it's name.
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_Global
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		if($tokensIterator->current()->isGlobal())
		{
			$tokensIterator->next();
			
			$in_global_index = false;
			$in_global_value = false;
			while($tokensIterator->valid())
			{
				$token = $tokensIterator->current();
				/* @var $token YaPhpDoc_Tokenizer_Token */
				if($token->isWhitespace())
				{
					$tokensIterator->next();
					continue;
				}
				
				if($token->getType() == '[')
				{
					$in_global_index = true;
				}
				elseif($in_global_index)
				{
					if($token->isConstantValue())
						$this->_name = $token->getConstantContent();
					elseif($token->getType() == ']')
						$in_global_index = false;
				}
				elseif($token->isVariable())
				{
					$this->_name = substr($token->getContent(), 1);
				}
				elseif($token->getType == '=')
				{
					$in_global_value = true;
				}
				elseif($in_global_value && $token->isConstantValue())
				{
					$this->_value = $token->getConstantContent();
				}
				elseif($token->getType() == ';')
				{
					break;
				}
				$tokensIterator->next();
			}
		}
		
		return $this;
	}
}
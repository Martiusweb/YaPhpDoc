<?php

class YaPhpDoc_Token_Array extends YaPhpDoc_Token_Abstract
{
	/**
	 * Full declaration of the array.
	 * @var string
	 */
	protected $_array_string;
	
	/**
	 * Array Constructor.
	 * @param YaPhpDoc_Token_Abstract $parent
	 * @param string $name
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent, $name)
	{
		parent::__construct($parent, 'array', $name);
	}
	
	/**
	 * Parse the array declaration.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Array
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		if($tokensIterator->current()->isArray())
		{
			$representation = '';
			$in_def = false;
			$nested = 0;
			while($tokensIterator->valid())
			{
				$token = $tokensIterator->current();
				/* @var $token YaPhpDoc_Tokenizer_Token */
				
				if($in_def)
				{
					if($token->getType() == '(')
					{
						++$nested;
						$representation .= '(';
					}
					elseif($token->isWhitespace())
					{
						$representation .= ' ';
					}
					elseif($token->getType() == ')')
					{
						--$nested;
						$representation .= ')';
						if($nested == 0)
						{
							$in_def = false;
							break;
						}
					}
					else
					{
						$content = $token->getContent();
						if(empty($content))
							$representation .= $token->getType();
						else
							$representation .= $token->getContent();
						unset($content);
					}
					
				}
				elseif($token->isArray())
				{
					$representation .= $token->getContent();
				}
				elseif($token->getType() == '(')
				{
					$in_def = true;
					$representation .= '(';
					++$nested;
				}
				$tokensIterator->next();
			}
		}
		$this->_array_string = $representation;
		return $this;
	}
	
	/**
	 * Return full declaration of the array.
	 * @return string
	 */
	public function getArrayString()
	{
		return $this->_array_string;
	}
}
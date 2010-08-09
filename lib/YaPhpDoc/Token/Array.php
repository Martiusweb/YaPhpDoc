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
	 * @param string $name
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct($name, YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct($name, T_ARRAY, $parent);
	}
	
	/**
	 * Parse the array declaration.
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_Array
	 */
	public function parse(ArrayIterator $tokensIterator)
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
				
				if($token->isArray())
				{
					$representation .= $token->getContent();
				}
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
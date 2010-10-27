<?php

class YaPhpDoc_Token_Array extends YaPhpDoc_Token_Abstract
{
	/**
	 * Nested bracket "(" level.
	 * @var int
	 */
	protected $_nested = 0;
	
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
		$this->_addTokenCallback('array', array($this, '_parseRepresentation'));
		$this->_addTokenCallback('(', array($this, '_parseLeftBracket'));
		
		parent::parse($tokensIterator);
		return $this;
	}
	
	/**
	 * Parse a token and add it to the array representation.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseRepresentation(YaPhpDoc_Tokenizer_Token $token)
	{
		if($token->isWhitespace())
			$this->_array_string .= ' ';
		else
		{
			$content = $token->getContent();
			if(!empty($content))
				$this->_array_string .= $content;
			else
				$this->_array_string .= $token->getType();			
		}
	}
	
	/**
	 * Parse a left bracket "(".
	 * @return void
	 */
	protected function _parseLeftBracket()
	{
		++$this->_nested;
		if($this->_nested == 1)
		{
			$this->_addTokenCallback(')', array($this, '_parseRightBracket'));
			$this->_addTokenCallback('*', array($this, '_parseRepresentation'));
		}
		$this->_array_string .= '(';
	}
	
	/**
	 * Parse a right bracket ")".
	 * @return void
	 */
	protected function _parseRightBracket()
	{
		$this->_array_string .= ')';
		--$this->_nested;
		
		if($this->_nested == 0)
			$this->_breakParsing();
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
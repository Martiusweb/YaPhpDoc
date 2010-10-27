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
		parent::__construct($parent, 'global', 'unknown');
	}
	
	/**
	 * Parses the global : try to find its name.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Global
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$this->_addTokenCallback('[', array($this, '_parseLeftSquareBrace'));
		$this->_addTokenCallback('variable', array($this, '_parseName'));
		$this->_addTokenCallback(';', array($this, '_breakParsing'));
		$this->_addTokensIteratorCallback('=', array($this, '_parseValue'));
		
		parent::parse($tokensIterator);
		
		return $this;
	}
	
	/**
	 * Parses a left square brace '[' (in global name definition).
	 * @return void
	 */
	protected function _parseLeftSquareBrace()
	{
		$this->_addTokenCallback('constantString', array($this, '_parseName'));
		$this->_addTokenCallback('constantValue', array($this, '_parseName'));
		$this->_addTokenCallback(']', array($this, '_parseRightSquareBrace'));
	}
	
	/**
	 * The name may have been found.
	 * @return void
	 */
	protected function _parseRightSquareBrace()
	{
		$this->_removeTokenCallback('constantString');
		$this->_removeTokenCallback('constantValue');
		$this->_removeTokenCallback('[');
		$this->_removeTokenCallback(']');
	}
	
	/**
	 * Parses the global name (can't find an evaluated name with concatenation.
	 * @todo Manage concatenation, maybe.
	 * @return void
	 */
	protected function _parseName(YaPhpDoc_Tokenizer_Token $token)
	{
		if($token->isVariable())
			$this->_name = substr($token->getContent(), 1);
		else
		$this->_name = $token->getConstantContent();
	}
}
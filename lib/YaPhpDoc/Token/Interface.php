<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent an interface, which is (for our use) a different kind of class
 * than "standard ones".
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Interface extends YaPhpDoc_Token_Class
{
	/**
	 * Parses the interface.
	 * Forces abstract to true and final to false.
	 * 
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Interface
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		parent::parse($tokensIterator);
		
		$this->_abstract = true;
		$this->_final = false;
		
		return $this;
	}
}
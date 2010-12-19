<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/../../util.php';
require_once 'YaPhpDoc/Tokenizer/Iterator.php';
require_once 'YaPhpDoc/Tokenizer/Token.php';
/**
 * Stub class for YaPhpDoc_Tokenizer
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tokenizer
{
	public function __construct($file_content)
	{
		
	}
	
	/**
	 * returns an iterator with one dummy token.
	 * 
	 * @return YaPhpDoc_Tokenizer_Iterator
	 */
	public function getIterator()
	{
		return new YaPhpDoc_Tokenizer_Iterator(array(
			new YaPhpDoc_Tokenizer_Token(YaPhpDoc_Tokenizer_Token::T_MISC)
		));
	}
}
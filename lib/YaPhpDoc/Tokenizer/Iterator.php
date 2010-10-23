<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Specializes an ArrayIterator into an iterator of tokens.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tokenizer_Iterator extends ArrayIterator
{
	/**
	 * Returns current offset.
	 * @return YaPhpDoc_Tokenizer_Token
	 */
	public function current()
	{
		$current = parent::current();
		// DEBUG Display currently parsed token
//		var_dump($current->getType().' : '.$current->getContent());
		return $current;
	}
}
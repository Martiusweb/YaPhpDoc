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
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function current()
	{
		$current = parent::current();
		// TODO debug only
//		var_dump($current->getType().' : '.$current->getContent());
		return $current;
	}
}
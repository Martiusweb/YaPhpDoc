<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Return tag precises the type of the value returned by a function.
 * 
 * You can specify the value returned is an array of known type with
 * type[] (ie YaPhpDoc_Tag_Return[]).
 * 
 * Syntax : @return type|type[]
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Return extends YaPhpDoc_Tag_Abstract
{
	/**
	 * Return tag constructor.
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
		$this->_setMultipleUsage(false);
		parent::__construct($tagline);
	}
	
	// TODO array syntax for @return
}
<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a function.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Function extends YaPhpDoc_Token_Abstract
{
	/**
	 * Function constructor.
	 * 
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct('unknown', T_FUNCTION, $parent);
	}
	
	/**
	 * Parse the function.
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_Function
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		// TODO parse function
		return $this;
	}
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : 
	 * @todo choose tags
	 *  
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Function
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		// TODO standard tags for function
		return $this;
	}
}
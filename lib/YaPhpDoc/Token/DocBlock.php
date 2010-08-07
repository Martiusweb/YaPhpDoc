<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A docBlock is a comment block starting with "/**" and that may contain
 * @tags (documentation elements on a line starting with "@").
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_DocBlock extends YaPhpDoc_Token_Abstract
{
	/**
	 * Parse a docblock : find content and @tags.
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_DocBlock
	 */
	public function parse()
	{
		// TODO DocBlock parser
		return $this;
	}
}
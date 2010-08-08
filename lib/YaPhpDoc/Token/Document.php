<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent the document token, which is in fact a root node for the
 * documented elements.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Document extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * Root constructor.
	 * Parent of the root is null.
	 */
	public function __construct()
	{
		$this->_name = 'Root';
		$this->_tokenType = YaPhpDoc_Token_Abstract::ROOT;
	}
}
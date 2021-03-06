<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Since Tag.
 * 
 * Syntax: @since Version
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Since extends YaPhpDoc_Tag_Abstract_Abstract
{
	/**
	 * Since tag constructor.
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
		$this->_setMultipleUsage(false);
		parent::__construct($tagline);
	}
}
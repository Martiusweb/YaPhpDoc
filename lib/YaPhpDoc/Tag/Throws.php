<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Throw tag, allow to specify exceptions that can be thrown inside the
 * function.
 * 
 * Sytax: @throws TypeOfException
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Throws extends YaPhpDoc_Tag_Abstract_Abstract
{
	/**
	 * Throws tag constructor.
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
		$this->_setMultipleUsage(false);
		parent::__construct($tagline);
	}
	
	// TODO resolve exception (@throws tag)
}
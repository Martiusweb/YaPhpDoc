<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent any token (file, package, namespace, class, function, etc) the
 * program can parse and document.
 * 
 * @author Martin Richard
 */
abstract class YaPhpDoc_Token_Abstract
{
	/**
	 * Name of the token
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Returns the token name
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
}
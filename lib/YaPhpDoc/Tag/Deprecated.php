<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * The deprecated tag informs that the token is considered as deprecated and
 * should not be used anymore in new developments since he may be removed in
 * future versions.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Deprecated extends YaPhpDoc_Tag_Abstract_BooleanType
{
	/**
	 * Returns true if the token is deprecated.
	 * @return bool
	 */
	public function isDeprecated()
	{
		return $this->getFlag();
	}
}
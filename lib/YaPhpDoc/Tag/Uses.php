<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Uses is like the see tag, but focus on PHP functions and methods. You can't
 * use this tag to add an external link.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Uses extends YaPhpDoc_Tag_Abstract
{
	/**
	 * True if the link had been resolved.
	 * @var bool
	 */
	protected $_resolved = false;
	
	/**
	 * True if a link can be gessed from the tag value.
	 * @var bool
	 */
	protected $_linkable;
	
	/**
	 * Label of the link (should be the value of the tag).
	 * @var string
	 */
	protected $_label;
	
	/**
	 * Parses the uses tag.
	 * @param string $tagline
	 * @return YaPhpDoc_Tag_Uses
	 */
	protected function _parse($tagline)
	{
		parent::_parse($tagline);
		// TODO Parse the tagline : use namespace and package separator
		if(preg_match('``', $tagline, $matches))
		{
			
		}
		else
		{
			$this->_label = $tagline;
		}
		return $this;
	}
	
	/**
	 * Try to find the link in the documentation matching the tag value.
	 * 
	 * @return YaPhpDoc_Tag_See
	 */
	protected function _resolve()
	{
		return $this;
	}
}
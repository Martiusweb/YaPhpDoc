<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * The see tag allow to add a link between the token and another token,
 * such a class, function, etc - or a link to an external resource.
 * 
 * Syntax: @see \Path\To\Class#method()|path.to.class#method()|http://external/link.html
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_See extends YaPhpDoc_Tag_Abstract
{
	/**
	 * Parses the see tag.
	 * @param string $tagline
	 * @return YaPhpDOc_Tag_See
	 */
	protected function _parse($tagline)
	{
		// TODO see @ŧag parser
		parent::_parse($tagline);
		return $this;
	}
}
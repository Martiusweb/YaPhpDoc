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
class YaPhpDoc_Tag_See extends YaPhpDoc_Tag_Uses
{
	/**
	 * Link URL.
	 * @var string
	 */
	protected $_link;
	
	/**
	 * Parses the see tag.
	 * @param string $tagline
	 * @return YaPhpDoc_Tag_See
	 */
	protected function _parse($tagline)
	{
		YaPhpDoc_Tag_Abstract_Abstract::_parse($tagline);
		
		// TODO find standard regex or cstring function for URL parsing
		/*
		if(preg_match('``', $tagline, $matches))
		{
			
			$this->_linkable = true;
			$this->_resolved = true;
		}
		else
			parent::_parse($tagline);
		*/
		
		return $this;
	}
	
	/**
	 * Try to find the link in the documentation matching the tag value.
	 * 
	 * @return YaPhpDoc_Tag_See
	 */
	protected function _resolve()
	{
		if(null !== $this->_link)
		{
			
		}
		else
			parent::_resolve();
		
		return $this;
	}
}
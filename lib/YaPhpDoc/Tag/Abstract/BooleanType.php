<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A booleanType tag is an abstract tag type corresponding to tags
 * which can be associated with no value (the tag is self-descriptive
 * about the state of the token).
 * 
 * Syntax: @tag [optional : comment]
 * 
 * @author Martin Richard
 */
abstract class YaPhpDoc_Tag_Abstract_BooleanType extends YaPhpDoc_Tag_Abstract_Abstract
{
	/**
	 * Tag flag (should be true after parsing, because the tag exists !)
	 * @var bool
	 */
	protected $_flag = false;
	
	/**
	 * Tag comment (optional)
	 * @var string
	 */
	protected $_comment;
	
	/**
	 * BooleanType constructor.
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
		$this->_setMultipleUsage(false);
		parent::__construct($tagline);
	}
	
	/**
	 * Parse the boolean value
	 *  
	 * @param string $tagline
	 * @return YaPhpDoc_Tag_Deprecated
	 */
	protected function _parse($tagline)
	{
		parent::_parse($tagline);
		$this->_flag = true;
		
		if(!empty($this->_value))
		{
			$this->_comment = trim($this->_value);
		}
		return $this;
	}
	
	/**
	 * Returns the flag state.
	 * @return bool
	 */
	public function getFlag()
	{
		return $this->_flag;
	}
	
	/**
	 * Returns the comment if exists.
	 * @return string|NULL
	 */
	public function getComment()
	{
		return $this->_comment;
	}
}
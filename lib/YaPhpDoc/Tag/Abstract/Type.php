<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Abstract tag type where the value represents the type of an expression, such
 * as @var or @return.
 * 
 * syntax: @tag type|type[] [Comment]
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Abstract_Type extends YaPhpDoc_Tag_Abstract_Abstract
{
	/**
	 * Type of the tag.
	 * @var string
	 */
	protected $type = 'mixed';
	
	/**
	 * Comment added to the type.
	 * @var string
	 */
	protected $comment = ''; 
	
	/**
	 * Type of an item of the array returned by the function.
	 * @var string
	 */
	protected $_array_of;
	
	/**
	 * Tag constructor.
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
		parent::__construct($tagline);
	}
	
	/**
	 * Parses the a type tag.
	 * 
	 * @param string $line
	 * @return YaPhpDoc_Tag_Return
	 */
	protected function _parse($line)
	{
		parent::_parse($line);
		
		$type = preg_split('#\s#', $this->_value, 1);
		$comment = (isset($type[1]) ? $type[1] : '');
		$type = $type[0];
		
		# is it an array-like syntax ? (= type[])
		if(substr($type, -2) == '[]')
		{
			$this->_array_of = substr($type, 0, -2);
		}
		elseif($type == 'array')
		{
			$this->_array_of = $this->_type;
		}
		
		$this->_type = $type;
		$this->_comment = $comment;
		
		return $this;
	}
	
	/**
	 * Returns the type of the described expression.
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}
	
	/**
	 * Returns the comment added to the type. 
	 * @return string
	 */
	public function getComment()
	{
		return $this->_comment;
	}
	
	/**
	 * Returns the type of the items of the array returned by the function,
	 * mixed if the type is "array" or null if the type is not an array.
	 * @return string|NULL
	 */
	public function arrayOf()
	{
		return $this->_arrayOf;
	}
}
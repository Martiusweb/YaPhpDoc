<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Return tag precises the type of the value returned by a function.
 * 
 * You can specify the value returned is an array of known type with
 * type[] (ie YaPhpDoc_Tag_Return[]).
 * 
 * Syntax : @return type|type[]
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Return extends YaPhpDoc_Tag_Abstract_Abstract
{
	/**
	 * Type of an item of the array returned by the function.
	 * @var string
	 */
	protected $_array_of;
	
	/**
	 * Return tag constructor.
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
		$this->_setMultipleUsage(false);
		parent::__construct($tagline);
	}
	
	/**
	 * Parses the @return tag.
	 * 
	 * @param string $line
	 * @return YaPhpDoc_Tag_Return
	 */
	protected function _parse($line)
	{
		parent::_parse($line);
		
		# is it an array-like syntax ? (= type[])
		if(substr($this->_value, -2) == '[]')
		{
			$this->_array_of = substr($this->_value, 0, -2);
		}
		elseif($this->_value == 'array')
		{
			$this->_array_of = 'mixed';
		}
		
		return $this;
	}
	
	/**
	 * Returns the type of the value returned by the function.
	 * @return string
	 */
	public function getType()
	{
		return $this->_value;
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
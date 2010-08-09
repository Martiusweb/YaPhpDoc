<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a function or method parameter.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Param extends YaPhpDoc_Token_Var
{
	/**
	 * If true, the parameter is optional.
	 * @var bool
	 */
	protected $_option = false;
	
	/**
	 * Parameter constructor.
	 * 
	 * @param YaPhpDoc_Token_Function $parent
	 * @return YaPhpDoc_Token_Param
	 */
	public function __construct(YaPhpDoc_Token_Function $parent)
	{
		parent::__construct('unknown', T_VARIABLE, $parent);
	}
	
	/**
	 * Set the parameter type.
	 * 
	 * @param string $type
	 * @return YaPhpDoc_Token_Param
	 */
	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}
	
	/**
	 * Set the parameter name.
	 * 
	 * @param string $name
	 * @return YaPhpDoc_Token_Param
	 */
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	
	/**
	 * Set the default parameter value.
	 * 
	 * @param string $value
	 * @return YaPhpDoc_Token_Param
	 */
	public function setDefaultValue($value)
	{
		$this->_value = $value;
		return $this;
	}
	
	/**
	 * Returns the default value of the parameter.
	 * @return string
	 */
	public function getDefaultValue()
	{
		return $this->_value;
	}
	
	/**
	 * Set the parameter as optional.
	 * @param string $flag (optional, default to true)
	 * @return YaPhpDoc_Token_Param
	 */
	public function setOptional($flag = true)
	{
		$this->_option = true;
		return $this;
	}
	
	/**
	 * Returns true if the parameter is optional.
	 * @return bool
	 */
	public function isOptional()
	{
		return $this->_option;
	}
}
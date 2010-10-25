<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Description of the parameter of a class or a function.
 * 
 * Syntax: @param type|alt_type|... $var description
 * 
 * You can also use this syntax as last parameter for function allowing any
 * number of parameters :
 * 
 * Alt syntax: @param ...
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tag_Param extends YaPhpDoc_Tag_Abstract_Abstract
{
	/**
	 * Parameter type.
	 * @var string
	 */
	protected $_type;
	
	/**
	 * Parameter name.
	 * @var string
	 */
	protected $_param_name;
	
	/**
	 * Parameter description.
	 * @var string
	 */
	protected $_description;
	
	/**
	 * Parse parameter.
	 * 
	 * @param string $tagline
	 * @return YaPhpDoc_Tag_Param
	 */
	protected function _parse($tagline)
	{
		parent::_parse($tagline);
		$this->_value = trim($this->_value);
		if($this->_value != '...')
		{
			$content = preg_split('`\s+`', $this->_value, 3);
			if(isset($content[0]))
			{
				$this->_type = $content[0];
				if(isset($content[1]))
				{
					$this->_param_name = $content[1];
					
					if(isset($content[2]))
						$this->_description = $content[2];
				}
			}	
		}
		
		return $this;
	}
	
	/**
	 * Returns parameter type.
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;	
	}
	
	/**
	 * Returns parameter name.
	 * 
	 * @return string
	 */
	public function getParamName()
	{
		return $this->_param_name;
	}
	
	/**
	 * Returns parameter description.
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->_description;
	}
	
	/**
	 * Returns true if this parameter tag means that the function allows any
	 * number of parameters.
	 * 
	 * @return bool
	 */
	public function isOpenParameter()
	{
		return $this->_value == '...';
	}
}
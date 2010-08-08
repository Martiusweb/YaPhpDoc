<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a variable-like token (global or constant).
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Var extends YaPhpDoc_Token_Abstract
{
	/**
	 * Value of the variable
	 * @var string|NULL
	 */
	protected $_value;
	
	/**
	 * Type of the variable
	 * @var string|NULL
	 */
	protected $_type;
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : var (variable type and desc if available)
	 * 
	 * @see YaPhpDoc_Token_Abstract#setStandardTags
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Global
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		$this->setDescription($docblock->getContent());
		if($var = $docblock->getTags('var'))
		{
			$var = array_pop($var);
			if(preg_match('`(.*?)(?:\w|$)(.*)`', $var, $matches))
			{
				$this->_type = $matches[1];
				if(!empty($matches[2]))
					$this->setDescription($matches[2]);
			}
		}
		
		parent::setStandardTags($docblock);
	}
	
	/**
	 * Returns the variable value if known. Else, returns null.
	 * @return string|NULL
	 */
	public function getValue()
	{
		return $this->_value;
	}
	
	/**
	 * Returns the variable type if known. Else, returns null.
	 * @return string|nULL
	 */
	public function getType()
	{
		return $this->_type;
	}
}
<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * This decorator extends the generic decorator and overides children and
 * descendants getters methods of structures tokens.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Structure extends
	YaPhpDoc_Generator_Decorator_Token
{
	/**
	 * @see YaPhpDoc_Token_Structure_Abstract#getIterator()
	 * @return Iterator
	 */
	public function getIterator()
	{
		return new YaPhpDoc_Generator_Decorator_Iterator(
			$this->_getType(), $this->_token->getIterator());
	}
	
	// TODO Implements children getters
	 
	/**
	 * @see YaPhpDoc_Token_Structure_Abstract#getChildrenByType()
	 * @param string $type
	 * @return YaPhpDoc_Generator_Decorator_Token[]
	 */
	public function getChildrenByType($type)
	{
		$children = $this->_token->getChildrenByType($type);
		
		# decorates
		return $this->_decorate($children);
	}
}
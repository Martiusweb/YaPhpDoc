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
	YaPhpDoc_Generator_Decorator_Token implements IteratorAggregate
{
	/**
	 * @see YaPhpDoc_Token_Structure_Abstract#getIterator()
	 * @return Iterator
	 */
	public function getIterator()
	{
		return new YaPhpDoc_Generator_Decorator_Iterator(
			$this->getDecoratorType(), $this->_token->getIterator());
	}
	
	// TODO Implements children getters
	 
	/**
	 * @see YaPhpDoc_Token_Structure_Abstract::getChildrenByType()
	 * @param string $type
	 * @return YaPhpDoc_Generator_Decorator_Token[]
	 */
	public function getChildrenByType($type)
	{
		$children = $this->_token->getChildrenByType($type);
		
		# decorates
		return $this->_decorate($children);
	}
	
	/**
	 * @see YaPhpDoc_Token_Structure_Abstract::getDescendantsByType()
	 * @param string $type
	 * @return YaPhpDoc_Generator_Decorator_Token[]
	 */
	public function getDescendantsByType($type)
	{
		$descendants = $this->_token->getDescendantsByType($type);
		
		# decorates
		return $this->_decorate($descendants);
	}
	
	/**
	 * Overrides the magics getters of a structure token, or call modifier.
	 * 
	 * @see YaPhpDoc_Token_Structure_Abstract::__call()
	 * @see YaPhpDoc_Generator_Decorator_Token::__call()
	 * @param string $func
	 * @param array $args
	 * @return mixed
	 */
	public function __call($func, $args)
	{
		try {
			$children = call_user_func_array(array($this->_token, $func), $args);
			
			return $this->_decorate($children);
		}
		catch(YaPhpDoc_Core_Parser_Exception $e)
		{
			# Bad function call, the called function is not a valid getter for
			# the token, we assume we want the decorator __call() behavior.
			return parent::__call($func, $args); 
		}
	}
	
	/**
	 * @see YaPhpDoc_Token_Structure_Abstract::__get()
	 * @param unknown_type $tokenType
	 * @return mixed
	 */
	public function __get($tokenType)
	{
		$children = $this->_token->__get($tokenType);
		
		return $this->_decorate($children);
	}
}
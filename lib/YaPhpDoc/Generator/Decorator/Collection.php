<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * This class is a collection decorator, and decorates tokens retrieved through
 * a collection.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Collection
	extends YaPhpDoc_Generator_Decorator_Token
	implements IteratorAggregate
{
	/**
	 * Constructor of a collection decorator, overrides the parent
	 * __constructor().
	 * 
	 * @param string $type
	 * @param YaPhpDoc_Token_Structure_Collection_Abstract $token
	 */
	public function __construct($type, YaPhpDoc_Token_Structure_Collection_Abstract $token)
	{
		$this->_type = $type;
		$this->_token = $token;
	}
	
	/**
	 * @see YaPhpDoc_Token_Structure_Collection_Abstract::getByName()
	 * @param string $name
	 * @return YaPhpDoc_Generator_Decorator_Token
	 */
	public function getByName($name)
	{
		return $this->_decorate($this->_token->getByName($name));
	}
	
	/**
	 * @see YaPhpDoc_Token_Structure_Collection_Abstract::toArray()
	 * @return YaPhpDoc_Generator_Decorator_Token
	 */
	public function toArray()
	{
		return $this->_decorate($this->_token->toArray());
	}
	
	/**
	 * @see YaPhpDoc_Token_Structure_Collection_Abstract::getIterator()
	 * @return Iterator
	 */
	public function getIterator()
	{
		return new YaPhpDoc_Generator_Decorator_Iterator($this->_type,
			$this->_token->getIterator());
	}
}
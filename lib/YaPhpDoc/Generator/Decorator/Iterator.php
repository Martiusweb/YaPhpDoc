<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * This iterator is used to decorates elements fetched by an iterator.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Iterator implements Iterator
{
	/**
	 * Decorator type.
	 * @var string 
	 */
	protected $_type;
	
	/**
	 * Iterator to decorate.
	 * @var Iterator
	 */
	protected $_iterator;

	/**
	 * Constructs the iterator, proxies $iterator on elements to be decorated
	 * by a decorator of type $type.
	 * 
	 * @param string $type Decorator type.
	 * @param Iterator $iterator
	 */
	public function __construct($type, Iterator $iterator)
	{
		$this->_type = $type;
		$this->_iterator = $iterator;
	}
	
	/**
	 * Rewind the Iterator to the first element.
	 * @return void
	 */
	public function rewind()
	{
		return $this->_iterator->rewind();
	}
	
	/**
	 * Checks if current position is valid.
	 * @return bool
	 */
	public function valid()
	{
		return $this->_iterator->valid();
	}
	
	/**
	 * Move forward to next element.
	 * @return void
	 */
	public function next()
	{
		return $this->_iterator->next();
	}
	
	/**
	 * Return the key of the current element.
	 * @return scalar
	 */
	public function key()
	{
		return $this->_iterator->key();
	}
	
	/**
	 * Return the current element, decorated.
	 * @return mixed
	 */
	public function current()
	{
		$token = $this->_iterator->current();
		
		return YaPhpDoc_Generator_Decorator_Token
			::getDecorator($this->_type, $token);
	}
	
	/**
	 * Returns the decorator type.
	 * @return string
	 */
	public function getDecoractorType()
	{
		return $this->_type;
	}
}
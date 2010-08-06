<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A structure is a token that can have children (file, namespace, class, ...).
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Structure_Abstract extends YaPhpDoc_Token_Abstract
	implements IteratorAggregate, Countable
{
	/**
	 * Children tokens
	 * @var YaPhpDoc_Token_Abstract
	 */
	protected $_children = array();
	
	/**
	 * Returns an iterator on the children tokens.
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->_children);
	}
	
	/**
	 * Returns the number of children.
	 * @return int
	 */
	public function count()
	{
		return count($this->_children);
	}
}
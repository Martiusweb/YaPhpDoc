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
	 * Adds a child to the node.
	 * 
	 * @param YaPhpDoc_Token_Abstract|array $child
	 * @return YaPhpDoc_Token_Structure_Abstract
	 */
	public function addChild($child)
	{
		if(is_array($child))
		{
			foreach($child as $c)
			{
				if($c instanceof YaPhpDoc_Token_Abstract)
					array_push($this->_children, $c);
			}
		}
		elseif($child instanceof YaPhpDoc_Token_Abstract)
			array_push($this->_children, $child);
		
		return $this;
	}
	
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
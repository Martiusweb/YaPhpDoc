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
	 * Use statements
	 * @var array
	 */
	protected $_uses = array();
	
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
		
		if($child instanceof YaPhpDoc_Token_Use)
			array_push($this->_uses, $child);
		
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
	
	/**
	 * Returns an Iterator on Use tokens.
	 * @return ArrayIterator
	 */
	public function getUseIterator()
	{
		return new ArrayIterator($this->_uses);
	}
	
	/**
	 * Returns an array of all the classes.
	 * 
	 * @return YaPhpDoc_Token_Class[]
	 */
	public function getAllClasses()
	{
		$classes = new SplObjectStorage();
		foreach($this->_children as $child)
		{
			if($child instanceof YaPhpDoc_Token_Class && !$classes->contains($child))
				$classes->attach($child);
			elseif($child instanceof YaPhpDoc_Token_Structure_Abstract)
			{
				$childClasses = $child->getAllClasses();
				foreach($childClasses as $childClass)
				{
					if(!$classes->contains($childClass))
						$classes->attach($childClass);
				}
			}
		}
		return $classes;
	}
}
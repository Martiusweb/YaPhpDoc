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
	 * Array of parsable token types.
	 * @var array
	 */
	private $_parsableTokenTypes = array();
	
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
	
	/**
	 * Returns true if the token is of a type that can be parsed.
	 * 
	 * @param string $type
	 * @return YaPhpDoc_Token_Structure_Abstract
	 */
	protected function _addParsableTokenType($type)
	{
		array_push($this->_parsableTokenTypes, $type);
		return $this;
	}
	
	/**
	 * Returns true if the token is of a type that can be parsed.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return bool
	 */
	protected function _isParsableToken(YaPhpDoc_Tokenizer_Token $token)
	{
		return in_array($token->getType(), $this->_parsableTokenTypes, true);
	}
	
	/*
	 * Structure parsing
	 */
	
	/**
	 * @see lib/YaPhpDoc/Token/YaPhpDoc_Token_Abstract#parse($tokensIterator)
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$docblock = null;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
			
			# Tokens modifying the context
			if(!$this->_parseContext($token))
			{
				# Docblock
				if($token->isDocblock())
				{
					# If there is a docblock never used, it must be for this token
					if($dockblock !== null)
						$this->setStandardTags($docblock);
					
					$docblock = YaPhpDoc_Token_Abstract::getToken(
						$this->getParser(), $token->getType(), $this);
					$docblock->parse($tokensIterator);
				}
				# Other tokens
				elseif($this->_isParsableToken($token))
				{
					$parsedToken = YaPhpDoc_Token_Abstract::getToken(
						$this->getParser(), $token->getType(), $this);
					
					if($docblock !== null)
					{
						$parsedToken->setStandardTags($docblock);
						$docblock = null;
					}
					
					$parsedToken->parse($tokensIterator);
					$this->addChild($parsedToken);
					unset($parsedToken);
				}
			}
			
			# We are done, go to next token
			$tokensIterator->next();
		}
		
		# We still have a dockblock, it's a for this token
		if($dockblock !== null)
			$this->setStandardTags($docblock);
		
		return $this;
	}
	
	/**
	 * Determinates if the token is a context modifier. We call context
	 * modifiers tokens which are used to modify the scope, visibility or the
	 * state of a symbol. For instance, "abstract", "static", "public", modifies
	 * methods or properties of a class.
	 * 
	 * The method returns true if the token was a modifier).
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return bool
	 */
	protected function _parseContext(YaPhpDoc_Tokenizer_Token $token)
	{
		return false;
	}
}
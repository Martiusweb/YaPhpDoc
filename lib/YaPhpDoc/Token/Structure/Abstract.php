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
	 * Callbacks functions by token type.
	 * @var callback[]
	 */
	private $_tokenCallbacks = array();
	
	/**
	 * Children tokens.
	 * @var YaPhpDoc_Token_Abstract
	 */
	protected $_children = array();
	
	/**
	 * Children tokens, ordered by token type.
	 * @var array
	 */
	protected $_childrenByTokenType = array('_structure' => array());

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
				$this->addChild($c);
		}
		elseif($child instanceof YaPhpDoc_Token_Abstract)
		{
			array_push($this->_children, $child);
			$type = lcfirst(YaPhpDoc_Tool_Loader::getLocalClassname(get_class($child)));
			
			if(!isset($this->_childrenByTokenType[$type]))
				$this->_childrenByTokenType[$type] = array();
			
			array_push($this->_childrenByTokenType[$type], $child);
			
			if($child instanceof YaPhpDoc_Token_Structure_Abstract)
				array_push($this->_childrenByTokenType['_structure'], $child);
		}
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
	 * Returns an array of child tokens of the given type.
	 * 
	 * @param string $type
	 * @return YaPhpDoc_Token_Abstract[]
	 */
	public function getChildrenByType($type)
	{
		$type = lcfirst($type);
		if(isset($this->_childrenByTokenType[$type]))
			return $this->_childrenByTokenType[$type];
		else
			return array();
	}
	
	/**
	 * Returns an array of descendant tokens of the given type. 
	 * @param string $type
	 * @return YaPhpDoc_Token_Abstract[]
	 */
	public function getDescendantsByType($type)
	{
		$type = lcfirst($type);
		$descendant = $this->getChildrenByType($type);
		foreach($this->_childrenByTokenType['_structure'] as $child)
		{
			/* @var $child YaPhpDoc_Token_Structure_Abstract */
			$descendant = array_merge($descendant,
				$child->getDescendantsByType($type));
		}
		return $descendant;
	}
	
	/**
	 * You can use magic calls as a proxy for getChildrenByType() or
	 * getDescendantsByType(), for instance :
	 *  * getFiles() or files() means getChildrenByType('file').
	 *  * getAllFiles() or allFiles() mens getDescendantsByType('file').
	 *  
	 * The trailing "s" is mandatory.
	 * 
	 * @param string $funcname
	 * @param array $args
	 * @throws YaPhpDoc_Core_Parser_Exception
	 * @return array
	 */
	public function __call($funcname, $args)
	{
		if(substr($funcname, -1) == 's')
		{
			if(substr($funcname, 0, 6) == 'getAll')
				return $this->getDescendantsByType(lcfirst(substr($funcname, 6, -1)));
			elseif(substr($funcname, 0, 3) == 'all')
				return $this->getDescendantsByType(lcfirst(substr($funcname, 3, -1)));
			elseif(substr($funcname, 0, 3) == 'get')
				return $this->getChildrenByType(lcfirst(substr($funcname, 3, -1)));
			else
				return $this->getChildrenByType(lcfirst(substr($funcname, 0, -1)));
		}
		else
		{
			# This exception message is intentionnaly not translated.
			throw new YaPhpDoc_Core_Parser_Exception('Bad function call '.$funcname);
		}
	}
	
	/**
	 * You can use magic getters as a proxy for getChildrenByType() or
	 * getDescendantsByType(), for instance :
	 *  * $foo->files means getChildrenByType('file').
	 *  * $foo->allFiles means getDescendantByType('file').
	 *  
	 * The trailing "s" is mandatory.
	 * 
	 * @param string $tokenType
	 * @return array
	 */
	public function __get($tokenType)
	{
		if(substr($tokenType, -1) == 's')
		{
			if(substr($tokenType, 0, 3) == 'all')
				return $this->getDescendantsByType(lcfirst(substr($tokenType, 3, -1)));
			else
				return $this->getChildrenByType(lcfirst(substr($tokenType, 0, -1)));
		}
	}
	
	/**
	 * Returns true if the token is of a type that can be parsed.
	 * 
	 * An array of type can also be given.
	 * 
	 * @param string $type
	 * @return YaPhpDoc_Token_Structure_Abstract
	 */
	protected final function _addParsableTokenType($type)
	{
		if(is_array($type))
		{
			foreach($type as $t)
				$this->_addParsableTokenType($type);
		}
		
		array_push($this->_parsableTokenTypes, $type);
		return $this;
	}
	
	/**
	 * Adds a callback according to the token type.
	 *  
	 * @param string $token_type
	 * @param callback $callback
	 * @return YaPhpDoc_Token_Structure_Abstract
	 */
	protected final function _addTokenCallback($token_type, $callback)
	{
		$this->_tokenCallbacks[$token_type] = $callback;
		
		return $this;
	}
	
	/**
	 * Returns (if defined) the callback sat for the given type of tokens.
	 * 
	 * @param string $token_type
	 * @return callback
	 */
	protected function _getTokenCallback($token_type)
	{
		if(!isset($this->_tokenCallbacks[$token_type]))
			return null;
		
		return $this->_tokenCallbacks[$token_type];
	}
	
	/**
	 * Returns true if the token is of a type that can be parsed.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return bool
	 */
	protected function _isParsableToken(YaPhpDoc_Tokenizer_Token $token)
	{
		foreach($this->_parsableTokenTypes as $parsable)
		{
			$funcname = 'is'.ucfirst($parsable);
			if($token->$funcname())
				return true;
		}
		return false;
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
					if($docblock !== null)
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
				else
				{
					# callbacks defined by token type, can throw a break exception
					try {
						$this->_tokenCallback($token);
					} catch(YaPhpDoc_Core_Parser_Break_Exception $e)
					{
						$tokensIterator->next();
						break;
					}
				}
			}
			
			# We are done, go to next token
			$tokensIterator->next();
		}
		
		# We still have a dockblock, it's a for this token
		if($docblock !== null)
			$this->setStandardTags($docblock);
		
		return $this;
	}
	
	/**
	 * Determinates if the token is a context modifier. We call context
	 * modifiers tokens which are used to modify the scope, visibility or the
	 * state of a symbol. For instance, "abstract", "static", "public", modifies
	 * methods or properties of a class.
	 * 
	 * The method returns true if the token was a modifier.
	 * 
	 * This method may be overriden in a concrete structure.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return bool
	 */
	protected function _parseContext(YaPhpDoc_Tokenizer_Token $token)
	{
		return false;
	}
	
	/**
	 * Calls a callback function or method according to the token type.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return YaPhpDoc_Token_Structure_Abstract
	 */
	protected final function _tokenCallback(YaPhpDoc_Tokenizer_Token $token)
	{
		$callback = $this->_getTokenCallback($token->getType());
		if($callback !== null)
		{
			call_user_func($callback, $token);
		}
		
		return $this;
	}
}
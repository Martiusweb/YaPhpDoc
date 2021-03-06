<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represents a namespace.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Namespace extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * Namespace separator character.
	 * @var string
	 */
	const NS_SEPARATOR = '\\';
	
	/**
	 * Namespace imbrication level.
	 * @var int
	 */
	private $_nested = 0;
	
	/**
	 * True if this namespace is a merge of all objects representing parts of
	 * the same namespace.
	 * @var bool
	 */
	protected $_merged = false;
	
	/**
	 * Constructor of a namespace.
	 * @param YaPhpDoc_Token_File $parent
	 * @param string $name optional
	 */
	public function __construct(YaPhpDoc_Token_Structure_Abstract $parent, $name = 'unknown')
	{
		parent::__construct($parent, 'namespace', $name);
	}
	
	/**
	 * Returns the full name of the namespace (prefixed with parent namespaces
	 * names).
	 * @return string
	 */
	public function getFullName()
	{
		$name = $this->_name;
		if($this->_parent instanceof YaPhpDoc_Token_Namespace)
			$name = $name.self::NS_SEPARATOR.$this->_parent->getFullName();
		return $name;
	}
	
	/**
	 * Parses the iterator
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Namespace
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		if(!$this->_merged && $tokensIterator->current()->isNamespace())
		{
			$tokensIterator->next();
			
			# Parse the name of the namespace
			$this->_name = '';
			while($tokensIterator->valid())
			{
				$token = $tokensIterator->current();
				if($token->isConstantString())
				{
					$this->_name .= $token->getConstantContent();
				}
				elseif($token->isNamespaceSeparator())
				{
					$this->_name .= self::NS_SEPARATOR;
				}
				elseif($token->getType() == ';' || $token->getType() == '{')
				{
					break;
				}
				$tokensIterator->next();
			}
			
			# Parsabe tokens in a namespace
			$this->_addParsableTokenType('namespace');
			$this->_addParsableTokenType('const');
			$this->_addParsableTokenType('use');
			$this->_addParsableTokenType('global');
			$this->_addParsableTokenType('function');
			$this->_addParsableTokenType('class');
			$this->_addParsableTokenType('iterator');
					
			# Manage nested blocks if namespace is enclosed by brackets
			if($token->getType() == '{')
			{
				$this->_addTokenCallback('{', array($this, '_parseLeftBrace'));
				$this->_addTokenCallback('}', array($this, '_parseRightBrace'));
			}
			
			# Parse content
			parent::parse($tokensIterator);
			$merged = $this->getParser()->getCollection('namespace')
				->getByName($this->getFullName());
			/* @var $marged YaPhpDoc_Token_Namespace */
			$merged->merge($this);
		}
		
		return $this;
	}

	/**
	 * @see lib/YaPhpDoc/Token/YaPhpDoc_Token_Abstract#setStandardTags($docblock)
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		$this->appendDescription($docblock->getContent());
		return parent::setStandardTags($docblock);
	}
	
	/**
	 * Remembers the block level.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseLeftBrace(YaPhpDoc_Tokenizer_Token $token)
	{
		++$this->_nested;
	}
	
	/**
	 * Remembers the block level (end of a block). Stops the parsing if the
	 * level is 0 and the namespace is enclosed by brackets.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseRightBrace(YaPhpDoc_Tokenizer_Token $token)
	{
		--$this->_nested;
		if($this->_nested == 0)
			$this->_breakParsing();
	}
	
	/**
	 * @return YaPhpDoc_Token_Namespace
	 */
	public function merge(YaPhpDoc_Token_Namespace $namespace)
	{
		foreach($namespace->_children as $child)
			$this->addChild($child);
		
		$this->_merged = true;
		return $this;
	}
}
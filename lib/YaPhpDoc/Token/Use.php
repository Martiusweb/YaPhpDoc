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
class YaPhpDoc_Token_Use extends YaPhpDoc_Token_Abstract
{
	/**
	 * Alias of the use statement.
	 * @var string|NULL
	 */
	protected $_alias;
	
	/**
	 * Constructor of a use token.
	 * @param YaPhpDoc_Token_File $parent
	 */
	public function __construct(YaPhpDoc_Token_Structure_Abstract $parent)
	{
		parent::__construct($parent, 'use', '');
	}
	
	/**
	 * Parses the iterator
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Use
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$this->_addTokenCallback('constantString', array($this, '_parseName'));
		$this->_addTokenCallback('namespaceSeparator', array($this, '_parseSeparator'));
		$this->_addTokenCallback('as', array($this, '_parseAlias'));
		$this->_addTokenCallback(';', array($this, '_breakParsing'));
		
		
		parent::parse($tokensIterator);
		
		return $this;
	}
	
	/**
	 * Evaluates the namespace name.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseName(YaPhpDoc_Tokenizer_Token $token)
	{
		$this->_name .= $token->getConstantContent();
	}
	
	/**
	 * Replace the callback that parses the name to the one that parses the
	 * use alias.
	 * 
	 * @return void
	 */
	protected function _parseAlias()
	{
		$this->_addTokenCallback('constantString', array($this, '_parseAliasName'));
	}
	
	/**
	 * Evaluates the alias name.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseAliasName(YaPhpDoc_Tokenizer_Token $token)
	{
		$this->_alias = $token->getConstantContent();
	}
	
	/**
	 * Adds separator to namespace name.
	 * @return void
	 */
	protected function _parseSeparator()
	{
		$this->_name .= YaPhpDoc_Token_Namespace::NS_SEPARATOR;
	}
	
	/**
	 * Returns the use statement alias.
	 * @return string|NULL
	 */
	public function getAlias()
	{
		return $this->_alias;
	}
}
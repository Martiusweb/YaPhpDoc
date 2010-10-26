<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a PHP file.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_File extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * Current namespace
	 * @var YaPhpDoc_Token_Namespace
	 */
	protected $_currentNamespace;
	
	/**
	 * File constructor.
	 * @param string $filename
	 * @param YaPhpDoc_Token_Structure_Abstract $parent
	 */
	public function __construct($filename, YaPhpDoc_Token_Structure_Abstract $parent)
	{
		parent::__construct($parent, YaPhpDoc_Token_Abstract::FILE, $filename);
	}
	
	/**
	 * Parse a PHP file.
	 * 
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_File
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$this->_addParsableTokenType('namespace');
		$this->_addParsableTokenType('use');
		$this->_addParsableTokenType('const');
		$this->_addParsableTokenType('global');
		$this->_addParsableTokenType('function');
		$this->_addParsableTokenType('class');
		$this->_addParsableTokenType('interface');
		
		parent::parse($tokensIterator);
		
		return $this;
	}
	
	/**
	 * Returns the current parent (a namespace or a file).
	 * 
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected function _getCurrentParent()
	{
		if($this->_currentNamespace != null)
		{
			return $this->_currentNamespace;
		}
		return $this;
	}
	
	/**
	 * Return the filename without the parsing root.
	 * @return string
	 */
	public function getFilename()
	{
		return str_replace($this->getParser()->getDirectories(), '', $this->getName());
	}
	
	/**
	 * @see YaPhpDoc/Token/YaPhpDoc_Token_Abstract#setStandardTags($docblock)
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		$this->appendDescription($docblock->getContent());
		return parent::setStandardTags($docblock);
	}
}
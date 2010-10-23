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
		parent::__construct($filename, YaPhpDoc_Token_Abstract::FILE, $parent);
	}
	
	/**
	 * Parse a PHP file.
	 * 
	 * @todo A refactoring will be performed in 1.X+, management of namespaces is not fully stable
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_File
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		
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
}
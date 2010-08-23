<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent the document token, which is in fact a root node for the
 * documented elements.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Document extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * The parser objet
	 * @var YaPhpDoc_Core_Parser
	 */
	private $_parser;
	
	/**
	 * Root constructor.
	 * Parent of the root is null.
	 * 
	 * @param YaPhpDoc_Core_Parser $parser
	 */
	public function __construct(YaPhpDoc_Core_Parser $parser)
	{
		$this->_name = 'Root';
		$this->_tokenType = YaPhpDoc_Token_Abstract::ROOT;
		$this->_parser = $parser;
	}
	
	/**
	 * Returns the parser object.
	 * @return YaPhpDoc_Core_Parser
	 */
	public function getParser()
	{
		return $this->_parser;
	}
}
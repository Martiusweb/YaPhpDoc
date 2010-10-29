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
	
	/**
	 * Returns a collection stored by the parser if this one exists or returns
	 * children. This new behaviour is used, for instance, to get a collection
	 * of merged namespaces.
	 * 
	 * @see YaPhpDoc/Token/Structure/YaPhpDoc_Token_Structure_Abstract#getChildrenByType($type)
	 */
	public function getChildrenByType($type)
	{
		if($this->getParser()->hasCollection($type))
		{
			return $this->getParser()->getCollection($type);
		}
		else
			return parent::getChildrenByType($type);
	}
}
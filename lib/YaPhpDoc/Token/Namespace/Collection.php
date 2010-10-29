<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A set of namespaces, containing merged namespaces.
 * 
 * This collection is used to parse and merge namespaces defined through several
 * files.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Namespace_Collection
	extends YaPhpDoc_Token_Structure_Collection_Abstract
{
	/**
	 * Constructs the collection.
	 * 
	 * @param YaPhpDoc_Core_Parser $parser
	 */
	public function __construct(YaPhpDoc_Core_Parser $parser)
	{
		$this->_initialize('namespace');
		parent::__construct($parser);
	}
}
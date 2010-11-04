<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A set of structures stored by name.
 * 
 * @author Martin Richard
 */
abstract class YaPhpDoc_Token_Structure_Collection_Abstract
	implements IteratorAggregate
{
	/**
	 * Type of structures stored by the collection
	 * @var string
	 */
	private $_type;
	
	/**
	 * Parser object.
	 * @var YaPhpDoc_Core_Parser
	 */
	protected $_parser;
	
	/**
	 * Collection of structures.
	 * @var YaPhpDoc_Token_Structure_Abstract[]
	 */
	protected $_structures = array();
	
	/**
	 * Constructs the collection.
	 * @param YaPhpDoc_Core_Parser $parser The parser instance.
	 */
	public function __construct(YaPhpDoc_Core_Parser $parser)
	{
		$this->_parser = $parser;
	}
	
	/**
	 * Initialize the structure.
	 * @param string $type Type of tructures stored by the collection
	 * @return YaPhpDoc_Token_Collection_Abstract
	 */
	protected function _initialize($type)
	{
		$this->_type = $type;
	}
	
	/**
	 * Returns a structure according to its full name.
	 * If the structure doesn't exists yet in the collection, a new one is
	 * created.
	 * 
	 * @param string $name
	 * @return YaPhpDoc_Token_Structure_Abstract
	 */
	public function getByName($name)
	{
		# Structure doesn't exists yet, create it.
		if(!isset($this->_structures[$name]))
		{
			$this->add(YaPhpDoc_Token_Abstract::getToken($this->_parser,
				$this->_type, $this->_parser->getRoot(), $name));
		}
		
		return $this->_structures[$name];
	}
	
	/**
	 * Adds a structure.
	 * 
	 * If the name is not provided, the function will try to find it using
	 * getName() method of the structure.
	 * 
	 * @param YaPhpDoc_Token_Structure_Abstract $structure
	 * @param string $name (optional)
	 * @return YaPhpDoc_Token_Structure_Collection_Abstract
	 */
	public function add(YaPhpDoc_Token_Structure_Abstract $structure, $name = null)
	{
		if($name === null)
			$name = $structure->getName();
		
		$this->_structures[$name] = $structure;
		
		return $this;
	}
	
	/**
	 * Returns an iterator of the elements in the collection.
	 * @return Iterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->_structures);
	}
	
	/**
	 * Returns the set of structures as an array.
	 * 
	 * @return array
	 */
	public function toArray()
	{
		return $this->_structures;
	}
	
	/**
	 * Returns a new instance of a typed collection. The collection class name
	 * is gessed according to the token type collection suffixed by
	 * "_Collection". For instance, if namespaces class is
	 * YaPhpDoc_Token_Namespace, the associated collection is
	 * YaPhpDoc_Token_Namespace_Collection.
	 * 
	 * If you want to override a token type which as a collection class defined,
	 * you must also override this collection class.
	 * 
	 * @param YaPhpDoc_Core_Parser $parser
	 * @param string $type type of the tokens in the collection
	 * 
	 * @return YaPhpDoc_Token_Structure_Collection_Abstract
	 */
	public static function getCollection(YaPhpDoc_Core_Parser $parser, $type)
	{
		$classname = $parser->getConfig()->class->get($type);
		
		if($classname === null)
		{
			throw new YaPhpDoc_Core_Parser_Exception(sprintf(
				$parser->l10n()->getTranslation('parser')
				->_('Unable to find a class for the collection of type %s'), $type
			));
		}
		
		$classname .= '_Collection';
		return new $classname($parser);
	}
}
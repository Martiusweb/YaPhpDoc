<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent any token (file, package, namespace, class, function, etc) the
 * program can parse and document.
 * 
 * @author Martin Richard
 */
abstract class YaPhpDoc_Token_Abstract
{
	/**
	 * Root token type identifier
	 * @var int
	 */
	const ROOT = 0;
	
	/**
	 * File token type identifier
	 * @var file
	 */
	const FILE = 1;
	
	/**
	 * Name of the token
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Token type (as given by token_get_all)
	 * @var int
	 */
	protected $_tokenType;
	
	/**
	 * Parent node 
	 * @var YaPhpDoc_Token_Abstract
	 */
	protected $_parent;
	
	/**
	 * Author of the token
	 * @var string|NULL
	 */
	protected $_author;

	/**
	 * Licence of the token
	 * @var string|NULL
	 */
	protected $_license;
	
	/**
	 * Description of the token
	 * @var string|NULL
	 */
	protected $_description;
	
	/**
	 * Constructor of a token. The type of token is not tested,
	 * but the behavior of the program is not predictable if
	 * the givent value is not one of the php token constants.
	 * 
	 * @param string $name
	 * @param int $token_type
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct($name, $token_type, YaPhpDoc_Token_Abstract $parent)
	{
		$this->_name = $name;
		$this->_tokenType = $token_type;
		$this->_parent = $parent;
	}
	
	/**
	 * Returns the token name.
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Returns the token type.
	 * @return int
	 */
	public function getTokenType()
	{
		$this->_tokenType;
	}
	
	/**
	 * Returns parent node. Returns null if node is the root.
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function getParent()
	{
		return $this->_parent;
	}
	
	/**
	 * Parse a token using the token iterator. A non overriden
	 * parser will throw a YaPhpDoc_Core_Parser_Exception.
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @throw YaPhpDoc_Core_Parser_Exception
	 * @return YaPhpDoc_Token_Abstract 
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		throw new YaPhpDoc_Core_Parser_Exception(
			Ypd::getInstance()->getTranslation('parser')
				->_('This token type is not parsable'));
	}
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : author, license
	 * 
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		if($author = $docblock->getTags('author'))
			$this->setAuthor(implode(', ', $author));
		if($licence = $docblock->getTags('licence'));
			$this->setLicense(implode(', ', $licence));
		
		return $this;
	}
	
	/**
	 * Set author.
	 * @param string $author
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setAuthor($author)
	{
		$this->_author = $author;
		return $this;
	}
	
	/**
	 * Get author.
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->_author;
	}
	
	/**
	 * Set license.
	 * @param string $license
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setLicense($license)
	{
		$this->_license;
		return $this;
	}
	
	/**
	 * Returns license.
	 * @return string|NULL
	 */
	public function getLicense()
	{
		return $this->_license;
	}
	
	/**
	 * Sets descriptions.
	 * @param string $description
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setDescription($description)
	{
		$this->_description = $description;
		return $this;
	}
	
	/**
	 * Returns description.
	 * @return string|NULL
	 */
	public function getDescription()
	{
		return $this->_description;
	}
}
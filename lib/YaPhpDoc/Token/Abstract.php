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
 * @todo tags : deprecated, see
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
	 * @var array|NULL|YaPhpDoc_Tag_Author
	 */
	protected $_author;

	/**
	 * Licence of the token
	 * @var array|NULL|YaPhpDoc_Tag_Licence
	 */
	protected $_license;
	
	/**
	 * Copyright notice of the token.
	 * @var array|NULL|YaPhpDoc_Tag_Copyright
	 */
	protected $_copyright;
	
	/**
	 * Deprecated state of the token.
	 * @var array|NULL|YaPhpDoc_Tag_Deprecated
	 */
	protected $_deprecated = false;
	
	/**
	 * Description of the token
	 * @var string|NULL
	 */
	protected $_description;
	
	/**
	 * See (reference to a documentation complement).
	 * @var array|NULL|YaPhpDoc_Tag_See
	 */
	protected $_see;
	
	/**
	 * Since refers to the first version when the token is available.
	 * @var array|NULL|YaPhpDoc_Tag_Since
	 */
	protected $_since;
	
	/**
	 * Non identified tags 
	 * @var YaPhpDoc_Tag_Abstract[]|NULL
	 */
	protected $_anonymousTags;
	
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
	 * Tags are : author, license, copyright, deprecated
	 * 
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		if($author = $docblock->getTags('author'))
			$this->setAuthor($author);
		if($licence = $docblock->getTags('licence'))
			$this->setLicense($licence);
		if($copyright = $docblock->getTags('copyright'))
			$this->setCopyright($copyright);
		if($deprecated = $docblock->getTags('deprecated'))
			$this->setDeprecated($deprecated);
		if($tags = $docblock->getNotUsedTags())
			$this->_anonymousTags = $tags;
		
		return $this;
	}
	
	/**
	 * Set author.
	 * @param YaPhpDoc_Tag_Author|array $author
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setAuthor($author)
	{
		$this->_author = $author;
		return $this;
	}
	
	/**
	 * Get author.
	 * @return YaPhpDoc_Tag_Author
	 */
	public function getAuthor()
	{
		return $this->_author;
	}
	
	/**
	 * Set license.
	 * @param YaPhpDoc_Tag_License|array $license
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setLicense($license)
	{
		$this->_license;
		return $this;
	}
	
	/**
	 * Returns license.
	 * @return YaPhpDoc_Tag_License|NULL
	 */
	public function getLicense()
	{
		return $this->_license;
	}
	
	/**
	 * Set copyright notice. 
	 * @param YaPhpDoc_Tag_Copyright|array $copyright
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setCopyright($copyright)
	{
		return $this;
	}
	
	/**
	 * Returns copyright notice.
	 * @return YaPhpDoc_Tag_Copyright|NULL
	 */
	public function getCopyright()
	{
		return $this->_copyright;
	}
	
	/**
	 * Set deprecated tag.
	 * @param YaPhpDoc_Tag_Deprecated|array $deprecated
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setDeprecated($deprecated)
	{
		$this->_deprecated = $deprecated;
		return $this;
	}
	
	/**
	 * Returns deprecated tag.
	 * @return YaPhpDoc_Tag_Deprecated
	 */
	public function getDeprecated()
	{
		return $this->_deprecated;
	}
	
	/**
	 * Set since tag.  
	 * @param YaPhpDoc_Tag_Since|array $deprecated
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setSince($since)
	{
		$this->_since = $since;
		return $this;
	}
	
	/**
	 * Returns since tag.
	 * @return YaPhpDoc_Tag_Since
	 */
	public function getSince()
	{
		return $this->_since;
	}
	
	/**
	 * Returns unknown tags.
	 * If $tagname is specified, only these are returned, else an array of
	 * array of tags (key is the tagname) is returned.
	 * 
	 * @param string $tagname (optional)
	 * @return array
	 */
	public function getAnonymousTags($tagname = null)
	{
		if(null !== $tagname)
		{
			return isset($this->_anonymousTags[$tagname]) ?
				$this->_anonymousTags[$tagname] : array();
		} 
		return $this->_anonymousTags;
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
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
abstract class YaPhpDoc_Token_Abstract implements
	YaPhpDoc_Core_OutputManager_Aggregate,
	YaPhpDoc_Core_TranslationManager_Aggregate
{
	/**
	 * Root token type identifier
	 * @var int
	 */
	const ROOT = 0;
	
	/**
	 * File token type identifier
	 * @var string
	 */
	const FILE = 'file';
	
	/**
	 * Name of the token
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Token type
	 * @var string
	 */
	protected $_tokenType;
	
	/**
	 * Parent node 
	 * @var YaPhpDoc_Token_Abstract
	 */
	protected $_parent;
	
	/**
	 * The parser objet
	 * @var YaPhpDoc_Core_Parser
	 */
	protected $_parser;
	
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
	 * @var YaPhpDoc_Tag_Deprecated
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
	 * @var NULL|YaPhpDoc_Tag_Since
	 */
	protected $_since;
	
	/**
	 * Version of the token.
	 * @var NULL|YaPhpDoc_Tag_Since
	 */
	protected $_version;
	
	/**
	 * Non identified tags 
	 * @var YaPhpDoc_Tag_Abstract_Abstract[]|NULL
	 */
	protected $_anonymousTags;
	
	/**
	 * Skip whitespaces token while parsing.
	 * @var bool
	 */
	private $_ignoreWhitespaces = true;
	
	/**
	 * Callbacks functions by token type.
	 * @var callback[]
	 */
	private $_tokenCallbacks = array();
	
	/**
	 * Callbacks functions by token type, gives the tokensIterator for
	 * subparsing.
	 * @var callback[]
	 */
	private $_tokensIteratorCallbacks = array();
	
	/**
	 * Constructor of a token. The type of token is not tested,
	 * but the behavior of the program is not predictable if
	 * the givent value is not one of the php token constants.
	 * 
	 * @param YaPhpDoc_Token_Abstract $parent
	 * @param int $token_type
	 * @param string $name
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent, $token_type, $name)
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
		return $this->_tokenType;
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
	 * Returns the parser object.
	 * @return YaPhpDoc_Core_Parser
	 */
	public function getParser()
	{
		if($this->_parser === null)
		{
			$this->_parser = $this->getParent()->getParser(); 
		}
		return $this->_parser;
	}
	
	/**
	 * @see YaPhpDoc/Core/OutputManager/YaPhpDoc_Core_OutputManager_Aggregate#getOutputManager()
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function getOutputManager()
	{
		return $this->getParser()->getOutputManager();
	}
	
	/**
	 * @see YaPhpDoc/Core/OutputManager/YaPhpDoc_Core_OutputManager_Aggregate#out()
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function out()
	{
		return $this->getParser()->out();
	}
	
	/**
	 * @see YaPhpDoc/Core/TranslationManager/YaPhpDoc_Core_TranslationManager_Aggregate#getTranslationManager()
	 * @return YaPhpDoc_Core_TranslationManager_Interface
	 */
	public function getTranslationManager()
	{
		return $this->getParser()->getTranslationManager();
	}
	
	/**
	 * @see YaPhpDoc/Core/TranslationManager/YaPhpDoc_Core_TranslationManager_Aggregate#l10n()
	 * @return YaPhpDoc_Core_TranslationManager_Interface
	 */
	public function l10n()
	{
		return $this->getParser()->l10n();
	}
	
	/**
	 * Parse a token using the token iterator. A non overriden
	 * parser will throw a YaPhpDoc_Core_Parser_Exception.
	 * 
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @throws YaPhpDoc_Core_Parser_Exception
	 * @return YaPhpDoc_Token_Abstract 
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$docblock = null;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
			
			# Skip whitespaces
			if($this->_ignoreWhitespaces && $token->isWhitespace())
			{
				$tokensIterator->next();
				continue;
			}
				
			# Tokens modifying the context
			if(!$this->_parseContext($token))
			{
				# Docblock
				if($token->isDocblock())
				{
					# If there is a docblock never used, it must be for this token
					if($docblock !== null)
						$this->setStandardTags($docblock);
					
					$docblock = YaPhpDoc_Token_Abstract::getToken(
						$this->getParser(), $token->getType(), $this);
					$docblock->parse($tokensIterator);
				}
				else
				{
					# callbacks defined by token type, can throw a break exception
					try {
						$this->_tokenCallback($token);
						$this->_tokensIteratorCallback($token, $tokensIterator);
					} catch(YaPhpDoc_Core_Parser_Break_Exception $e)
					{
						break;
					}
				}
			}
			
			# We are done, go to next token
			$tokensIterator->next();
		}
		
		# We still have a dockblock, it's a for this token
		if($docblock !== null)
			$this->setStandardTags($docblock);
		
		return $this;
	}
	
	/**
	 * Adds a callback according to the token type. You can use de wildcard *
	 * type for all types (called if a specific callback is not defined).
	 *  
	 * @param string $token_type
	 * @param callback $callback
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected final function _addTokenCallback($token_type, $callback)
	{
		$this->_tokenCallbacks[$token_type] = $callback;
		
		return $this;
	}
	
	/**
	 * Removes a token callback.
	 * @param string $token_type
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected final function _removeTokenCallback($token_type)
	{
		unset($this->_tokenCallbacks[$token_type]);
		
		return $this;
	}
	
	/**
	 * Returns (if defined) the callback sat for the given type of tokens.
	 * 
	 * @param string $token_type
	 * @return callback
	 */
	protected function _getTokenCallback($token_type)
	{
		if(!isset($this->_tokenCallbacks[$token_type]))
		{
			if(isset($this->_tokenCallbacks['*']))
				return $this->_tokenCallbacks['*'];
			return null;
		}
		
		return $this->_tokenCallbacks[$token_type];
	}
	
	/**
	 * Calls a callback function or method according to the token type.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected final function _tokenCallback(YaPhpDoc_Tokenizer_Token $token)
	{
		$callback = $this->_getTokenCallback($token->getType());
		if($callback !== null)
		{
			call_user_func($callback, $token);
		}
		
		return $this;
	}
	
	/**
	 * Determinates if the token is a context modifier. We call context
	 * modifiers tokens which are used to modify the scope, visibility or the
	 * state of a symbol. For instance, "abstract", "static", "public", modifies
	 * methods or properties of a class.
	 * 
	 * The method returns true if the token was a modifier.
	 * 
	 * This method may be overriden in a concrete structure.
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return bool
	 */
	protected function _parseContext(YaPhpDoc_Tokenizer_Token $token)
	{
		if($token->isAbstract())
		{
			$this->getParser()->setAbstract();
			return true;
		}
		if($token->isFinal())
		{
			$this->getParser()->setFinal();
			return true;
		}
		
		return false;
	}
	
	/**
	 * Adds a callback according to the token type, which provides the
	 * tokens iterator for sub-parsing.
	 *  
	 * @param string $token_type
	 * @param callback $callback
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected final function _addTokensIteratorCallback($token_type, $callback)
	{
		$this->_tokensIteratorCallbacks[$token_type] = $callback;
		
		return $this;
	}
	
	/**
	 * Removes a tokens iterator callback.
	 * 
	 * @param string $token_type
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected final function _removeTokensIteratorCallback($token_type)
	{
		unset($this->_tokensIteratorCallbacks[$token_type]);
		
		return $this;
	}
	
	
	/**
	 * Returns (if defined) the callback sat for the given type of tokens for
	 * sub-parsing.
	 * 
	 * @param string $token_type
	 * @return callback
	 */
	protected function _getTokensIteratorCallback($token_type)
	{
		if(!isset($this->_tokensIteratorCallbacks[$token_type]))
		{
			if(isset($this->_tokensIteratorCallbacks['*']))
				return $this->_tokensIteratorCallbacks['*'];
			return null;
		}
		
		return $this->_tokensIteratorCallbacks[$token_type];
	}
	
	/**
	 * Calls a callback function or method according to the token type, providing
	 * the tokens iterator for sub-parsing. You can use de wildcard type for all
	 * types (called if a specific callback is not defined).
	 * 
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected final function _tokensIteratorCallback(
		YaPhpDoc_Tokenizer_Token $token,
		YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$callback = $this->_getTokensIteratorCallback($token->getType());
		if($callback !== null)
		{
			call_user_func($callback, $tokensIterator);
		}
		
		return $this;
	}
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : author, license, copyright, deprecated, since, see, version
	 * 
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		if($author = $docblock->getTags('author'))
			$this->setAuthor($author);
		if($license = $docblock->getTags('license'))
			$this->setLicense($license);
		if($copyright = $docblock->getTags('copyright'))
			$this->setCopyright($copyright);
		if($deprecated = $docblock->getTags('deprecated'))
			$this->setDeprecated($deprecated);
		if($since = $docblock->getTags('since'))
			$this->setSince($since);
		if($see = $docblock->getTags('see'))
			$this->setSee($see);
		if($version = $docblock->getTags('version'))
			$this->setVersion($version);
		if($tags = $docblock->getNotUsedTags())
			$this->_anonymousTags = $tags;
		
		return $this;
	}
	
	/**
	 * Breaks the parser, this method allow to control and stop parsing in a
	 * parent parse() method.
	 * 
	 * @throws YaPhpDoc_Core_Parser_Break_Exception
	 */
	protected function _breakParsing()
	{
		throw new YaPhpDoc_Core_Parser_Break_Exception();
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
		$this->_license = $license;
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
		$this->_copyright = $copyright;
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
	 * @param YaPhpDoc_Tag_Deprecated $deprecated
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setDeprecated(YaPhpDoc_Tag_Deprecated $deprecated)
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
	 * Returns true if the token is marked as deprecated.
	 * It's a proxy method for the (maybe existing) deprecated tag.
	 * @return bool
	 */
	public function isDeprecated()
	{
		return (null !== $this->_deprecated
			&& $this->_deprecated->isDeprecated());
	}
	
	/**
	 * Set see tag.
	 * @param array|YaPhpDoc_Tag_See $see
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setSee($see)
	{
		$this->_see = $see;
		return $this;
	}
	
	/**
	 * Returns see tag(s).
	 * @return YaPhpDoc_Tag_See|array|NULL
	 */
	public function getSee()
	{
		return $this->_see;
	}
	
	/**
	 * Set since tag.  
	 * @param YaPhpDoc_Tag_Since $deprecated
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setSince(YaPhpDoc_Tag_Since $since)
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
	 * Set version tag.
	 * 
	 * @param YaPhpDoc_Tag_Version $version
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setVersion(YaPhpDoc_Tag_Version $version)
	{
		$this->_version = $version;
		return $this;
	}
	
	/**
	 * Returns version tag.
	 * @return YaPhpDoc_Tag_Version
	 */
	public function getVersion()
	{
		return $this->_version;
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
	 * Sets description.
	 * @param string $description
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function setDescription($description)
	{
		$this->_description = $description;
		return $this;
	}
	
	/**
	 * Append text to existing description.
	 * @param string $description
	 * @return YaPhpDoc_Token_Abstract
	 */
	public function appendDescription($description)
	{
		$this->_description .= $description;
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
	
	/*
	 * Tokens factory
	 */
	
	/**
	 * Returns a new instance of a token according to its type $type.
	 * The token type is evaluated according to the configuration (class node),
	 * if the method can't find a class to instanciate, a
	 * YaPhpDoc_Core_Parser_Exception is thrown.
	 * 
	 * @param YaPhpDoc_Core_Parser $parser
	 * @param string $type
	 * @param YaPhpDoc_Token_Abstract $parent
	 * @param string $name optional, "unknown" is default value
	 * @throws YaPhpDoc_Core_Parser_Exception
	 * @return YaPhpDoc_Token_Abstract
	 */
	public static function getToken(YaPhpDoc_Core_Parser $parser, $type,
		YaPhpDoc_Token_Abstract $parent, $name = 'unknown')
	{
		$classname = $parser->getConfig()->class->get($type);
		
		if($classname === null)
		{
			throw new YaPhpDoc_Core_Parser_Exception(sprintf(
				$parser->l10n()->getTranslation('parser')
				->_('Unable to find a class for the token type %s'), $type
			));
		}
		
		return new $classname($parent, $name);
	}
	
	/**
	 * Returns a new instance of a file token.
	 * The token type is evaluated according to the configuration (class node),
	 * there is no check of the correctness of the found classname.
	 * 
	 * @param YaPhpDoc_Core_Parser $parser
	 * @param string $filename
	 * @param YaPhpDoc_Token_Structure_Abstract $parent
	 * @return YaPhpDoc_Token_File
	 */
	public static function getFileToken(YaPhpDoc_Core_Parser $parser, $filename,
		YaPhpDoc_Token_Structure_Abstract $parent)
	{
		$classname = $parser->getConfig()->class->get(self::FILE);
		return new $classname($filename, $parent);
	}
	
	/**
	 * Returns a new instance of a root.
	 * The token tpe is evaluated according to the configuration
	 * (class/document node), there is no check of the correctness of the found
	 * classname.
	 * 
	 * @param YaPhpDoc_Core_Parser $parser
	 * @return YaPhpDoc_Token_Document
	 */
	public static function getDocumentToken(YaPhpDoc_Core_Parser $parser)
	{
		$classname = $parser->getConfig()->class->get('document');
		return new $classname($parser);
	}
}
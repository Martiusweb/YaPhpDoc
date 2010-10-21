<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A tag is a parsable information that can be contained into a docblock
 * used to adds information about token.
 * 
 * @tags are documentation elements on a line starting with "@".
 * 
 * @author Martin Richard
 */
abstract class YaPhpDoc_Tag_Abstract_Abstract implements YaPhpDoc_Core_OutputManager_Aggregate, YaPhpDoc_Core_TranslationManager_Aggregate
{
	/**
	 * Parser object.
	 * @return YaPhpDoc_Core_Parser
	 */
	protected $_parser;
	
	/**
	 * Name of the @tag.
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Value of the @tag.
	 * @var string|NULL
	 */
	protected $_value;
	
	/**
	 * Can the tag be used multiple times ?
	 * @var bool
	 */
	protected $_multiple_usage = true;
	
	/**
	 * Constructor of a tag.
	 * 
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
	}
	
	/**
	 * Return the @tag name.
	 * 
	 * @return string  
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Returns @tag value.
	 * 
	 * @return string|NULL
	 */
	public function getValue()
	{
		return $this->_value;	
	}
	
	/**
	 * By default, when converted into string, the value is returned.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		if(($value = $this->getValue()) !== null)
			return $value;
		return '';
	}
	
	/**
	 * Returns true if the tags can be used several times in the same docblock.
	 * @return bool
	 */
	public function isMultipleUsage()
	{
		return $this->_multiple_usage;
	}
	
	/**
	 * Set the multiple_usage flag.
	 * @param bool $flag (optional, default to true)
	 * @return YaPhpDoc_Tag_Abstract_Abstract
	 */
	protected function _setMultipleUsage($flag = true)
	{
		$this->_multiple_usage = $flag;
		return $this;
	}
	
	/**
	 * Parses the tag line.
	 * 
	 * @param string $line
	 * @return YaPhpDoc_Tag_Abstract_Abstract
	 */
	protected function _parse($line)
	{
		if(preg_match('`^@([a-zA-Z0-9_\-]+)\s*(.*|$)`', $line, $matches))
		{
			$this->_name = $matches[1];
			if(!empty($matches[2]))
				$this->_value = trim($matches[2]);
		}
		else
		{
			throw new YaPhpDoc_Tag_Exception(
				$this->l10n()->getTranslation('tag')->_(
				'Invalid tag parsing'
			));
		}
		return $this;
	}
	
	/**
	 * Tag object factory. Creates an object YaPhpDoc_Tag_{Tagname} or a
	 * YaPhpDoc_Tag_Anonymous if the class is not defined.
	 * 
	 * YaPhpDoc will try to autoload the class if this one respects the
	 * classname convention and the definition file location
	 * (/lib/YaPhpDoc/Tag/{Tagname}).
	 * 
	 * @param string $tagline
	 * @param YaPhpDoc_Core_Parser $parser
	 * @return YaPhpDoc_Tag_Abstract_Abstract
	 */
	public static function getTag($tagline, YaPhpDoc_Core_Parser $parser)
	{
		if(preg_match('`^@([a-zA-Z0-9_\-]+)`', $tagline, $matches))
		{
			$tagname = $matches[1];
			unset($matches);
			
			$class = 'YaPhpDoc_Tag_'.ucfirst($tagname);
			if(!YaPhpDoc_Tool_Loader::classExists($class))
			{
				$class = 'YaPhpDoc_Tag_Anonymous';
			}
		}
		else
			$class = 'YaPhpDoc_Tag_Anonymous';

		$tag = new $class($tagline);
		$tag->_parser = $parser;
		$tag->_parse($tagline);
		return $tag;
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
}
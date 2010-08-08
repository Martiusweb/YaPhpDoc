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
abstract class YaPhpDoc_Tag_Abstract
{
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
	 * Constructor of a tag.
	 * 
	 * @param string $tagline
	 */
	public function __construct($tagline)
	{
		$this->_parse($tagline);
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
	 * Parses the tag line.
	 * 
	 * @param string $line
	 * @return YaPhpDoc_Tag_Abstract
	 */
	protected function _parse($line)
	{
		if(preg_match('`^@([a-zA-Z0-9_\-]+)[\b]*(.*|$)`', $line, $matches))
		{
			$this->_name = $matches[1];
			if(!empty($matches[2]))
				$this->_value = $matches[2];
		}
		else
		{
			throw new YaPhpDoc_Tag_Exception(
				Ypd::getInstance()->getTranslation('tag')->_(
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
	 * @return YaPhpDoc_Tag_Abstract
	 */
	public static function getTag($tagline)
	{
		if(preg_match('`^@([a-zA-Z0-9_\-]+)`', $tagline, $matches))
		{
			$tagname = $matches[1];
			unset($matches);
			
			$class = 'YaPhpDoc_Tag_'.ucfirst($tagname);
			if(!class_exists($class, false))
			{
				$loader = Zend_Loader_Autoloader::getInstance();
				if(!in_array('YaPhpDoc', $loader->getRegisteredNamespaces()))
				{
					throw new YaPhpDoc_Core_Exception(
						Ypd::getInstance()->getTranslation()->_(
						'Autoloader is not defined or configurated.'
					));
				}
				$loader->suppressNotFoundWarnings(true);
				if(!$loader->autoload($class))
				{
					$class = 'YaPhpDoc_Tag_Anonymous';
				}
				$loader->suppressNotFoundWarnings(false);
			}
		}
		else
			$class = 'YaPhpDoc_Tag_Anonymous';

		return new $class($tagline);
	}
}
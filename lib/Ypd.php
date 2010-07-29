<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Ypd is the base system class. It provides translation helpers and
 * store the configuration of the instance.
 * 
 * @author Martin Richard
 */
class Ypd
{
	/**
	 * Returns the array of cli options.
	 * @return array
	 */
	protected static function _getCliOptions()
	{
		return array(
			'verbose|v' 	=> 'Enable verbose output',
			'file|f=s'		=> 'Name of file(s) to parse, coma-separated',
			'directory|d=s'	=> 'Name of directory(ies) to parse, coma-separated',
			'help|h'		=> 'Display help summary',
		);
	}
	
	/**
	 * Returns an array of available locales
	 * @return array
	 */
	protected static function _getTranslations()
	{
		return array('en');
	}
	
	/**
	 * Singleton instance
	 * @var YaPhpDoc_Core_Ypd
	 */
	protected static $_instance;
	
	/**
	 * Getopt cli option parser object.
	 * @var Zend_Console_Getopt
	 */
	protected $_getopt;
	
	/**
	 * Locale code
	 * @var string
	 */
	protected $_locale = 'en';
	
	/**
	 * Translations object map
	 * @var Zend_Translate
	 */
	protected $_translation;
	
	/**
	 * Constructor is private, use getInstance() instead.
	 * 
	 * Try to find the current locale according to YPD_LOCALE constant and
	 * available translations.
	 */
	protected function __construct()
	{
		if(!defined('YPD_LOCALE') || !in_array(YPD_LOCALE, self::_getTranslations()))
			$this->_locale = 'en';
		else
			$this->_locale = YPD_LOCALE;
	}
	
	/**
	 * Return the Getopt object allowing to parse cli options.
	 * @return Zend_Console_Getopt
	 */
	public function getGetopt()
	{
		if(null === $this->_getopt)
		{
			$options = self::_getCliOptions();
			
			# Translate options
			$translator = $this->getTranslation('cli');
			foreach($options as $option => $desc)
				$options[$option] = $translator->_($desc);
			
			# Create getopt object
			$this->_getopt = new Zend_Console_Getopt($options);
		}
		
		return $this->_getopt;
	}
	
	/**
	 * Returns translate object for current $key dictionnary
	 * @param string $key (default core)
	 * @return Zend_Translate
	 */
	public function getTranslation($key = 'core')
	{
		$options = array('disableNotices' => true);
		if(null === $this->_translation)
		{
			$this->_translation = new Zend_Translate(
				'csv',
				YPD_ROOT.DIRECTORY_SEPARATOR.'l10n'.DIRECTORY_SEPARATOR.self::$this->_locale.DIRECTORY_SEPARATOR.$key.'.csv',
				$this->_locale,
				$options
			);
			$this->_translation->setLocale($this->_locale);
		}
		else
		{
			$this->_translation->addTranslation(
				YPD_ROOT.DIRECTORY_SEPARATOR.'l10n'.DIRECTORY_SEPARATOR.self::$this->_locale.DIRECTORY_SEPARATOR.$key.'.csv',
				$this->_locale, $options);
			
		}
		return $this->_translation;
	}
	
	/**
	 * Returns the Ypd instance
	 * @return Ypd
	 */
	public static function getInstance()
	{
		if(null === self::$_instance)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
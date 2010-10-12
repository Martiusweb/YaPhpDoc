<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * This class allow low-level classes to find a
 * YaPhpDoc_Core_TranslationManager_Interface object.
 * 
 * The selected TranslationManager must declare itself with
 * setTranslationManager() method or the getTranslation() method will
 * return the original string.
 * 
 * This class can not be instanciated.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Core_TranslationManager_Resolver
{
	/**
	 * TranslationManager object.
	 * @var YaPhpDoc_Core_TranslationManager_Interface
	 */
	private static $_translationManager;
	
	/**
	 * Allow the translation manager to declare itself.
	 * 
	 * @param YaPhpDoc_Core_TranslationManager_Interface $translationManager
	 * @return void
	 */
	public static function setTranslationManager(
		YaPhpDoc_Core_TranslationManager_Interface $translationManager)
	{
		self::$_translationManager = $translationManager;
	}
	
	/**
	 * Returns the translation if the translation manager is known.
	 * 
	 * @param string $string String to translate
	 * @param string $key	 Dictionnary (default to 'core')
	 * @return string
	 */
	public function getTranslation($string, $key = 'core')
	{
		if(self::$_translationManager === null)
			return $string;
		
		return self::$_translationManager->getTranslation($key)->_($string);
	}
	
	private function __construct()
	{
	}
}
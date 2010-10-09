<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Loader provides handy methods for dynamic resources (such as classes)
 * loading.
 * 
 * This class can not be instanciated.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tool_Loader
{
	/**
	 * Tests if the class $class exists. classExists() try to load the class
	 * using the Zend autoloader (in the YaPhpDoc namespace).
	 * 
	 * @param string $class
	 * @return bool
	 */
	public static function classExists($class)
	{
		if(!class_exists($class, false))
		{
			$loader = Zend_Loader_Autoloader::getInstance();
			if(!in_array('YaPhpDoc', $loader->getRegisteredNamespaces()))
			{
				throw new YaPhpDoc_Core_Exception(
					YaPhpDoc_Core_TranslationManager_Resolver::getTranslation(
					'Autoloader is not defined or configurated.'
				));
			}
			$loader->suppressNotFoundWarnings(true);
			$loading = $loader->autoload($class);
			$loader->suppressNotFoundWarnings(false);
			
			return $loading;
		}
		else
		{
			return true;
		}
	}
	
	private function __construct()
	{
	}
}
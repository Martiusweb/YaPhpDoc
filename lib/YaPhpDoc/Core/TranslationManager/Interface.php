<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * TranslationManager_Interface provide a standard interface for translation and
 * localization utilities.
 * 
 * @author Martin Richard
 */
interface YaPhpDoc_Core_TranslationManager_Interface
{
	/**
	 * Returns translate object for current $key dictionnary
	 * @param string $key (default core)
	 * @return Zend_Translate
	 */
	public function getTranslation($key = 'core');
}
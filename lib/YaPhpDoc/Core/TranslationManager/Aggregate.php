<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * An object that implements YaPhpDoc_Core_TranslationManager_Aggregate is able to
 * return a TranslationManager object.
 * 
 * @author Martin Richard
 */
interface YaPhpDoc_Core_TranslationManager_Aggregate
{
	/**
	 * Returns a translation manager.
	 * @return YaPhpDoc_Core_TranslationManager_Interface
	 */
	public function getTranslationManager();
	
	/**
	 * Proxy to getTranslationManager
	 * @return YaPhpDoc_Core_TranslationManager_Interface
	 */
	public function l10n();
}
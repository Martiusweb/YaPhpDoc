<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/../../../../util.php';
require_once 'YaPhpDoc/Core/TranslationManager/Interface.php';

class YaPhpDoc_Core_TranslationManager_InterfaceImpl
	implements YaPhpDoc_Core_TranslationManager_Interface
{
	protected static $_translate;
	public function getTranslation($key = 'core')
	{
		if(self::$_translate == null)
			self::$_translate = new Zend_Translate(array(
				'adapter'	=> 'array',
				'content'	=> array(),
				'locale'	=> 'en'
			));
		return self::$_translate;
	}
}
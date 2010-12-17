<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */
require_once __DIR__.'/../../../../util.php';
require_once 'YaPhpDoc/Core/TranslationManager/Aggregate.php';
require_once __DIR__.'/InterfaceImpl.php';

class YaPhpDoc_Core_TranslationManager_AggregateImpl
	implements YaPhpDoc_Core_TranslationManager_Aggregate
{
	public function getTranslationManager()
	{
		return new YaPhpDoc_Core_TranslationManager_InterfaceImpl();	
	}
	
	public function l10n()
	{
		return $this->getTranslationManager();
	}	
}

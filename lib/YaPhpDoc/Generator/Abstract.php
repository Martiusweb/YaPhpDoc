<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Abstract Generator. A concrete generator must inherits of this class and
 * implement these methods.
 *  
 * @author Martin Richard
 */
abstract class YaPhpDoc_Generator_Abstract implements YaPhpDoc_Core_OutputManager_Aggregate, YaPhpDoc_Core_TranslationManager_Aggregate
{
	/**
	 * Output manager object.
	 * @var YaPhpDoc_Core_OutputManager_Interface
	 */
	protected $_outputManager;
	
	/**
	 * Translation manager object
	 * @var YaPhpDoc_Core_TranslationManager_Interface
	 */
	protected $_translationManager;
	
	public function __construct(
		YaPhpDoc_Core_OutputManager_Interface $outputManager,
		YaPhpDoc_Core_TranslationManager_Interface $translationManager)
	{
		$this->_outputManager = $outputManager;
		$this->_translationManager = $translationManager;	
	}
	
	/*
	 * Implements YaPhpDoc_Core_OutputManager_Aggregate
	 */
	
	/**
	 * @see lib/YaPhpDoc/Core/OutputManager/YaPhpDoc_Core_OutputManager_Aggregate#getOutputManager()
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function getOutputManager()
	{
		return $this->_outputManager;
	}
	
	/**
	 * @see lib/YaPhpDoc/Core/OutputManager/YaPhpDoc_Core_OutputManager_Aggregate#out()
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function out()
	{
		return $this->getOutputManager();
	}
	
	/*
	 * Implements YaPhpDoc_Core_TranslationManager_Aggregate
	 */
	
	/**
	 * @see lib/YaPhpDoc/Core/TranslationManager/YaPhpDoc_Core_TranslationManager_Aggregate#getTranslationManager()
	 * @return YaPhpDoc_Core_TranslationManager_Interface
	 */
	public function getTranslationManager()
	{
		return $this->_translationManager;
	}
	
	/**
	 * @see lib/YaPhpDoc/Core/TranslationManager/YaPhpDoc_Core_TranslationManager_Aggregate#l10n()
	 * @return YaPhpDoc_Core_TranslationManager_Interface
	 */
	public function l10n()
	{
		return $this->getTranslationManager();
	}
}
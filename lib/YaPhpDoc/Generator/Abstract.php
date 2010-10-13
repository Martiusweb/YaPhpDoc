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
	 * Destination of the generated files. 
	 * @var string
	 */
	protected $_destination = '.';
	
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
	
	/**
	 * setDestination allows to specify the directory where generated files
	 * will be stored. If the directory does not exists, setDestination will
	 * try to create it.
	 * 
	 * If the directory is not writable, an exception is thrown.
	 * 
	 * @throws YaPhpDoc_Generator_Exception
	 * @param string $destination
	 * @return YaPhpDoc_Generator_Abstract
	 */
	public function setDestination($destination)
	{
		$writable = false;
		if(!is_dir($destination))
		{
			if($writable = mkdir($destination, 0755, true))
			{
				$this->out()->verbose(
					'Destination directory did not exist and has been created',
					true, 'generator');
			}
		}
		else
			$writable = is_writable($destination);
		 
		if(!$writable)
		{
			throw new YaPhpDoc_Generator_Exception(sprintf(
				$this->l10n()->getTranslation('generator')->_(
				'%s is not a writable directory'
				), $destination
			));
		}
		
		$this->_destination = $destination;
		return $this;
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
	
	/**
	 * Returns the name of the format, guessed according to the classname.
	 * @return string
	 */
	public function __toString()
	{
		return lcfirst(YaPhpDoc_Tool_Loader::getLocalClassname(get_class($this)));
	}
}
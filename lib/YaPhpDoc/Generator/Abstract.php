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
	 * Path to the data directory.
	 * @var string
	 */
	protected $_dataDir = '';
	
	/**
	 * Destination of the generated files. 
	 * @var string
	 */
	protected $_destination = '.';
	
	/**
	 * Generator configuration object.
	 * @var Zend_Config
	 */
	protected $_config;
	
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
		YaPhpDoc_Core_TranslationManager_Interface $translationManager,
		$data_dir)
	{
		$this->_outputManager = $outputManager;
		$this->_translationManager = $translationManager;
		$this->_dataDir = $data_dir;	
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
	
	/**
	 * set the configuration object.
	 * 
	 * @param Zend_Config $config
	 * @return YaPhpDoc_Generator_Abstract
	 */
	public function setConfig(Zend_Config $config)
	{
		$this->_config = $config;
		return $this;
	}
	
	/*
	 * Implements YaPhpDoc_Core_OutputManager_Aggregate
	 */
	
	/**
	 * @see YaPhpDoc/Core/OutputManager/YaPhpDoc_Core_OutputManager_Aggregate#getOutputManager()
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function getOutputManager()
	{
		return $this->_outputManager;
	}
	
	/**
	 * @see YaPhpDoc/Core/OutputManager/YaPhpDoc_Core_OutputManager_Aggregate#out()
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
	 * @see YaPhpDoc/Core/TranslationManager/YaPhpDoc_Core_TranslationManager_Aggregate#getTranslationManager()
	 * @return YaPhpDoc_Core_TranslationManager_Interface
	 */
	public function getTranslationManager()
	{
		return $this->_translationManager;
	}
	
	/**
	 * @see YaPhpDoc/Core/TranslationManager/YaPhpDoc_Core_TranslationManager_Aggregate#l10n()
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
	
	/**
	 * Write $content in the file $filename, created in the destination directory.
	 * Create directories under the destination if they don't exist.
	 * 
	 * Content is optionnal, allowing to shortcut this parameter for specifics
	 * generator implementation needs (using a library that already writes files
	 * for instance).
	 * 
	 * @param string $filename
	 * @param string $content (optionnal)
	 * @throws YaPhpDoc_Generator_Exception
	 * @return YaPhpDoc_Generator_Absctract
	 */
	protected function _write($filename, $content = '')
	{
		$filename = $this->_destination.DIRECTORY_SEPARATOR.$filename;
		
		$this->_mkDirIfNotExists(dirname($filename));
		
		if(!file_put_contents($filename, $content, LOCK_EX))
		{
			throw new YaPhpDoc_Generator_Exception(sprintf(
				$this->l10n('generator')->_('Unable to write file %s'), $filename
			));
		}
		
		return $this;
	}
	
	/**
	 * Creates a directory if this one does not exist. Directories are created
	 * recursively.
	 *  
	 * @param string $dirname Directory to create
	 * @param int $mode Mode of the directory (same as standard mkdir function), default to 755
	 * @throws YaPhpDoc_Generator_Exception
	 * @return YaPhpDoc_Generator_Abstract
	 */
	protected function _mkDirIfNotExists($dirname, $mode = 0755)
	{
		if(!is_dir($dirname))
		{
			if(!mkdir($dirname, $mode, true))
			{
				throw new YaPhpDoc_Generator_Exception(sprintf(
					$this->l10n('generator')->_('Unable to create directory %s'),
					$dirname
				));
			}
		}
		return $this;
	}
	
	/**
	 * Renders the documentation.
	 * @throws YaPhpDoc_Generator_Exception
	 * @return YaPhpDoc_Generator_Abstract
	 */
	public function render()
	{
		$this->_initialize();
		$this->_build();
		
		return $this;
	}
	
	/**
	 * Initializes the generator. This function must be overriden by the
	 * generator concrete class. There are no requirements, but no returned
	 * value is expected.
	 * @return void
	 */
	abstract protected function _initialize();
	
	/**
	 * Generates the documentation.
	 * 
	 * @throws YaPhpDoc_Generator_Exception
	 * @return void
	 */
	abstract protected function _build();
}
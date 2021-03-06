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
class Ypd implements YaPhpDoc_Core_OutputManager_Interface, YaPhpDoc_Core_TranslationManager_Interface
{
	/**
	 * Returns the array of cli options.
	 * @return array
	 */
	protected static function _getCliOptions()
	{
		return array(
			'file|f=s'				=> 'Name of file(s) to parse, coma-separated',
			'directory|d=s'			=> 'Name of directory(ies) to parse, coma-separated',
			'help|h'				=> 'Display help summary',
			'verbose|v' 			=> 'Enable verbose output',
			'disable-output|no'		=> 'Disable output',
			'disable-notice|nn'		=> 'Disable notices output',
			'disable-warning|nw'	=> 'Disable warning messages output',
			'disable-error|ne'		=> 'Disable fatal errors output',
			'list|l'				=> 'Don\'t parse, just display files to parse list',
			'exclude|e=s'			=> 'Exclude files matching the given Regex pattern',
			'include|i=s'			=> 'Include files matching the given Regex pattern',
			'output-format|out=s'	=> 'Select output format (default is the standard value)',
			'destination|o=s'		=> 'Destination of the generated files (default is working directory)',
			'config|c=s'			=> 'Configuration file'
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
	 * @var YaPhpDoc_Console_Getopt
	 */
	protected $_getopt;
	
	/**
	 * PHP Documents parser object.
	 * @var YaPhpDoc_Core_Parser
	 */
	protected $_parser;
	
	/**
	 * PHP Documentation generator object.
	 * @var YaPhpDoc_Generator_Abstract
	 */
	protected $_generator;
	
	/**
	 * Zend_Config object.
	 * @var Zend_Config
	 */
	protected $_config;
	
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
	 * Loaded translation dictionaries
	 * @var array
	 */
	protected $_loadedTranslations = array(); 
	
	/**
	 * True if output is enabled
	 * @var bool
	 */
	protected $_enableOutput = true;
	
	/**
	 * True if errors are displayed
	 * @var bool
	 */
	protected $_outputErrors = true;
	
	/**
	 * True if warning messages are displayed
	 * @var bool
	 */
	protected $_outputWarnings = true;
	
	/**
	 * True if notices are displayed
	 * @var bool
	 */
	protected $_outputNotices = true;
	
	/**
	 * True if verbose mode is enabled (default to false).
	 * @var bool
	 */
	protected $_verbose = false;
	
	/**
	 * Destination of the generated files.
	 * @var string
	 */
	protected $_destination = '';
	
	/**
	 * Path to data directory.
	 * @var string
	 */
	protected $_dataDir = '';
	/**
	 * Warning alerts counter
	 * @var int
	 */
	protected $_warnings = 0;
	
	/**
	 * Notices counter
	 * @var int
	 */
	protected $_notices = 0;
	
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
		
		$this->_parser = new YaPhpDoc_Core_Parser($this, $this);
		
		YaPhpDoc_Core_TranslationManager_Resolver::setTranslationManager($this);
	}
	
	/**
	 * Return the Getopt object allowing to parse cli options.
	 * @return YaPhpDoc_Console_Getopt
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
			$this->_getopt = new YaPhpDoc_Console_Getopt($options);
		}
		
		return $this->_getopt;
	}
	
	/**
	 * Returns the document parser object.
	 * @return YaPhpDoc_Core_Parser
	 */
	public function getParser()
	{
		return $this->_parser;
	}
	
	/**
	 * Returns the documentation generator. If the generator does not exists
	 * yet, getGenerator() will try to create a generator for "default" format.
	 * 
	 * @return YaPhpDoc_Generator_Abstract
	 */
	public function getGenerator()
	{
		if($this->_generator === null)
		{
			$this->setOutputFormat('default');
		}
		return $this->_generator;
	}
	
	/**
	 * Returns translate object for current $key dictionnary
	 * @param string $key (default core)
	 * @return Zend_Translate
	 */
	public function getTranslation($key = 'core')
	{
		$options = array(
			'delimiter' => ',',
			'disableNotices' => true
		);
		
		if(null === $this->_translation)
		{
			$this->_translation = new Zend_Translate(
				'csv',
				YPD_ROOT.DIRECTORY_SEPARATOR.'l10n'.DIRECTORY_SEPARATOR
				.self::$this->_locale.DIRECTORY_SEPARATOR.$key.'.csv',
				$this->_locale,
				$options
			);
			$this->_translation->setLocale($this->_locale);
			array_push($this->_loadedTranslations, $key);
		}
		elseif(!in_array($key, $this->_loadedTranslations))
		{
			$this->_translation->addTranslation(
				YPD_ROOT.DIRECTORY_SEPARATOR.'l10n'.DIRECTORY_SEPARATOR
				.self::$this->_locale.DIRECTORY_SEPARATOR.$key.'.csv',
				$this->_locale, $options);
			array_push($this->_loadedTranslations, $key);	
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
	
	/**
	 * Enable or disable output.
	 * 
	 * @param bool $flag default true
	 * @return Ypd
	 */
	public function setEnableOutput($flag = true)
	{
		$this->_enableOutput = $flag;
		return $this;
	}
	
	/**
	 * Enable or disable fatal errors.
	 * 
	 * @param bool $flag default true
	 * @return Ypd
	 */
	public function setOutputErrors($flag = true)
	{
		$this->_outputErrors = $flag;
		return $this;
	}
	
	/**
	 * Enable or disable fatal warning messages.
	 * 
	 * @param bool $flag default true
	 * @return Ypd
	 */
	public function setOutputWarnings($flag = true)
	{
		$this->_outputWarnings = $flag;
		return $this;
	}
	
	/**
	 * Enable or disable notices.
	 * 
	 * @param bool $flag default true
	 * @return Ypd
	 */
	public function setOutputNotices($flag = true)
	{
		$this->_outputNotices = $flag;
		return $this;
	}
	
	/**
	 * Enable or disable verbose mode.
	 * 
	 * @param bool $flag default true
	 * @return Ypd
	 */
	public function setVerbose($flag = true)
	{
		$this->_verbose = $flag;
		return $this;
	}
	
	/**
	 * Display a message to user.
	 * 
	 * @todo Support web interface
	 * @param string $message
	 * @param bool $linebreak optional, default to true, adds a trailing line-break
	 * @return Ypd
	 */
	public function out($message, $linebreak = true)
	{
		if($this->_enableOutput)
		{
			echo $message."\n";
		}
		return $this;
	}
	
	/**
	 * Send a fatal error and stops the program.
	 * 
	 * @todo Support web interface
	 * @param Exception|string $error
	 * @return void
	 */
	public function error($error)
	{
		$txt = $this->getTranslation()->_('Fatal error');
		if($this->_outputErrors)
		{
			if($error instanceof Exception)
				$this->out($txt.' : '.$error->getMessage());
			else
				$this->out($txt.' : '.$error);
		}
		exit();
	}
	
	/**
	 * Send a warning.
	 * 
	 * @param Exception|string $warning
	 * @return Ypd
	 */
	public function warning($warning)
	{
		$txt = $this->getTranslation()->_('Warning');
		if($this->_outputWarnings)
		{
			if($warning instanceof Exception)
				$this->out($txt.' : '.$warning->getMessage());
			else
				$this->out($txt.' : '.$warning);
		}
		++$this->_warning;
		return $this;
	}
	
	/**
	 * Send a notice.
	 * 
	 * @param Exception|string $notice
	 * @return Ypd
	 */
	public function notice($notice)
	{
		if($this->_outputNotices)
		{
			$txt = $this->getTranslation()->_('Notice');
			if($notice instanceof Exception)
				$this->out($txt.' : '.$notice->getMessage());
			else
				$this->out($txt.' : '.$notice);
		}
		++$this->_notices;
		return $this;
	}
	
	/**
	 * Display (if verbose mode is enabled) the message.
	 * 
	 * @param String $message
	 * @param bool $translate (optional, default true) Translate the message
	 * @param string $translation_key (optional, default core) Set the dictionnary to use for translation
	 * @return Ypd
	 */
	public function verbose($message, $translate = true, $translation_key = 'core')
	{
		if($this->_verbose)
		{
			if($translate)
				$message = $this->getTranslation($translation_key)->_($message);
			$this->out($message);
		}
		
		return $this;		
	}
	
	/**
	 * Returns the number of warning alerts caught during execution.
	 * @return int
	 */
	public function getWarningCount()
	{
		return $this->_warnings;
	}
	
	/**
	 * Returns the number of notices caught during execution.
	 * @return int
	 */
	public function getNoticeCount()
	{
		return $this->_notices;
	}
	
	/**
	 * Display an information when non-handled exception is caught, and exit
	 * the program.
	 * 
	 * @param Exception $e
	 * @return void
	 */
	public function phpException(Exception $e)
	{
		$this->out(
			'A fatal error had been received, if you think that it must '
			."not append, please contact developer with following :\n"
			.$e->getMessage()."\n"
			.$e->getTraceAsString()
		);
		
		exit();
	}
	
	/**
	 * Add file(s) to parse, coma separated.
	 * 
	 * @param string $file
	 * @return Ypd
	 */
	public function addFileFromOption($file)
	{
		if(!empty($file))
		{
			$file = explode(',', $file);
			if(count($file))
				$this->_parser->addFile($file);
		}
		return $this;
	}
	
	/**
	 * Add directory(ies) to parse, coma separated.
	 * 
	 * @param string $directory
	 * @return Ypd
	 */
	public function addDirectoryFromOption($directory)
	{
		if(!empty($directory))
		{
			$directory = explode(',', $directory);
			if(count($directory))
				$this->_parser->addDirectory($directory);
		}
		return $this;
	}
	
	/**
	 * Add paths to parse, in an array. Each path will be analyzed and tested
	 * in order to determine if the path matches an existing file or a
	 * directory.
	 * 
	 * @param array $paths
	 * @return Ypd
	 */
	public function addPathsFromOption(array $paths)
	{
		foreach($paths as $path)
		{
			if(is_file($path))
				$this->_parser->addFile($path);
			elseif(is_dir($path))
				$this->_parser->addDirectory($path);
			else
				$this->warning(sprintf($this->getTranslation()->_(
					'%s does not seem to be a file or a directory, ignoring'
				), $path));
		}
		return $this;
	}
	
	/**
	 * Set filenames include pattern from option.
	 * 
	 * @param string $pattern
	 * @return Ypd
	 */
	public function setIncludePatternFromOption($pattern)
	{
		$this->_parser->setIncludePattern($pattern);
		return $this;
	}
	
	/**
	 * Set filenames exclude pattern from option.
	 * 
	 * @param string $pattern
	 * @return Ypd
	 */
	public function setExcludePatternFromOption($pattern)
	{
		$this->_parser->setExcludePattern($pattern);
		return $this;
	}
	
	/**
	 * Set the documentation output format.
	 * @param string $format
	 * @return Ypd
	 */
	public function setOutputFormat($format)
	{
		$this->_generator = YaPhpDoc_Generator_Factory::getGenerator($format,
			$this, $this, $this->_dataDir);
		return $this;
	}
	
	/**
	 * Set the destination directory.
	 * @param string $dest
	 * @return Ypd
	 */
	public function setDestination($dest)
	{
		$this->_destination = $dest;
		return $this;
	}
	
	/**
	 * Set the data directory.
	 * 
	 * @param string $data_dir path to data directory
	 * @return Ypd
	 */
	public function setDataDirectory($data_dir)
	{
		$this->_dataDir = $data_dir;
		return $this;
	}
	
	/**
	 * Returns the data directory.
	 * @return string
	 */
	public function getDataDirectory()
	{
		return $this->_dataDir;
	}
	
	/**
	 * Loads configuration according to config_file.
	 * Formats supported are listed in the YaPhpDoc_Tool_Config::load() method
	 * documentation.
	 * 
	 * If $merge is given, the file will be used as default configuration file.
	 * 
	 * @param string $config_file
	 * @param string $merge base file to merge with
	 * @throws YaPhpDoc_Core_Exception
	 * @return Ypd
	 */
	public function setConfig($config_file, $merge = null)
	{
		try {
			if($merge == null)
				$this->_config = YaPhpDoc_Tool_Config::load($config_file);
			else
				$this->_config = YaPhpDoc_Tool_Config::merge($merge, $config_file);
		}
		catch(Zend_Config_Exception $e)
		{
			throw new YaPhpDoc_Core_Exception(sprintf($this->getTranslation()
				->_('Unable to load configuration file %s'), $config_file));
		}
		return $this;
	}
		
	/**
	 * Display the list of file to be parsed.
	 * @return Ypd
	 */
	public function outputFilesToParse()
	{
		$this->out($this->getTranslation()->_('Files to parse :'));
		foreach($this->getParser()->getFilesToParse() as $file)
			$this->out($file);
		return $this;
	}
	
	/**
	 * Start parsing.
	 * 
	 * @return Ypd
	 */
	public function parse()
	{
		$this->verbose('Start parsing');
		
		$timer = new YaPhpDoc_Tool_Timer();
		$timer->start();
		
		$e = null;
		try {
			$this->_parser->setConfig($this->_config->parser)->parseAll();
		}
		catch(Exception $e)
		{}
		
		$timer->stop();
		
		$msg = sprintf($this->getTranslation()
			->_('Stop parsing (time : %.4f sec, memory: %s, max: %s)'), $timer->getTimeUsage(),
			$timer->getMemoryUsage(), $timer->getMemoryPeak()
		);
		$this->verbose($msg, false);
		
		if(null !== $e)
			throw $e;
		
		return $this;
	}
	
	/**
	 * Start documentation generation.
	 * 
	 * @return Ypd
	 */
	public function generate()
	{
		$msg = sprintf($this->getTranslation()
			->_('Start generation of documentation in format %s'), $this->_generator);
		$this->verbose($msg, false);
		
		$this->getGenerator()
			->setDestination($this->_destination)
			->setConfig($this->_config->generator)
			->setRoot($this->getParser()->getRoot())
			->render();
		
		return $this;
	}
}

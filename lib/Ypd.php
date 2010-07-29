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
class Ypd
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
	 * @var Zend_Console_Getopt
	 */
	protected $_getopt;
	
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
	}
	
	/**
	 * Return the Getopt object allowing to parse cli options.
	 * @return Zend_Console_Getopt
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
			$this->_getopt = new Zend_Console_Getopt($options);
		}
		
		return $this->_getopt;
	}
	
	/**
	 * Returns translate object for current $key dictionnary
	 * @param string $key (default core)
	 * @return Zend_Translate
	 */
	public function getTranslation($key = 'core')
	{
		$options = array('disableNotices' => true);
		if(null === $this->_translation)
		{
			$this->_translation = new Zend_Translate(
				'csv',
				YPD_ROOT.DIRECTORY_SEPARATOR.'l10n'.DIRECTORY_SEPARATOR.self::$this->_locale.DIRECTORY_SEPARATOR.$key.'.csv',
				$this->_locale,
				$options
			);
			$this->_translation->setLocale($this->_locale);
		}
		else
		{
			$this->_translation->addTranslation(
				YPD_ROOT.DIRECTORY_SEPARATOR.'l10n'.DIRECTORY_SEPARATOR.self::$this->_locale.DIRECTORY_SEPARATOR.$key.'.csv',
				$this->_locale, $options);
			
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
		return $this;
	}
	
	/**
	 * Display (if verbose mode isenabled) the message.
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
}
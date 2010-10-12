<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Documents parser, parses each file using token_get_all().
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Core_Parser implements YaPhpDoc_Core_OutputManager_Aggregate, YaPhpDoc_Core_TranslationManager_Aggregate
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
	
	/**
	 * Directories to explore stack
	 * @var array
	 */
	protected $_directories = array();
	
	/**
	 * Files to parse stack
	 * @var array
	 */
	protected $_files = array();
	
	/**
	 * Only files matching this pattern will be parsed
	 * @var string
	 */
	protected $_includePattern;
	
	/**
	 * Files matching this pattern will be excluded
	 * @var string
	 */
	protected $_excludePattern;
	
	/**
	 * Root element
	 * @var YaPhpDoc_Token_Document
	 */
	protected $_root;
	
	/**
	 * Currently parsed file.
	 * @var string|NULL
	 */
	protected $_current_file;
	
	/**
	 * Current namespace.
	 * @var string
	 */
	protected $_current_namespace = '';
	
	/**
	 * Current package.
	 * @var string
	 */
	protected $_current_package = '';
	
	/**
	 * Current class.
	 * @var string
	 */
	protected $_current_class;
	
	/**
	 * True if the next element is final.
	 * @var bool
	 */
	protected $_final = false;
	
	/**
	 * True if the next element is abstract.
	 * @var bool
	 */
	protected $_abstract = false;
	
	/**
	 * True if the next element is static.
	 * @var bool
	 */
	protected $_static = false;
	
	/**
	 * True if the next element is public.
	 * @var bool
	 */
	protected $_public = false;
	
	/**
	 * True if the next element is protected.
	 * @var bool
	 */
	protected $_protected = false;
	
	/**
	 * True if the next element is private.
	 * @var bool
	 */
	protected $_private = false;
	
	/**
	 * Constructor of the parser.
	 * 
	 * @param YaPhpDoc_Core_OutputManager_Interface $ouputManager
	 * @param YaPhpDoc_Core_TranslationManager_Interface $translationManager
	 * @param string|array	$dirs  directory(ies) to explore - optionnal
	 * @param string|array	$files file(s) to parse - optionnal
	 * @param string		$include_pattern Pattern of files to parse - optionnal
	 * @param string		$exlude_pattern  Pattern of files to exclude - optionnal
	 */
	public function __construct(YaPhpDoc_Core_OutputManager_Interface $ouputManager, YaPhpDoc_Core_TranslationManager_Interface $translationManager, $dirs = null, $files = null, $include_pattern = null, $exclude_pattern = null)
	{
		$this->_outputManager = $ouputManager;
		$this->_translationManager = $translationManager;
		if(null !== $dirs)
			$this->addDirectory($dirs);
		if(null !== $files)
			$this->addFile($files);
		if(null !== $include_pattern)
			$this->_includePattern = $include_pattern;
		else
			$this->_includePattern = '.*\.(php[3-5]?|phtml|phps)$';
		if(null !== $exclude_pattern)
			$this->_excludePattern = $exclude_pattern;
		else
		{
			$ds = str_replace('\\', '\\\\', DIRECTORY_SEPARATOR);
			$this->_excludePattern = '('.$ds.'|^)\.[^'.$ds.']*$';
		}
	}
	
	/**
	 * Adds a directory to explore.
	 * @param string|array $dir directory(ies) to explore
	 * @return YaPhpDoc_Core_Parser 
	 */
	public function addDirectory($dir)
	{
		if(is_array($dir))
			$this->_directories = array_merge($this->_directories, $dir);
		else
			array_push($this->_directories, $dir);
		
		return $this;
	}
	
	/**
	 * Adds a file to parse.
	 * @param string|array $file file(s) to parse
	 * @return YaPhpDoc_Core_Parser 
	 */
	public function addFile($file)
	{
		if(is_array($file))
			$this->_files = array_merge($this->_files, $file);
		else
			array_push($this->_files, $file);
		
		return $this;
	}
	
	/**
	 * Sets the pattern of included files.
	 * 
	 * The file (absolute or relative path and name of the file) following
	 * this pattern will be included in parsing
	 * 
	 * @param string $pattern
	 * @return YaPhpDoc_Core_Parser
	 */
	public function setIncludePattern($pattern)
	{
		if(($error = $this->_testRegex($pattern)) !== false)
		{
			throw new YaPhpDoc_Core_Parser_Exception(sprintf(
				$this->l10n()->getTranslation('parser')
				->_('Wrong Include Pattern : %s')
			, $error));
		}
		
		$this->_includePattern = $pattern;
		return $this;
	}

	/**
	 * Sets the pattern of excluded files.
	 * 
	 * The file (absolute or relative path and name of the file) following
	 * this pattern will be excluded of parsing. This pattern is prior to
	 * include pattern.
	 * 
	 * @param string $pattern
	 * @return YaPhpDoc_Core_Parser
	 */	
	public function setExcludePattern($pattern)
	{
	if(($error = $this->_testRegex($pattern)) !== false)
		{
			throw new YaPhpDoc_Core_Parser_Exception(sprintf(
				$this->l10n()->getTranslation('parser')
				->_('Wrong Exclude Pattern : %s')
			, $error));
		}
		
		$this->_excludePattern = $pattern;
		return $this;
	}
	
	/**
	 * Returns an array of filenames that the object will
	 * parse.
	 * @return array
	 */
	public function getFilesToParse()
	{
		$files = array();
		$this->_files = array_unique($this->_files);
		foreach($this->_files as $file)
		{
			if($this->_isFilenameToParse($file))
				array_push($files, $file);
		}
		
		$this->_directories = array_unique($this->_directories);
		foreach($this->_directories as $dir)
		{
			$files = array_merge(
				$files,
				$this->_getFilesToParseInDir($dir)
			);
		}
		return $files;
	}
	
	/**
	 * Explore recursivly directories and find files to parse. Hidden
	 * directories (begining with ".") are excluded.
	 * 
	 * @param string $dirname
	 * @return array
	 */
	protected function _getFilesToParseInDir($dirname)
	{
		$files = array();
		
		if(!$this->_isHidden($dirname))
		{
			if(!($dir = @opendir($dirname)))
			{
				throw new YaPhpDoc_Core_Parser_Exception(
					sprintf($this->l10n()->getTranslation('parser')
						->_('Directory %s is not readable'),  $dirname)
				);
			}
			
			while(false !== ($current = readdir($dir)))
			{
				if($current == '.' || $current == '..')
					continue;
				
				$current = $dirname.'/'.$current;
				if(is_dir($current))
				{
					$files = array_merge(
						$files,
						$this->_getFilesToParseInDir($current)
					);
				}
				elseif($this->_isFilenameToParse($current))
				{
					array_push($files, $current);
				}
			}
			
			closedir($dir);
		}

		return $files;
	}
	/**
	 * Tests if the file matches the include pattern and does
	 * not matchs exclude pattern.
	 * @param string $filename
	 * @return bool
	 */
	protected function _isFilenameToParse($filename)
	{
		# TODO Refactor and use fnmatch instead (PHP 5.3.0 only on windows)
		# To include ?
		return (empty($this->_includePattern) || 
				preg_match('`'.$this->_includePattern.'`', $filename))
		# To exclude ?
		 	&& (empty($this->_excludePattern) || 
			!preg_match('`'.$this->_excludePattern.'`', $filename));
	}
	
	/**
	 * Returns true if the directory is hidden (starts with a ".").
	 * 
	 * @param string $dirname
	 * @return bool
	 */
	protected function _isHidden($dirname)
	{
		$dirname = substr($dirname, strrpos($dirname, DIRECTORY_SEPARATOR)+1);
		return $dirname[0] == '.';
	}
	
	/**
	 * Parses the selected code snippets and generate the documentation tokens
	 * tree.
	 * 
	 * @return YaPhpDoc_Core_Parser
	 */
	public function parseAll()
	{
		$files = $this->getFilesToParse();
		
		if(empty($files))
		{
			throw new YaPhpDoc_Core_Parser_Exception(
				$this->l10n()->getTranslation('parser')
				->_('There is no source to parse.')
			);
		}
		
		# Create the root node
		$this->_root = new YaPhpDoc_Token_Document($this);
		
		# Start parsing
		foreach($files as $file)
		{
			$this->out()->verbose(sprintf($this->l10n()
				->getTranslation('parser')->_('Parsing %s'), $file),
			false);
			
			$this->_current_file = $file;
			$this->_parseFile($file);
		}
		$this->_current_file = null;
		
		return $this;
	}
	
	/**
	 * Parses the file given in parameter.
	 *  
	 * @param string $filename
	 * @return YaPhpDoc_Core_Parser
	 */
	protected function _parseFile($filename)
	{
		$file_content = null;
		if(is_readable($filename))
		{
			$file_content = file_get_contents($filename);
		}
		if(null === $file_content)
		{
			throw new YaPhpDoc_Core_Parser_Exception(
				sprintf($this->l10n()->getTranslation('parser')
				->_('Can not read file %s'), $file
			));
		}
		
		# New file
		$file = new YaPhpDoc_Token_File($filename, $this->_root);
		$this->_root->addChild($file);
		
		# Parse file content
		$tokens = new YaPhpDoc_Tokenizer($file_content);
		$tokensIterator = $tokens->getIterator();
		
		$inPhp = false;
		if($tokensIterator->valid())
		{
			$file->parse($tokensIterator);
		}
		
		return $this;
	}
	
	/**
	 * Rerturns the regex compilation error or false if the Regex pattern seems
	 * to be working.
	 * 
	 * @param string $pattern
	 * @return bool
	 */
	protected function _testRegex($pattern)
	{
		if(@preg_match('`'.$pattern.'`', '') === false)
		{
			$error = error_get_last();
			return substr($error['message'], 14);
		}
		else
			return false;
	}
	
	/*
	 * Implements  @return YaPhpDoc_Core_OutputManager_Aggregate
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
	 * Returns the root element, can be null if the parser has not been
	 * executed yet.
	 * 
	 * @return YaPhpDoc_Token_Document
	 */
	public function getRoot()
	{
		return $this->_root;
	}
	
	/**
	 * Returns currently parsed file.
	 * @return string|NULL
	 */
	public function getCurrentFile()
	{
		return $this->_current_file;
	}
	
	/**
	 * Returns current namespace.
	 * @return string
	 */
	public function getCurrentNamespace()
	{
		return $this->_current_namespace;
	}
	
	/**
	 * Returns current package.
	 * @return string
	 */
	public function getCurrentPackage()
	{
		return $this->_current_package;
	}
	
	/**
	 * Returns current class.
	 * @return string|NULL
	 */
	public function getCurrentClass()
	{
		return $this->_current_class;
	}
	
	/**
	 * Sets the Final flag.
	 * @param bool $flag (optional, default to true)
	 * @return bool
	 */
	public function setFinal($flag = true)
	{
		$this->_final = $flag;
	}
	
	/**
	 * Returns true if the next element is final.
	 * If $clear is true, the flag will be set to false.
	 * 
	 * @param bool $clear (optional, default to true)
	 * @return bool
	 */
	public function isFinal($clear = true)
	{
		$final = $this->_final;
		if($clear)
			$this->_final = false;
		return $final;
	}
	
	/**
	 * Sets the Abstract flag.
	 * @param bool $flag (optional, default to true)
	 * @return YaPhpDoc_Core_Parser
	 */
	public function setAbstract($flag = true)
	{
		$this->_abstract = $flag;
		return $this;
	}
	
	/**
	 * Returns true if the next element is abstract.
	 * If $clear is true, the flag will be set to false.
	 * 
	 * @param bool $clear (optional, default to true)
	 * @return bool
	 */
	public function isAbstract($clear = true)
	{
		$abstract = $this->_abstract;
		if($clear)
			$this->_abstract = false;
		return $abstract;
	}

	/**
	 * Sets the Static flag.
	 * @param bool $flag (optional, default to true)
	 * @return YaPhpDoc_Core_Parser
	 */
	public function setStatic($flag = true)
	{
		$this->_static = $flag;
		return $this;
	}
	
	/**
	 * Returns true if the next element is static.
	 * If $clear is true, the flag will be set to false.
	 * 
	 * @param bool $clear (optional, default to true)
	 * @return bool
	 */
	public function isStatic($clear = true)
	{
		$static = $this->_static;
		if($clear)
			$this->_static = false;
		return $static;
	}
	
	/**
	 * Sets the Public flag.
	 * @param bool $flag (optional, default to true)
	 * @return YaPhpDoc_Core_Parser
	 */
	public function setPublic($flag = true)
	{
		$this->_public = true;
		return $this;
	}
	
	/**
	 * Returns true if the next element is public.
	 * If $clear is true, the flag will be set to false.
	 * 
	 * @param bool $clear (optional, default to true)
	 * @return bool
	 */
	public function isPublic($clear = true)
	{
		$public = $this->_public;
		if($clear)
			$this->_public = false;
		return $public;
	}
	
	/**
	 * Sets the Protected flag.
	 * @param bool $flag (optional, default to true)
	 * @return YaPhpDoc_Core_Parser
	 */
	public function setProtected($flag = true)
	{
		$this->_protected = true;
		return $this;
	}
	
	/**
	 * Returns true if the next element is protected.
	 * If $clear is true, the flag will be set to false.
	 * 
	 * @param bool $clear (optional, default to true)
	 * @return bool
	 */
	public function isProtected($clear = true)
	{
		$protected = $this->_protected;
		if($clear)
			$this->_protected = false;
		return $protected;
	}
	
	/**
	 * Sets the Private flag.
	 * @param bool $flag (optional, default to true)
	 * @return YaPhpDoc_Core_Parser
	 */
	public function setPrivate($flag = true)
	{
		$this->_private = true;
		return $this;
	}
	
	/**
	 * Returns true if the next element is private.
	 * If $clear is true, the flag will be set to false.
	 * 
	 * @param bool $clear (optional, default to true)
	 * @return bool
	 */
	public function isPrivate($clear = true)
	{
		$private = $this->_private;
		if($clear)
			$this->_private = false;
		return $private;
	}
}
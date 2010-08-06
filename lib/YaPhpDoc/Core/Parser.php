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
class YaPhpDoc_Core_Parser
{
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
	protected $_includePattern = '.*\.(php[3-5]?|phtml|phps)';
	
	/**
	 * Files matching this pattern will be excluded
	 * @var string
	 */
	protected $_excludePattern = '';
	
	/**
	 * Constructor of the parser.
	 * 
	 * @param string|array	$dirs  directory(ies) to explore - optionnal
	 * @param string|array	$files file(s) to parse - optionnal
	 * @param string		$include_pattern Pattern of files to parse - optionnal
	 * @param string		$exlude_pattern  Pattern of files to exclude - optionnal
	 */
	public function __construct($dirs = null, $files = null, $include_pattern = null, $exclude_pattern = null)
	{
		if(null !== $dirs)
			$this->addDirectory($dirs);
		if(null !== $files)
			$this->addFile($files);
		if(null !== $include_pattern)
			$this->_includePattern = $include_pattern;
		if(null !== $exclude_pattern)
			$this->_excludePattern = $exclude_pattern;
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
				Ypd::getInstance()->getTranslation('parser')
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
				Ypd::getInstance()->getTranslation('parser')
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
	 * Explore recursivly directories and find files to parse.
	 * @param string $dirname
	 * @return array
	 */
	protected function _getFilesToParseInDir($dirname)
	{
		$files = array();
		
		if(!($dir = @opendir($dirname)))
		{
			throw new YaPhpDoc_Core_Parser_Exception(
				sprintf(Ypd::getInstance()->getTranslation('parser')
					->_('Directory %s is not readable'),  $dirname)
			);
		}
		
		while(false !== ($current = readdir($dir)))
		{
			if($current == '.' || $current == '..')
				continue;
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
		# To include ?
		return (empty($this->_includePattern) || 
				preg_match('`'.$this->_includePattern.'`', $filename))
		# To exclude ?
		 	&& (empty($this->_excludePattern) || 
			!preg_match('`'.$this->_excludePattern.'`', $filename));
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
				Ypd::getInstance()->getTranslation('parser')
				->_('There is no source to parse.')
			);
		}
		
		# TODO parser
		throw new YaPhpDoc_Core_Exception(
			Ypd::getInstance()->getTranslation()
			->_('YaPhpDoc does not support this feature yet.')
		);
		return $this;
	}
	
	/**
	 * Parses the array of file lines.
	 *  
	 * @param array $file
	 * @return YaPhpDoc_Core_Parser
	 */
	protected function _parseFile(array $file)
	{
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
}
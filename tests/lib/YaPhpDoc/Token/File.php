<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/../../../util.php';
require_once 'YaPhpDoc/Token/Abstract.php';
require_once 'YaPhpDoc/Token/Structure/Abstract.php';
require_once 'YaPhpDoc/Token/File.php';

/**
 * Stub class for YaPhpDoc_Token_Abstract
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_FileStub extends YaPhpDoc_Token_File
{
	public $curent_file;
	/**
	 * Shortcut parsing
	 * @see lib/YaPhpDoc/Token/YaPhpDoc_Token_File::parse()
	 * 
	 * @return YaPhpDoc_Token_File
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$this->current_file = $this->getParser()->getCurrentFile();
		return $this;
	}
}
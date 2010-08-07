<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a PHP file.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_File extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * File constructor.
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		parent::__construct($filename, YaPhpDoc_Token_Abstract::FILE);
	}
	
	/**
	 * Parse a PHP file.
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_File
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->next();
			/* @var $token YaPhpDoc_Tokenizer_Token */
			
			if($token->isDocBlock())
			{
				# TODO File parser
			}
		}
		
		return $this;
	}
}
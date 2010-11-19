<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Implements the decorator for files for the HTML format.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Html_File
	extends YaPhpDoc_Generator_Decorator_Html_Token
{
	/**
	 * Returns an URL of the html page containing the documentation of the
	 * file.
	 * 
	 * @param YaPhpDoc_Token_File $token
	 * @return string
	 */
	public function getUrl(YaPhpDoc_Token_File $token)
	{
		# Normalize directory separator to "/"
		$name = $token->getFilename();
		if(DIRECTORY_SEPARATOR != '/')
			$name = str_replace(DIRECTORY_SEPARATOR, '/', $name);
		
		# Normalize special characters
		$name = preg_replace(
			array('`[^a-zA-Z0-9/]+`', '`\s+`'),
			array('_', '-'),
			$name);
		
		# Find URL
		if($name[0] == '/')
			return 'files'.$name.'.html';
		else
			return 'files/'.$name.'.html';
	}
}
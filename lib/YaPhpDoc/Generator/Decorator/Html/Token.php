<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Implements the decorator for any tokens for the HTML format.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Html_Token
	extends YaPhpDoc_Generator_Decorator_Modifier
{
	/**
	 * Returns an URL of the html page containing the documentation of the
	 * token.
	 * 
	 * @param YaPhpDoc_Token_Abstract $token
	 * @return string
	 */
	public function getUrl(YaPhpDoc_Token_Abstract $token)
	{
		// TODO
		return $token->getName();
	}
}
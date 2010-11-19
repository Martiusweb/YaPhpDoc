<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Implements the decorator for namespaces for the HTML format.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Decorator_Html_Namespace
	extends YaPhpDoc_Generator_Decorator_Html_Token
{
	/**
	 * Returns an URL of the html page containing the documentation of the
	 * namespace.
	 * 
	 * @param YaPhpDoc_Token_Namespace $token
	 * @return string
	 */
	public function getUrl(YaPhpDoc_Token_Namespace $token)
	{
		return 'namespaces/'
			.str_replace('\\', '/', $token->getName())
			.'.html';
	}
}
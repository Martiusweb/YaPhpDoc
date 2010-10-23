<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Exception used to stop parsing.
 * You may not use this exception alone but call
 * YaPhpDoc_Token_Abstract#_breakParser().
 * 
 * @see YaPhpDoc_Token_Abstract#_breakParser() 
 * @author Martin Richard
 */
class YaPhpDoc_Core_Parser_Break_Exception extends YaPhpDoc_Core_Parser_Exception
{
}
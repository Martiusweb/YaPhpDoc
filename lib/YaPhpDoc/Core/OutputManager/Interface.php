<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * OutputManager_Interface provide a standard interface for output messages to
 * user.
 * 
 * @author Martin Richard
 */
interface YaPhpDoc_Core_OutputManager_Interface
{
	/**
	 * Display a message to user.
	 * 
	 * @param string $message
	 * @param bool $linebreak optional, default to true, adds a trailing line-break
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function out($message, $linebreak = true);
	
	/**
	 * Send a fatal error and stops the program.
	 * 
	 * @param Exception|string $error
	 * @return void
	 */
	public function error($error);
	
	/**
	 * Send a warning.
	 * 
	 * @param Exception|string $warning
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function warning($warning);
	
	/**
	 * Send a notice.
	 * 
	 * @param Exception|string $notice
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function notice($notice);
	
	/**
	 * Display (if verbose mode is enabled) the message.
	 * 
	 * @param String $message
	 * @param bool $translate (optional, default true) Translate the message
	 * @param string $translation_key (optional, default core) Set the dictionnary to use for translation
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function verbose($message, $translate = true, $translation_key = 'core');
}
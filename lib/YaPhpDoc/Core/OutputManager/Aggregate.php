<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * An object that implements YaPhpDoc_Core_OutputManager_Aggregate is able to
 * return an OutputManager object.
 * 
 * @author Martin Richard
 */
interface YaPhpDoc_Core_OutputManager_Aggregate
{
	/**
	 * Returns an output manager.
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function getOutputManager();
	
	/**
	 * Proxy to getOutputManager();
	 * @return YaPhpDoc_Core_OutputManager_Interface
	 */
	public function out();
}
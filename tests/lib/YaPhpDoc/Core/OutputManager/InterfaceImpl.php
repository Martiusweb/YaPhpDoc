<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/../../../../util.php';
require_once 'YaPhpDoc/Core/OutputManager/Interface.php';

class YaPhpDoc_Core_OutputManager_InterfaceImpl
	implements YaPhpDoc_Core_OutputManager_Interface
{
	protected $_fatal = false;
	
	public function out($message, $linebreak = true) {
		return $this;
	}
	
	public function error($error) {
		$this->_fatal = true;
	}
	
	public function warning($warning) {
		return $this;
	}
	
	public function notice($notice) {
		return $this;
	}
	

	public function verbose($message, $translate = true, $translation_key = 'core')
	{
		return $this;
	}
	
	/**
	 * Returns true if error() had been called.
	 * @return bool
	 */
	public function hasFatal()
	{
		return $this->_fatal;
	}
}
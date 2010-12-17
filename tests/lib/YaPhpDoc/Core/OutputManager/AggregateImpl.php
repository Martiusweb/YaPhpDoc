<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/../../../../util.php';
require_once 'YaPhpDoc/Core/OutputManager/Aggregate.php';
require_once __DIR__.'/InterfaceImpl.php';

class YaPhpDoc_Core_OutputManager_AggregateImpl implements
	YaPhpDoc_Core_OutputManager_Aggregate
{
	protected $_outputManager;
	public function getOutputManager()
	{
		if(null == $this->_outputManager)
			$this->_outputManager = new YaPhpDoc_Core_OutputManager_InterfaceImpl();
		return $this->_outputManager;
	}
	
	public function out()
	{
		return $this->getOutputManager();
	}
}
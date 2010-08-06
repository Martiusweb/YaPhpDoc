<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Extends the Zend_Getopt class and some specific features for
 * our needs.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Console_Getopt extends Zend_Console_Getopt
{
	/**
	 * Extends the Usage message in order to add contextualized messages.
	 * @return string
	 */
	public function getUsageMessage()
	{
		$usage = Ypd::getInstance()->getTranslation('cli')->_(
			"YaPhpDoc : Yet Another PHP Documentator\n"
			."By Martin Richard - www.martiusweb.net\n\n"
			."Usage : YaPhpDoc [path] ... [options]\n\n"
			."Options are :"
		);
		
		$parent_usage = parent::getUsageMessage();
		$usage .= "\n".substr($parent_usage, strpos($parent_usage, "\n"));
		
		return $usage;
	}
	
	/**
	 * Returns non parsed arguments, which is considered as a path to
	 * parse.
	 * 
	 * @return array
	 */
	public function getOtherPaths()
	{
		return $this->getRemainingArgs();
	}
}
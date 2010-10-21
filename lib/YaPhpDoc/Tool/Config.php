<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Loader provides handy methods for dynamic resources (such as classes)
 * loading.
 * 
 * This class can not be instanciated.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tool_Config
{
	/**
	 * Returns the Zend_Config object according to the $config_file type or $type.
	 * If $config_file is an array, this array is used as configuration data, else
	 * if $type is null or not given, the method will try to gess the file type
	 * according to its extension (xml, yml or yaml, json, ini).
	 * 
	 * Yaml and Json are only available with Zend Framework 1.11+.
	 * 
	 * @param string|array $config_file
	 * @param string $type optional
	 * @param bool $writable optional, default to false
	 * @throws Zend_Config_Exception
	 * @return Zend_Config|NULL
	 */
	public static function load($config_file, $type = null, $writable = false)
	{
		# Guess the right adapter according to $config_file type
		if(is_array($config_file))
		{
			return new Zend_Config($config_file, $writable);
		}
		else
		{
			if($type === null)
			{
				# Guess according to file extension
				$type = pathinfo($config_file, PATHINFO_EXTENSION);
			}
			
			$options = array('allowModifications' => $writable);
			
			switch($type)
			{
				case 'xml':
					return new Zend_Config_Xml($config_file, null, $options);
					break;
				case 'yml':
//				case 'yaml':
//					return new Zend_Config_Yaml($config_file);
//					break;
//				case 'json':
//					return new Zend_Config_Json($config_file);
//					break;
				case 'ini':
					return new Zend_Config_Ini($config_file, null, $options);
					break;
			}
		}
		
		return null;
	}
	
	/**
	 * Merge $base and $config_file (the second overrides the first).
	 * $base can be a Zend_Config object of a file to load.
	 * 
	 * @param Zend_Config $base
	 * @param string $config_file
	 * @param string $type optional
	 * @return Zend_Config
	 */
	public static function merge($base, $config_file, $type = null)
	{
		if(!$base instanceof Zend_Config)
		{
			$base = self::load($base, $base_type, true);
		}
		$base->merge(self::load($config_file, $type))->setReadOnly();
		return $base;
	}
	
	private function __construct()
	{}
}
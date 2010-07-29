#!/usr/bin/php
<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

# Configuration options
define('YPD_LOCALE', 'en');

# Adds lib directory to include Path
define('YPD_ROOT', dirname(__FILE__));
set_include_path(
	get_include_path().PATH_SEPARATOR.
	YPD_ROOT.DIRECTORY_SEPARATOR.'lib'
);

# Prepare autoloading
require 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace(array('YaPhpDoc', 'Ypd'));

# Parse cli options
$options = Ypd::getInstance()->getGetopt();

try
{
	$options->parse();
	
	if($options->getOption('help'))
	{
		echo $options->getUsageMessage();
		exit();
	}
}
catch(Zend_Console_Getopt_Exception $e)
{
	echo $e->getUsageMessage();
	exit();
}
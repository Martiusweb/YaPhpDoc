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

# utility classes
$ypd = Ypd::getInstance();
	
# Parse cli options
$options = $ypd->getGetopt();

try
{
	$options->parse();
	
	if($options->getOption('help'))
	{
		echo $options->getUsageMessage();
		exit();
	}
	
	# Output options
	if($options->getOption('disable-output'))
		$ypd->setEnableOutput(false);
	if($options->getOption('verbose'))
	{
		$ypd->setVerbose();
		$ypd->verbose('Verbose mode is enabled');
	}
	if($options->getOption('disable-error'))
		$ypd->setOutputErrors(false);
	if($options->getOption('disable-warning'))
		$ypd->setOutputWarnings(false);
	if($options->getOption('disable-notice'))
		$ypd->setOutputNotices(false);
	
}
catch(Zend_Console_Getopt_Exception $e)
{
	echo $e->getUsageMessage();
	exit();
}

try
{
	throw new YaPhpDoc_Core_Exception(
		$ypd->getTranslation()
			->_('YaPhpDoc does not support this feature yet.')
	);
}
catch(YaPhpDoc_Core_Exception $e)
{
	$ypd->error($e);
}
catch(Exception $e)
{
	$ypd->phpException($e);
}
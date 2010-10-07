<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 * 
 * Some functions and constants which can be useful for run unit tests.
 */

if(version_compare('5.3.0', phpversion(), '>='))
{
	trigger_error('YaPhpDoc Test Suite requires PHP 5.3 or later', E_USER_ERROR);
}

/**
 * Base path of the lib directory
 * @var string
 */
define('YPDDIR', __DIR__.'/../lib');

# Add YPDDIR to include_path
set_include_path(get_include_path() . PATH_SEPARATOR . YPDDIR);
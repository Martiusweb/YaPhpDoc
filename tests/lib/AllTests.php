<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/YaPhpDoc/AllTests.php';

PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(__DIR__, '.php');

/**
 * Test suite for YaPhpDoc library
 * 
 * @author Martin Richard
 */
class lib_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('lib_YaPhpDoc');
		$suite->addTestSuite(lib_YaPhpDoc_AllTests::suite());
		return $suite;
	}
}
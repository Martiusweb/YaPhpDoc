<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/Core/AllTests.php';
require_once __DIR__.'/Tokenizer.test.php';

PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(__DIR__, '.php');

/**
 * Test suite for YaPhpDoc package.
 *
 * @author Martin Richard
 */
class lib_YaPhpDoc_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('lib_YaPhpDoc');
		
		$suite->addTest(lib_YaPhpDoc_Core_AllTests::suite());
		$suite->addTestSuite('TokenizerTest');
		
		return $suite;
	}
}
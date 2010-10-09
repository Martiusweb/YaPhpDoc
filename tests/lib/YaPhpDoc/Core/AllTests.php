<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist(__DIR__, '.php');

require __DIR__.'/Parser.test.php';

/**
* Test suite for YaPhpDoc_Core package.
*
* @author Martin Richard
*/
class lib_YaPhpDoc_Core_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('lib_YaPhpDoc_Core');
		$suite->addTestSuite('ParserTest');
	
		return $suite;
	}
}
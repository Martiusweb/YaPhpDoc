<?php

require_once 'lib/AllTests.php';
require_once __DIR__.'/util.php';

/**
 * The top-level AllTests class.
 * 
 * @author Martin Richard
 */
class AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('YaPhpDoc');
		
		$suite->addTest(lib_AllTests::suite());
		
		return $suite;
	}
}
<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

require_once __DIR__.'/../../../util.php';
require_once 'YaPhpDoc/Core/OutputManager/Aggregate.php';
require_once 'YaPhpDoc/Core/TranslationManager/Aggregate.php';
require_once 'YaPhpDoc/Core/Parser.php';

class ParserTest extends PHPUnit_Framework_TestCase
{
	public function testDummy()
	{
		$this->assertTrue(true);
	}
}
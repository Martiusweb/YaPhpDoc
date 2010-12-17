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
require_once __DIR__.'/OutputManager/AggregateImpl.php';
require_once __DIR__.'/TranslationManager/AggregateImpl.php';

class ParserTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Parser instance
	 * @var YaPhpDoc_Core_Parser
	 */
	protected $_parser;
	
	/**
	 * Create the Parser object and the mocks.
	 * 
	 * @see Framework/PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$ouputManager = new YaPhpDoc_Core_OutputManager_InterfaceImpl();
		$translationManager = new YaPhpDoc_Core_TranslationManager_InterfaceImpl();
		
		$this->_parser = new YaPhpDoc_Core_Parser($ouputManager,
			$translationManager);
	}
	
	/**
	 *  * path added are returned by getDirectories
	 *  * we can add a path providing a string, an array, nested arrays
	 *  * trailing directory separator is removed
	 *
	 * @return void
	 */
	public function testAddDirectoryToExplore()
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->_parser->addDirectory('a');
		$this->_parser->addDirectory('b');
		$this->_parser->addDirectory(array('c', 'd'.$ds, array('e'.$ds.'f')));
		
		$expected = array('a', 'b', 'c', 'd', 'e'.$ds.'f');
		$actual = $this->_parser->getDirectories();
		sort($actual);
		$this->assertEquals($expected, $actual);
	}
}
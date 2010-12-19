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
require_once 'YaPhpDoc/Core/Exception.php';
require_once 'YaPhpDoc/Core/Parser/Exception.php';
require_once 'Zend/Config.php';
require_once __DIR__.'/OutputManager/AggregateImpl.php';
require_once __DIR__.'/TranslationManager/AggregateImpl.php';
require_once __DIR__.'/../Token/Document.php';
require_once __DIR__.'/../Token/File.php';
require_once __DIR__.'/../Tokenizer.php';
@include_once 'vfsStream/vfsStream.php';


/**
 * Tests for YaPhpDoc_Core_Parser
 * 
 * @author Martin Richard
 */
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
	 * Unset the Parser object.
	 * 
	 * @see Framework/PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		unset($this->_parser);
	}
	
	/**
	 *  * path added are returned by getDirectories
	 *  * we can add a path providing a string, an array, nested arrays
	 *  * trailing directory separator is removed
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
	
	/**
	 *  * files added are returned by getFilesToParse when the include pattern is empty
	 *  * we can add a file providing a string, an array, nested arrays
	 */
	public function testAddFileToParse()
	{
		$this->_parser->addFile('foo.php');
		$this->_parser->addFile(array('bar.html', array('baz.php')));
		$this->_parser->setIncludePattern('');
		$expected = array('bar.html', 'baz.php', 'foo.php');
		$actual = $this->_parser->getFilesToParse();
		sort($actual);
		$this->assertEquals($expected, $actual);
	}

	/**
	 *  * includes .php, .php3, .php4, .php5, .phps, .phtml extensions
 	 *  * does not include hidden files (starting with ".")
 	 *  * does not include other extensions
	 */
	public function testDefaultPattern()
	{
		$this->_parser->addFile('foo.php');
		$this->_parser->addFile('foo.php3');
		$this->_parser->addFile('foo.php4');
		$this->_parser->addFile('foo.php5');
		$this->_parser->addFile('foo.phps');
		$this->_parser->addFile('foo.phtml');
		$this->_parser->addFile('.ignore.php');
		$this->_parser->addFile('bar.html');
		$this->_parser->addFile('.foo');
		$this->_parser->addFile('bar/bar.php');
		
		$expected = array('bar/bar.php', 'foo.php', 'foo.php3', 'foo.php4',
			'foo.php5', 'foo.phps', 'foo.phtml');
		
		$actual = $this->_parser->getFilesToParse();
		sort($actual);
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 *  * includes .php, .php3, .php4, .php5, .phps, .phtml extensions
	 *  * does not include other extensions
	 */
	public function testIncludePatternDefaultRegex()
	{
		$this->_parser->addFile('foo.php');
		$this->_parser->addFile('foo.php3');
		$this->_parser->addFile('foo.php4');
		$this->_parser->addFile('foo.php5');
		$this->_parser->addFile('foo.phps');
		$this->_parser->addFile('foo.phtml');
		$this->_parser->addFile('.ignore.php');
		$this->_parser->addFile('bar.html');
		$this->_parser->addFile('.foo');
		$this->_parser->addFile('bar/bar.php');
		
		$this->_parser->setExcludePattern('');
		
		$expected = array('.ignore.php', 'bar/bar.php', 'foo.php', 'foo.php3',
			 'foo.php4', 'foo.php5', 'foo.phps', 'foo.phtml');
		
		$actual = $this->_parser->getFilesToParse();
		sort($actual);
		$this->assertEquals($expected, $actual);
		
	}
	
	/**
	 *  * includes .php extensions
	 *  * does not include others
	 */
	public function testIncludePatternShell()
	{
		$this->_parser->addFile('foo.php');
		$this->_parser->addFile('bar/bar.php');
		$this->_parser->addFile('bar.html');
		$this->_parser->addFile('.foo.php');
		
		$this->_parser->setIncludePattern('*.php', false);
		$this->_parser->setExcludePattern('');
		
		$expected = array('.foo.php', 'bar/bar.php', 'foo.php');
		$actual = $this->_parser->getFilesToParse();
		sort($actual);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 *  * files with .html extension are excluded
	 *  * hidden files (starting with ".") are not excluded
	 */
	public function testExcludePatternRegex()
	{
		$this->_parser->addFile('foo');
		$this->_parser->addFile('foo.php');
		$this->_parser->addFile('bar.html');
		$this->_parser->addFile('.bar');
		
		$this->_parser->setIncludePattern('');
		$this->_parser->setExcludePattern('.*\.html');
		
		$expected = array('.bar', 'foo', 'foo.php');
		$actual = $this->_parser->getFilesToParse();
		sort($actual);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 *  * files with .html extension are excluded
	 *  * hidden files (starting with ".") are not excluded
	 */
	public function testExcludePatternShell()
	{
		$this->_parser->addFile('foo.php');
		$this->_parser->addFile('bar.html');
		$this->_parser->addFile('.foo');
		
		$this->_parser->setIncludePattern('');
		$this->_parser->setExcludePattern('*.html', false);
		
		$expected = array('.foo', 'foo.php');
		$actual = $this->_parser->getFilesToParse();
		sort($actual);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 *	* Add the files in a directory
	 *	* Does not add hidden directories (starting with ".")
	 */
	public function testDirectoriesToParse()
	{
		if(!class_exists('vfsStream'))
		{
			$this->markTestSkipped('vfsStream is required for this test, skipped');
			return;
		}
		
		vfsStreamWrapper::register();
		$root = new vfsStreamDirectory('src');
		vfsStreamWrapper::setRoot($root);
		
		$excluded = vfsStream::newDirectory('.excluded')->at($root);
		$included = vfsStream::newDirectory('included')->at($root);
		$subdir	  = vfsStream::newDirectory('subdir')->at($included);
		
		vfsStream::newFile('bar.php')->at($excluded);
		vfsStream::newFile('foo.php')->at($included);
		vfsStream::newFile('bar.php')->at($subdir);
		
		$this->_parser->addDirectory(vfsStream::url('src/'));
		
		$expected = array(
			vfsStream::url('src/included/foo.php'),
			vfsStream::url('src/included/subdir/bar.php')
		);
		
		$this->_parser->setExcludePattern('');
		$this->_parser->setIncludePattern('');
		
		$actual = $this->_parser->getFilesToParse();
		sort($actual);
		
		$this->assertEquals($expected, $actual);
	}
	
	/**
	 *  * an exception is raisen when there is no source to parse
	 *  
	 *  @expectedException YaPhpDoc_Core_Parser_Exception
	 */
	public function testExceptionThrownWhenNoSourceToParse()
	{
		$this->assertNull($this->_parser->getRoot());	
		$this->_parser->parseAll();
		$this->assertNull($this->_parser->getRoot());
	}
	
	/**
	 *	* files to be parsed are parsed and available in the root node
	 *	* root is provided
	 *	* current file getter (in YaPhpDoc_Token_FileStub) has the right value
	 */
	public function testParsing()
	{
		if(!class_exists('vfsStream'))
		{
			$this->markTestSkipped('vfsStream is required for this test, skipped');
			return;
		}
		
		$cfg = new Zend_Config(array(
			'class' => array(
				'document' => 'YaPhpDoc_Token_DocumentStub',
				'file' => 'YaPhpDoc_Token_FileStub'
			)
		), true);
		
		vfsStreamWrapper::register();
		$root = new vfsStreamDirectory('src');
		vfsStreamWrapper::setRoot($root);
		vfsStream::newFile('test.php')->at($root);
		
		$this->_parser->setConfig($cfg);
		$this->_parser->addFile(vfsStream::url('src/test.php'));
		$this->_parser->parseAll();
		
		$files = $this->_parser->getRoot()->getChildrenByType('file');
		
		$this->assertNotNull($this->_parser->getRoot());
		$this->assertEquals($files[0]->getName(), vfsStream::url('src/test.php'));
		$this->assertEquals($files[0]->current_file, vfsStream::url('src/test.php'));
	}
	
	/**
	 *	* gets the configuration object given with setConfig()
	 */
	public function testConfigSetter()
	{
		$cfg = new Zend_Config(array('class' => 'foo'));
		
		$this->_parser->setConfig($cfg);
		$this->assertSame($cfg, $this->_parser->getConfig());
	}
	
	/**
	 *	* initial value is false
	 *	* set true without providing value
	 *	* set true/false providing a value
	 *	* if true and got (with isFinal), the value is set to false
	 *	* if true and got, $clear set to false, the value is still true
	 */
	public function testFinal()
	{
		$this->assertEquals(false, $this->_parser->isFinal());
		$this->_parser->setFinal();
		$this->assertEquals(true, $this->_parser->isFinal(false));
		$this->assertEquals(true, $this->_parser->isFinal());
		$this->assertEquals(false, $this->_parser->isFinal());
		$this->_parser->setFinal(true);
		$this->assertEquals(true, $this->_parser->isFinal(false));
		$this->_parser->setFinal(false);
		$this->assertEquals(false, $this->_parser->isFinal(false));
	}

	/**
	 *	* initial value is false
	 *	* set true without providing value
	 *	* set true/false providing a value
	 *	* if true and got (with isAbstract), the value is set to false
	 *	* if true and got, $clear set to false, the value is still true
	 */
	public function testAbstract()
	{
		$this->assertEquals(false, $this->_parser->isAbstract());
		$this->_parser->setAbstract();
		$this->assertEquals(true, $this->_parser->isAbstract(false));
		$this->assertEquals(true, $this->_parser->isAbstract());
		$this->assertEquals(false, $this->_parser->isAbstract());
		$this->_parser->setAbstract(true);
		$this->assertEquals(true, $this->_parser->isAbstract(false));
		$this->_parser->setAbstract(false);
		$this->assertEquals(false, $this->_parser->isAbstract(false));
	}

	/**
	 *	* initial value is false
	 *	* set true without providing value
	 *	* set true/false providing a value
	 *	* if true and got (with isStatic), the value is set to false
	 *	* if true and got, $clear set to false, the value is still true
	 */
	public function testStatic()
	{
		$this->assertEquals(false, $this->_parser->isStatic());
		$this->_parser->setStatic();
		$this->assertEquals(true, $this->_parser->isStatic(false));
		$this->assertEquals(true, $this->_parser->isStatic());
		$this->assertEquals(false, $this->_parser->isStatic());
		$this->_parser->setStatic(true);
		$this->assertEquals(true, $this->_parser->isStatic(false));
		$this->_parser->setStatic(false);
		$this->assertEquals(false, $this->_parser->isStatic(false));
	}
	
	/**
	 *	* initial value is false
	 *	* set true without providing value
	 *	* set true/false providing a value
	 *	* if true and got (with isPublic), the value is set to false
	 *	* if true and got, $clear set to false, the value is still true
	 */
	public function testPublic()
	{
		$this->assertEquals(false, $this->_parser->isPublic());
		$this->_parser->setPublic();
		$this->assertEquals(true, $this->_parser->isPublic(false));
		$this->assertEquals(true, $this->_parser->isPublic());
		$this->assertEquals(false, $this->_parser->isPublic());
		$this->_parser->setPublic(true);
		$this->assertEquals(true, $this->_parser->isPublic(false));
		$this->_parser->setPublic(false);
		$this->assertEquals(false, $this->_parser->isPublic(false));
	}

	/**
	 *	* initial value is false
	 *	* set true without providing value
	 *	* set true/false providing a value
	 *	* if true and got (with isProtected), the value is set to false
	 *	* if true and got, $clear set to false, the value is still true
	 */
	public function testProtected()
	{
		$this->assertEquals(false, $this->_parser->isProtected());
		$this->_parser->setProtected();
		$this->assertEquals(true, $this->_parser->isProtected(false));
		$this->assertEquals(true, $this->_parser->isProtected());
		$this->assertEquals(false, $this->_parser->isProtected());
		$this->_parser->setProtected(true);
		$this->assertEquals(true, $this->_parser->isProtected(false));
		$this->_parser->setProtected(false);
		$this->assertEquals(false, $this->_parser->isProtected(false));
	}

	/**
	 *	* initial value is false
	 *	* set true without providing value
	 *	* set true/false providing a value
	 *	* if true and got (with isPrivate), the value is set to false
	 *	* if true and got, $clear set to false, the value is still true
	 */
	public function testPrivate()
	{
		$this->assertEquals(false, $this->_parser->isPrivate());
		$this->_parser->setPrivate();
		$this->assertEquals(true, $this->_parser->isPrivate(false));
		$this->assertEquals(true, $this->_parser->isPrivate());
		$this->assertEquals(false, $this->_parser->isPrivate());
		$this->_parser->setPrivate(true);
		$this->assertEquals(true, $this->_parser->isPrivate(false));
		$this->_parser->setPrivate(false);
		$this->assertEquals(false, $this->_parser->isPrivate(false));
	}
}
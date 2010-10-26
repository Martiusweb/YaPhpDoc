<?php
/**
 * Test classes parsing
 * @author Martin Richard
 */

/**
 * An empty class
 * @author Martin Richard
 */
class MyClass
{
	
}

/**
 * Other class extends MyClass.
 * 
 * @author martius
 */
class MyOtherClass extends MyClass
{
	
}

/**
 * Test implementation of interfaces
 * @author martius
 */
class MyThirdClass extends MyClass implements MyClassInterface, MyOtherClassInterface
{
	
}

/**
 * Now, a "full" class.
 */
abstract class MyFullClass
{
	/**
	 * @var int
	 */
	public $param = 10;
	
	/**
	 * @var int
	 */
	protected $_param;
	
	/**
	 * @var array
	 */
	private $__param = array('val1', 'idx' => 'val2');
	
	/**
	 * @var int
	 */
	public static $staticParam;
	
	/**
	 * @var int
	 */
	protected static $_staticParam;
	
	/**
	 * @var int
	 */
	protected static $__staticParam;
	
	/**
	 * constructor
	 * @param int $param a parameter
	 */
	public function __construct($param)
	{
		$this->_param = $param;
	}
	
	/**
	 * Abstract method
	 * @return void
	 */
	public abstract function method();
	
	/**
	 * Final method
	 * @return void
	 */
	protected final function _method()
	{
		
	}
	
	/**
	 * method
	 * @return void
	 */
	private function __method()
	{
		
	}

	/**
	 * Static method
	 * @return void
	 */
	public static function staticMethod()
	{
		
	}
	
	/**
	 * Static method
	 * @return void
	 */
	protected static function _staticMethod()
	{
		
	}
	
	/**
	 * Static method
	 * @return void
	 */
	private static function __staticMethod()
	{
		
	}
}
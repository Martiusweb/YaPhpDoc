<?php
/**
 * File docblock
 */

/**
 * This is a class
 * 
 * @author Martin Richard
 * @since 1.3
 */
class MyClass
{
	/**
	 * @var $test Test variable
	 */
	protected $var = 'val';
	
	/**
	 * Returns true wathever the parameter given.
	 * 
	 * @param mixed $foo
	 * @return bool
	 */
	public final function myMethod($foo)
	{
		return true;
	}
}

abstract class MyAbstractClass
{}

final class MyFinalClass
{}

class B extends MyClass
{}

class Implementation implements MyInterface1
{}
 
class C extends B implements MyInterface1, MyInterface2
{}

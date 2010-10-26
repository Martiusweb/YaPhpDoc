<?php
/**
 * Test classes parsing
 * @author Martin Richard
 */

/**
 * An interface
 */
interface MyClassInterface
{
	/**
	 * My method
	 * @return array
	 */
	public function method();
	
	/**
	 * My second method
	 * @return void
	 */
	protected function _method();
	
	/**
	 * My third method.
	 * @return MyClass
	 */
	private function __method();
}

/**
 * An other interface
 */
interface MyOtherClassInterface
{
	
}

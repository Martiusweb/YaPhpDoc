<?php
/**
 * This file helps to test docblocks for a file token.
 * 
 * @author Martin Richard
 * @license WTFPL
 */

/**
 * As you can see, we can have several docblocks for a same level.
 * @deprecated
 * @copyright Martin Richard
 * @since 0.1
 * @see /file/docblock
 * @version 1.0
 */

/**
 * A constant
 * @var int
 */
define('TEST', 100);

/**
 * This constant is an array
 * @var array (foo => array(bar))
 */
define('ARRAY_CONST', array('foo' => array('bar')));

/**
 * A global var.
 * @var string
 */
$GLOBALS['foo'] = 'bar';

/**
 * A function doing nothing.
 * @return void
 */
function test()
{
	
}
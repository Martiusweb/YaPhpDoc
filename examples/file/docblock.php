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
 * @var array (foo => bar)
 */
define('ARRAY_CONST', array('foo' => 'bar'));
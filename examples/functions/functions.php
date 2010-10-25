<?php

/**
 * A foo function.
 * @return void
 */
function foo()
{
	
}

/**
 * A bar function with a non documented param p1.
 * @return void
 */
function bar($p1)
{
	
}

/**
 * Returns foo.
 * @param string $foo
 * @return string
 */
function baz($foo)
{
	return $foo;
}

/**
 * Concats foo and bar and returns the result.
 * 
 * @param string $foo
 * @param string $bar default value is 'foo'
 * @return string
 */
function foobar($foo, $bar = 'foo')
{
	return $foo.$bar;
}

/**
 * Concatenates all the parameters and returns the result.
 * @param string $foo
 * @param ...
 * @return string
 */
function barbaz($foo)
{
	$result = '';
	foreach(func_get_args() as $arg)
	{
		$result .= $arg;
	}
	return $result;
}
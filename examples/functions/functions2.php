<?php

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
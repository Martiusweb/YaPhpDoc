<?php
/**
 * 
 * @param MyType $param1 Description of param1
 * @param mixed $param2 Description of param2
 * @param mixed $param3 Description of Param3
 */
function test(MyType $param1, $param2 = T_ARRAY, $param3 = array('check' => T_FUNCTION))
{
	$i = 0;
	++$i;
}


/**
 * Description of test numero deux
 * 
 * @param MyType $param1 Description of param1
 * @param mixed $param2 Description of param2
 * @param mixed $param3 Description of Param3
 * @param ...
 */
function testnumero2(MyType $param1, $param2 = T_ARRAY, $param3 = array('check' => T_FUNCTION))
{
	$i = getarandvalue();
	++$i;
}
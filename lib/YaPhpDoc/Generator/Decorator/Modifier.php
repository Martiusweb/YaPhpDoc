<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * The modifier is an object implementing the logic of decoration for a
 * given output format.
 *
 * 
 * You can create a modifier for a format (ie format Foo) by creating class
 * extending this one and implementing modifiers methods.
 * 
 * In order to add, for instance, the method getUrl() to the token, you must
 * write a method called getUrl($token) (where $token is an instance of
 * YaPhpDoc_Token_Abstract). The method will return the expected value for this
 * function call as if getUrl() was called like $token->getUrl(). Only the first
 * parameter of this method is defined (and is $token), but you can add whatever
 * you want.
 * 
 * @author Martin Richard
 */
abstract class YaPhpDoc_Generator_Decorator_Modifier
{
	/**
	 * This method is called after the modifier and allow to perform generic
	 * decoration after any call.
	 *  
	 * @param mixed $value
	 * @return mixed
	 */
	public function afterCall($value)
	{
		return $value;
	}
}
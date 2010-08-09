<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a function.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Function extends YaPhpDoc_Token_Abstract
{
	/**
	 * Function parameters
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * Function constructor.
	 * 
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct('unknown', T_FUNCTION, $parent);
	}
	
	/**
	 * Parse the function.
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_Function
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		$in_function_definition = false;
		$in_params_definition = false;
		$in_default_value = false;
		$param_defined = 0;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
//			var_dump('type : '.$token->getType());
//			var_dump('val : '.$token->getContent());
			
			if($token->isWhitespace())
			{
				$tokensIterator->next();
				continue;
			}
			
			/* @var $token YaPhpDoc_Tokenizer_Token */
			if($token->isFunction())
			{
				$in_function_definition = true;
			}
			elseif($in_function_definition)
			{
				if($token->isConstantString())
					$this->_name = $token->getStringContent();
				if($token->getType() == '(')
				{
					$in_function_definition = false;
					$in_params_definition = true;
					++$param_defined;
				}
			}
			elseif($in_params_definition)
			{
				if($token->isConstantString())
				{
					$param = $this->_getParam($param_defined);
					$param->setType($token->getStringContent());
				}
				elseif($token->isVariable())
				{
					$param = $this->_getParam($param_defined);
					$param->setName($token->getContent());
				}
				elseif($token->getType() == '=')
				{
					$in_default_value = true;
					$this->_getParam($param_defined)->setOptional();
				}
				elseif($in_default_value)
				{
					if($token->isConstantValue())
					{
						$param = $this->_getParam($param_defined);
						$param->setDefaultValue($token->getConstantContent());
					}
					elseif($token->isArray())
					{
						$param = $this->_getParam($param_defined);
						$array = new YaPhpDoc_Token_Array($param->getName(), $param);
						$array->parse($tokensIterator);
						$param->setDefaultValue($array->getArrayString());
						unset($array);
					}
					$in_default_value = false;
				}
				elseif($token->getType() == ',')
				{
					++$param_defined;
				}
				elseif($token->getType() == ')')
				{
					$in_default_value = false;
					break;
				}
			}
			
			$tokensIterator->next();
		}
		return $this;
	}
	
	/**
	 * Returns the parameter at offset $param_idx
	 * 
	 * @param int $param_idx
	 * @return YaPhpDoc_Token_Param
	 */
	protected function _getParam($param_idx)
	{
		if(!isset($this->_params[$param_idx]))
		{
			$this->_params[$param_idx] = new YaPhpDoc_Token_Param($this);
		}
		return $this->_params[$param_idx];
	}
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : 
	 * @todo choose tags
	 *  
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Function
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		// TODO standard tags for function
		return $this;
	}
}
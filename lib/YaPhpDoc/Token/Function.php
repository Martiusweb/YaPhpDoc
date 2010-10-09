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
	 * True if the function allows any number of params.
	 * @var bool
	 */
	protected $_is_open_params = false;
	
	/**
	 * Type of the returned value.
	 * @var YaPhpDoc_Tag_Return|NULL
	 */
	protected $_return;
	
	/**
	 * Throw tags (exception that can be thrown inside the function).
	 * @var YaPhpDoc_Tag_Throw[]|NULL
	 */
	protected $_throw;
	
	/**
	 * Uses tags.
	 * @var YaPhpDoc_Tag_Uses[]|NULL
	 * @var unknown_type
	 */
	protected $_uses;
	
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
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Function
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$in_function_definition = false;
		$in_params_definition = false;
		$param_defined = 0;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
			/* @var $token YaPhpDoc_Tokenizer_Token */
			
			if($token->isWhitespace())
			{
				$tokensIterator->next();
				continue;
			}
			
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
				if($token->isVariable())
				{
					$param = $this->_getParam($param_defined);
					$param->parse($tokensIterator);
				}
				elseif($token->isConstantString())
				{
					$param = $this->_getParam($param_defined);
					$param->setType($token->getStringContent());
				}
				elseif($token->getType() == ',')
				{
					++$param_defined;
				}
				elseif($token->getType() == ')')
				{
					$in_params_definition = false;
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
	 * Returns an existing parameter according to its name.
	 * 
	 * The name can be prefixed by "$" or not.
	 *  
	 * @param string $name
	 * @return YaPhpDoc_Token_Param|NULL
	 */
	protected function _getParamByName($name)
	{
		if($name[0] != '$')
			$name = '$'.$name;
		
		foreach($this->_params as $param)
		{
			/* @var $param YaPhpDoc_Token_Param */
			if($param->getName() == $name)
				return $param;
		}
		
		return null;
	}
	
	/**
	 * Set standard tags if available from given dockblock.
	 * Tags are : param, return, throw, uses
	 *  
	 * @param YaPhpDoc_Token_DocBlock $docblock
	 * @return YaPhpDoc_Token_Function
	 */
	public function setStandardTags(YaPhpDoc_Token_DocBlock $docblock)
	{
		if($params = $docblock->getTags('param'))
			$this->_setParamTags($params);
		if($return = $docblock->getTags('return'))
			$this->_return = $return;
		if($throw = $docblock->getTags('throw'))
			$this->_throw = $throw;
		if($uses = $docblock->getTags('uses'))
			$this->_uses = $uses;
		
		return $this;
	}
	
	/**
	 * Update function parameters according to docblock param @tags.
	 * 
	 * @param array $params
	 * @return YaPhpDoc_Token_Function
	 */
	public function _setParamTags(array $params)
	{
		$param_defined = 0;
		foreach($params as $paramTag)
		{
			/* @var $paramTag YaPhpDoc_Tag_Param */
			if($paramTag->isOpenParameter())
			{
				$this->_is_open_params = true;
				continue;
			}
			++$param_defined;
			
			$paramToken = $this->_getParamByName($paramTag->getParamName());
			
			if(null == $paramToken)
			{
				$paramToken = new YaPhpDoc_Token_Param($this);
				$paramToken->setType($paramTag->getType());
				$paramToken->setName($paramTag->getParamName());
				$paramToken->setDescription($paramTag->getDescription());
				
				$this->_params[$param_defined] = $paramToken;
			}
			else
			{
				$paramToken->setType($paramTag->getType());
				$paramToken->setName($paramTag->getParamName());
				$paramToken->setDescription($paramTag->getDescription());
			}
		}
		return $this;
	}
	
	/**
	 * Returns true if the function allows any number of parameters.
	 * @return bool
	 */
	public function isOpenParams()
	{
		return $this->_is_open_params;
	}
	
	/**
	 * Returns function parameters.
	 * @return YaPhpDoc_Token_Param[]
	 */
	public function getParams()
	{
		return $this->_params;
	}
	
	/**
	 * Returns the type of the value returned by the function.
	 * @return YaPhpDoc_Tag_Return|NULL
	 */
	public function getReturn()
	{
		return $this->_return;
	}
	
	/**
	 * Returns throw tags (exception that can be thrown inside the function).
	 * @return YaPhpDoc_Tag_Throw[]|NULL
	 */
	public function getThrow()
	{
		return $this->_throw;
	}
	
	/**
	 * Returns uses tags.
	 * @return YaPhpDoc_Tag_Uses[]|NULL
	 */
	public function getUses()
	{
		return $this->_uses;
	}
}
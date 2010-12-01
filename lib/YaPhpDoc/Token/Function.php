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
	protected $_throws;
	
	/**
	 * Uses tags.
	 * @var YaPhpDoc_Tag_Uses[]|NULL
	 * @var unknown_type
	 */
	protected $_uses;
	
//	/**
//	 * Nested block level.
//	 * @var int
//	 */
//	protected $_nested = 0;
	
	/**
	 * Function constructor.
	 * 
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct($parent, 'function', 'unknown');
	}
	
	/**
	 * Parse the function.
	 * 
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Function
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		if($tokensIterator->current()->isFunction())
		{
			$this->_addTokenCallback('constantString', array($this, '_parseName'));
			$this->_addTokensIteratorCallback('(', array($this, '_parseParams'));
//			$this->_addTokensCallback('{', array($this, '_parseLeftBrace'));
//			$this->_addTokensCallback('}', array($this, '_parseRightBrace'));
			
			parent::parse($tokensIterator);
		}
		return $this;
	}
	
//	/**
//	 * Behavior when token is "{".
//	 * @return void
//	 */
//	protected function _parseLeftBrace()
//	{
//		++$this->_nested;
//	}
//	
//	/**
//	 * Behavior when token is "}".
//	 * @return void
//	 */
//	protected function _parseRightBrace()
//	{
//		--$this->_nested;
//		if($this->_nested == 0)
//		{
//			
//		}
//	}
	
	/**
	 * Parses the function name.
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _parseName($token)
	{
		$this->_name = $token->getStringContent();
	}
	
	/**
	 * Parses the parameters.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return void
	 */
	protected function _parseParams(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$tokensIterator->next();
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
			
			if($token->isVariable())
			{
				$param = $this->_getParam($param_defined);
				$param->parse($tokensIterator);
				
				if($param->isEndOfParameters())
					$this->_breakParsing();
				else
				{
					++$param_defined;
					continue;
				}
			}
			elseif($token->isConstantString())
			{
				$param = $this->_getParam($param_defined);
				$param->setType($token->getStringContent());
			}
			elseif($token->getType() == ')')
			{
				$this->_breakParsing();
			}
			$tokensIterator->next();
		}
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
			$this->_params[$param_idx] = YaPhpDoc_Token_Abstract::getToken(
				$this->getParser(), 'param', $this);
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
	 * Tags are : param, return, throws, uses
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
		if($throws = $docblock->getTags('throws'))
			$this->_throws = $throws;
		if($uses = $docblock->getTags('uses'))
			$this->_uses = $uses;
		
		$this->setDescription($docblock->getContent());
		
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
			
			$paramToken = $this->_getParamByName($paramTag->getParamName());
			
			if(null == $paramToken)
			{
				$paramToken = YaPhpDoc_Token_Abstract::getToken(
					$this->getParser(), 'param', $this);
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
			++$param_defined;
		}
		return $this;
	}
	
	/**
	 * Returns true if the function allows any number of parameters.
	 * @return bool
	 */
	public function getIsOpenParams()
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
	public function getThrows()
	{
		return $this->_throws;
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
<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a function or method parameter.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Param extends YaPhpDoc_Token_Var
{
	/**
	 * Means that the parameter was the last to be parsed (break token was ')'). 
	 * @var bool
	 */
	private $_endOfParameters = false;
	
	/**
	 * If true, the parameter is optional.
	 * @var bool
	 */
	protected $_option = false;
	
	/**
	 * Parameter constructor.
	 * 
	 * @param YaPhpDoc_Token_Function $parent
	 * @return YaPhpDoc_Token_Param
	 */
	public function __construct(YaPhpDoc_Token_Function $parent)
	{
		parent::__construct($parent, 'param', 'unknown');
	}
	
	/**
	 * Parses the function parameter.
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Param
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		# TODO Test when value is an array (why "=>" is an "evaluableStringDelimiter" ?)
		$this->_addTokenCallback(',', array($this, '_breakParsing'));
		$this->_addTokenCallback(')', array($this, '_endOfParameters'));
		
		parent::parse($tokensIterator);
		
		if($this->_value !== null)
			$this->setOptional();
			
		return $this;
	}
	
	/**
	 * breaks parsing and notify that parsing was ended by a ").
	 * @param YaPhpDoc_Tokenizer_Token $token
	 * @return void
	 */
	protected function _endOfParameters(YaPhpDoc_Tokenizer_Token $token)
	{
		if($token->getType() == ')')
			$this->_endOfParameters = true;
		
		$this->_breakParsing();
	}
	
	/**
	 * Returns true if parsing was terminated by a ')' token.
	 * @return bool
	 */
	public function isEndOfParameters()
	{
		return $this->_endOfParameters;
	}
	
	/**
	 * Set the parameter type.
	 * 
	 * @param string $type
	 * @return YaPhpDoc_Token_Param
	 */
	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}
	
	/**
	 * Set the parameter name.
	 * 
	 * @param string $name
	 * @return YaPhpDoc_Token_Param
	 */
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	
	/**
	 * Set the default parameter value.
	 * 
	 * @param string $value
	 * @return YaPhpDoc_Token_Param
	 */
	public function setDefaultValue($value)
	{
		$this->_value = $value;
		return $this;
	}
	
	/**
	 * Returns the default value of the parameter.
	 * @return string
	 */
	public function getDefaultValue()
	{
		return $this->_value;
	}
	
	/**
	 * Set the parameter as optional.
	 * @param string $flag (optional, default to true)
	 * @return YaPhpDoc_Token_Param
	 */
	public function setOptional($flag = true)
	{
		$this->_option = true;
		return $this;
	}
	
	/**
	 * Returns true if the parameter is optional.
	 * @return bool
	 */
	public function isOptional()
	{
		return $this->_option;
	}
}
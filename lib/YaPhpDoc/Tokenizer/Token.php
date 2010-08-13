<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * An object representation of a token from Tokenizer token_get_all() PHP
 * function.
 * 
 * Not all Token types are supported, only those who are needed by the parser.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tokenizer_Token
{
	/**
	 * Misc. token type (a token which replace one or more ignored tokens).
	 * @var int
	 */
	const T_MISC = 0;
	
	/**
	 * Token type
	 * @see http://www.php.net/manual/en/tokens.php
	 * @var int
	 */
	private $_type;
	
	/**
	 * Token content
	 * @var string
	 */
	private $_content;
	
	/**
	 * Token line
	 * @var int
	 */
	private $_line;
	
	/**
	 * Constructor.
	 * You can give the token as an array of with separated parameters.
	 * @param int|array $type
	 * @param string $content
	 */
	public function __construct($type, $content = '', $line = 0)
	{
		if(is_array($type))
			list($type, $content, $line) = $type;
		
		$this->_type = $type;
		$this->_content = $content;
		$this->_line = $line;
	}
	
	/**
	 * Return the token type as string.
	 * @return string
	 */
	public function getType()
	{
		if(is_string($this->_type))
			return $this->_type;
		elseif($this->_type == self::T_MISC)
			return 'T_MISC';
		else
			return token_name($this->_type);
	}
	
	/**
	 * Return the token type (PHP constant value).
	 * @return int
	 */
	public function getTypeId()
	{
		return $this->_type;
	}
	
	/**
	 * Rerturn the token content.
	 * @return string
	 */
	public function getContent()
	{
		return $this->_content;
	}
	
	/**
	 * Return the line where the token is.
	 * @return int
	 */
	public function getLine()
	{
		return $this->_line;
	}
	
	/**
	 * The object representation as string is the token content
	 */
	public function __toString()
	{
		return $this->_content;
	}
	
	/**
	 * Returns true if the token is a doc-block.
	 * @return bool
	 */
	public function isDocBlock()
	{
		return $this->_type == T_DOC_COMMENT;
	}
	
	/**
	 * Returns true if the token is a string of whitespaces.
	 * @return bool
	 */
	public function isWhitespace()
	{
		return $this->_type == T_WHITESPACE;
	}
	
	/**
	 * Returns true if the token is a class definition.
	 * @return bool
	 */
	public function isClass()
	{
		return $this->_type == T_CLASS; 
	}
	
	/**
	 * Returns true if the token is an interface definition.
	 * @return bool
	 */
	public function isInterface()
	{
		return $this->_type == T_INTERFACE;
	}
	
	/**
	 * Returns true if the token is the abstract keyword.
	 * @return bool
	 */	
	public function isAbstract()
	{
		return $this->_type == T_ABSTRACT;
	}
	
	/**
	 * Returns true if the token is a function definition.
	 * @return bool
	 */
	public function isFunction()
	{
		return $this->_type == T_FUNCTION;
	}
	
	/**
	 * Returns true if the token is a constant definition (using define or
	 * const keyword).
	 * @return bool
	 */
	public function isConst()
	{
		return $this->_type == T_CONST || ($this->_type == T_STRING
			&& $this->_content == 'define');
	}
	
	/**
	 * Returns true if the token is a global definition.
	 * @return bool
	 */
	public function isGlobal()
	{
		return $this->_type == T_GLOBAL || ($this->_type == T_VARIABLE
			&& $this->_content == '$GLOBALS'); 
	}
	
	/**
	 * Returns true if the token is a var definition (var keyword).
	 * @return bool
	 */
	public function isVar()
	{
		return $this->_type == T_VAR;
	}
	
	/**
	 * Returns true if the token is a constant value (such as a constant string
	 * or a constant number).
	 * @return bool 
	 */
	public function isConstantValue()
	{
		return $this->isConstantString()
			|| $this->_type == T_DNUMBER || $this->_type == T_LNUMBER;
	}
	
	/**
	 * Returns true if the token is a constant string ('foo', "foo", `foo`).
	 * A "double-quoted" string containing a variable is not a constant string.
	 * @return bool
	 */
	public function isConstantString()
	{
		return $this->_type == T_CONSTANT_ENCAPSED_STRING ||
			($this->_type == T_STRING && $this->_content != 'define');
	}
	
	/**
	 * Returns true if the token is a variable ($foo, ${foo}, {$foo}) or
	 * a string containing variable ("$foo", `$foo`).
	 * @return bool
	 */
	public function isVariable()
	{
		return $this->_type == T_VARIABLE || $this->_type == T_CURLY_OPEN
			|| $this->_type == T_STRING || $this->_type == T_STRING_VARNAME;
	}
	
	/**
	 * Returns true if the token is a "`".
	 * @return bool
	 */
	public function isEvaluableStringDelimiter()
	{
		return $this->_type == '`';
	}
	
	/**
	 * Returns true if the token is an array
	 */	
	public function isArray()
	{
		return $this->_type == T_ARRAY;
	}
	
	/**
	 * Try to parse constant value content.
	 * @return string
	 */
	public function getConstantContent()
	{
		if($this->isConstantString())
			return $this->getStringContent();
		else
			return $this->getContent();
	}
	
	/**
	 * Try to parse the string content.
	 * @return string
	 */
	public function getStringContent()
	{
		if($this->_type == T_CONSTANT_ENCAPSED_STRING)
		{
			return trim($this->_content, $this->_content[0]);
		}
		elseif($this->_type == T_STRING)
		{
			return $this->_content;
		}
		
		return '';
	}
}
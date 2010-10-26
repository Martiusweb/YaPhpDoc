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
 * A new token type is defined by adding a isType() method, which returns true
 * if the token is of type Type. If the method is a top-level type (ie TypeA
 * or TypeB), the method should be declared after the isTypeA and isTypeB. See
 * isConstantString() and isConstantValue() as an example.
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
	 * Returns the string representing the type $type, which is a type constant.
	 * @param int $type
	 * @return string
	 */
	public static function getTypeAsString($type)
	{
		if($type == self::T_MISC)
			return 'T_MISC';
		
		switch($type)
		{
			case T_DOC_COMMENT:
				return 'docBlock';
			case T_NAMESPACE:
				return 'namespace';
			case T_CONST:
				return 'const';
			default:
				return token_name($type);
		}
	}
	
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
		else
		{
			# Some reflection
			$reflection = new ReflectionClass(get_class($this));
			foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
			{
				/* @var $method ReflectionMethod */
				$method = $method->name;
				if(substr($method, 0, 2) == 'is' && $this->$method())
					return lcfirst(substr($method, 2));
			}
		}
		return 'T_MISC';
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
	 * Returns true if the token is a class or an interface definition.
	 * @return bool
	 */
	public function isClassOrInterface()
	{
		return $this->isClass() || $this->isInterface();
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
	 * Returns true if the token is the final keyword.
	 * @return bool
	 */
	public function isFinal()
	{
		return $this->_type == T_FINAL;
	}
	
	/**
	 * Returns true if the token is the static keyword.
	 * @return bool
	 */
	public function isStatic()
	{
		return $this->_type == T_STATIC;
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
	 * Returns true if the token is a constant string ('foo', "foo", `foo`).
	 * A "double-quoted" string containing a variable is not a constant string.
	 * @return bool
	 */
	public function isConstantString()
	{
		return $this->_type == T_CONSTANT_ENCAPSED_STRING ||
			($this->_type == T_STRING && $this->_content !== 'define');
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
	 * Returns true if the token is a variable ($foo, ${foo}, {$foo}) or
	 * a string containing variable ("$foo", `$foo`).
	 * @return bool
	 */
	public function isVariable()
	{
		return $this->_type == T_VARIABLE || $this->_type == T_CURLY_OPEN
			/* || $this->_type == T_STRING */ || $this->_type == T_STRING_VARNAME;
	}
	
	/**
	 * Returns true if the token is a "`".
	 * @return bool
	 */
	public function isEvaluableStringDelimiter()
	{
		return $this->_type === '`';
	}
	
	/**
	 * Returns true if the token is an array
	 * @return bool
	 */	
	public function isArray()
	{
		return $this->_type == T_ARRAY;
	}
	
	/**
	 * Returns true if the token is a double arrow ("=>").
	 * @return bool
	 */
	public function isDoubleArrow()
	{
		return $this->_type == T_DOUBLE_ARROW;
	}
	
	/**
	 * Returns true if the token is the public or the var keyword.
	 * @return bool
	 */
	public function isPublic()
	{
		return $this->_type == T_PUBLIC || $this->_type == T_VAR;
	}
	
	/**
	 * Returns true if the token is the protected keyword
	 * @return bool
	 */
	public function isProtected()
	{
		return $this->_type == T_PROTECTED;
	}
	
	/**
	 * Returns true if the token is the private keyword
	 * @return bool
	 */
	public function isPrivate()
	{
		return $this->_type == T_PRIVATE;
	}
	
	/**
	 * Returns true if the token is the extends keyword
	 * @return bool
	 */
	public function isExtends()
	{
		return $this->_type == T_EXTENDS;
	}
	
	/**
	 * Returns true if the token is the implements keyword
	 * @return bool
	 */
	public function isImplements()
	{
		return $this->_type == T_IMPLEMENTS;
	}
	
	/**
	 * Returns true if the token is the namespace keyword
	 * @return bool
	 */
	public function isNamespace()
	{
		return $this->_type == T_NAMESPACE;
	}
	
	/**
	 * Returns true if the token is the namespace separator token.
	 * @return bool
	 */
	public function isNamespaceSeparator()
	{
		return $this->_type == T_NS_SEPARATOR;
	}
	
	/**
	 * Returns true if the token is the use keyword.
	 * @return bool
	 */
	public function isUse()
	{
		return $this->_type == T_USE;
	}
	
	/**
	 * Returns true if the token is the as keyword.
	 * @return bool
	 */
	public function isAs()
	{
		return $this->_type == T_AS;
	}
	
	/**
	 * Allows call to isXxx() and returns false.
	 * 
	 * @param string $f
	 * @param array $a
	 * @return bool
	 */
	public function __call($f, $a)
	{
		return false;
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
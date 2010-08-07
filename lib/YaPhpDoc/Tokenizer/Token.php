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
		return is_string($this->_type) ? $this->_type : token_name($this->_type);
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
		return $this->_type == T_DO;
	}
}
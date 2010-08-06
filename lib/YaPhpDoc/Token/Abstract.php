<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent any token (file, package, namespace, class, function, etc) the
 * program can parse and document.
 * 
 * @author Martin Richard
 */
abstract class YaPhpDoc_Token_Abstract
{
	/**
	 * Name of the token
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Token type (as given by token_get_all)
	 * @var int
	 */
	protected $_tokenType;
	
	/**
	 * Constructor of a token. The type of token is not tested,
	 * but the behavior of the program is not predictable if
	 * the givent value is not one of the php token constants.
	 * 
	 * @param string $name
	 * @param int $token_type
	 */
	public function __construct($name, $token_type)
	{
		$this->_name = $name;
		$this->_tokenType = $token_type;
	}
	
	/**
	 * Returns the token name.
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Returns the token type.
	 * @return int
	 */
	public function getTokenType()
	{
		$this->_tokenType;
	}
}
<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A wrapper for the Tokenizer get_token_all() PHP function.
 * Customized for our needs.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Tokenizer implements IteratorAggregate, Countable
{
	/**
	 * Array of tokens as given by token_get_all()
	 * @var array
	 */
	protected $_tokens_array;
	
	/**
	 * Array of tokens as objects
	 * @var YaPhpDoc_Tokenizer_Token[]
	 */
	protected $_tokens = array();
	
	/**
	 * Parse the $source with token_get_all() and populates the
	 * object.
	 * 
	 * @param string $source
	 */
	public function __construct($source)
	{
		$this->_tokens_array = token_get_all($source);
		
		$this->_filter()->_toObjects();
	}
	
	/**
	 * Filters the token in order to keep only those needed by the parser.
	 * One ore more ignored tokens are replaced by a "T_MISC" token.
	 * 
	 * @return YaPhpDoc_Tokenizer
	 */
	public function _filter()
	{
		$ignored = self::_getIgnoredTokenTypes();

		for($i = 0, $j = count($this->_tokens_array); $i < $j; ++$i)
		{
			$in_misc = false;
			$last_misc = 0;
			if(isset($ignored[$this->_tokens_array[$i][0]]))
			{
				if($in_misc)
				{
					$this->_tokens_array[$last_misc][1] .= $this->_tokens_array[$i][1];
					unset($this->_tokens_array[$i]);
				}
				else
				{
					$in_misc = true;
					$last_misc = $i;
					$this->_tokens_array[$i][0] = YaPhpDoc_Tokenizer_Token::T_MISC;
				}
			}
			else
			{
				$in_misc = false;
			}
		}
		return $this;
	}
	
	/**
	 * Transform tokens as objects.
	 * 
	 * @return YaPhpDoc_Tokenizer
	 */
	protected function _toObjects()
	{
		foreach($this->_tokens_array as $token)
		{
			array_push($this->_tokens,
				new YaPhpDoc_Tokenizer_Token($token));
		}
		return $this;
	}
	
	/**
	 * Returns an iterator on tokens (as objects).
	 * 
	 * @return YaPhpDoc_Tokenizer_Iterator
	 */
	public function getIterator()
	{
		return new YaPhpDoc_Tokenizer_Iterator($this->_tokens);
	}
	
	/**
	 * Returns the number of tokens.
	 * 
	 * @return int
	 */
	public function count()
	{
		return count($this->_tokens_array);
	}
	
	/**
	 * Token types we don't need (in array keys).
	 * @return array
	 */
	protected static function _getIgnoredTokenTypes()
	{
		return array_flip(array(
			# Classic comments, html, & misc.
			T_COMMENT, T_BAD_CHARACTER, T_INLINE_HTML, //T_WHITESPACE,
			# all operators ("=" is not a typed parser token)
			T_AND_EQUAL, T_BOOLEAN_AND, T_BOOLEAN_OR, T_CONCAT_EQUAL, T_DEC,
			T_DIV_EQUAL, T_DOUBLE_ARROW, T_INC, T_IS_EQUAL,
			T_IS_GREATER_OR_EQUAL, T_IS_IDENTICAL, T_IS_NOT_EQUAL,
			T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_LOGICAL_AND,
			T_LOGICAL_OR, T_LOGICAL_XOR, T_MINUS_EQUAL, T_MOD_EQUAL,
			T_MUL_EQUAL, T_OR_EQUAL, T_PLUS_EQUAL, T_SL, T_SL_EQUAL, T_SR,
			T_SR_EQUAL, T_XOR_EQUAL,
			# Magic constants
			T_CLASS_C, T_DIR, T_FILE, T_FUNC_C, T_LINE, T_METHOD_C, T_NS_C,
			# Cast operations
			T_ARRAY_CAST, T_BOOL_CAST, T_DOUBLE_CAST, T_INT_CAST, T_OBJECT_CAST,
			T_STRING_CAST, T_UNSET_CAST,
			# various keywords
			T_AS, T_BREAK, T_CASE, T_CATCH, T_CONTINUE, T_DEFAULT, T_DO, T_ECHO,
			T_ELSE, T_ELSEIF, T_EMPTY, T_ENDDECLARE, T_ENDFOR, T_ENDFOREACH,
			T_ENDIF, T_ENDSWITCH, T_ENDWHILE, T_END_HEREDOC, T_EVAL, T_EXIT,
			T_GOTO, T_HALT_COMPILER, T_IF, T_FOR, T_FOREACH, T_INCLUDE,
			T_INCLUDE_ONCE, T_INSTANCEOF, T_ISSET, T_LIST, T_PRINT, T_REQUIRE,
			T_REQUIRE_ONCE, T_RETURN, T_START_HEREDOC, T_SWITCH, T_TRY, T_UNSET,
			T_WHILE,
			# open and close tags
			T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLOSE_TAG,
		));
	}
}
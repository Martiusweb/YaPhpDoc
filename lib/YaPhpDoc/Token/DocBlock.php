<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * A docBlock is a comment block starting with "/**" and that may contain
 * @tags (documentation elements on a line starting with "@").
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_DocBlock extends YaPhpDoc_Token_Abstract
{
	/**
	 * Tags in the DocBlock and their content.
	 * @var array
	 */
	public $_tags = array();
	
	/**
	 * DocBlock content.
	 * @var string
	 */
	public $_content = '';
	
	/**
	 * Constructor
	 */
	public function __construct(YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct('docblock', T_DOC_COMMENT, $parent);
	}
	
	/**
	 * Parse a docblock : find content and @tags. A line not begining with * is
	 * ignored (prepending whitespaces are ignored). 
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_DocBlock
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		$token = $tokensIterator->current();
		$token_content = explode("\n", substr(substr($token->getContent(), 2), 0, -2));
		/* @var $token YaPhpDoc_Tokenizer_Token */
		
		// TODO DocBlock parser
		$content = '';
		foreach($token_content as $line)
		{
			# Ignore prepending whitespaces
			$line = trim($line);
			
			# if the line first character is not *, it's ignored
			if(empty($line) || $line[0] != '*')
				continue;
			
			# find line content
			$line = trim(substr($line, 1));
			
			# if line is empty, go next !
			if(empty($line))
			{
				$content .= "\n";
				continue;
			}
			
			# is it @tag ?
			if(preg_match('`^@([a-zA-Z0-9_\-]*?) (.*)`', $line, $matches))
			{
				$this->_tags[$matches[1]][] = $matches[2];
			}
			else
				$content .= $line."\n";
		}
		$this->_content = $content;
		return $this;
	}
	
	/**
	 * Returns block content.
	 * 
	 * @return string
	 */
	public function getContent()
	{
		return $this->_content;
	}
	
	/**
	 * Returns thags content (in array, in the order they were found).
	 * If tag was not found, returns null.
	 * 
	 * If $tagname is null, all tags are returned.
	 * 
	 * @param string $tagname (optional, default null)
	 * @return array|null
	 */
	public function getTags($tagname = null)
	{
		if(null === $tagname)
			return $this->_tags;
		
		return isset($this->_tags[$tagname]) ? $this->_tags[$tagname]
			: null;
	}
}
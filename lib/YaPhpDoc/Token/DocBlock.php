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
	protected $_tags = array();
	
	
	/**
	 * DocBlock content.
	 * @var string
	 */
	protected $_content = '';
	
	/**
	 * Tags retrieved by getTags().
	 * @var array
	 */
	protected $_usedTags = array();
	
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
		
		$content = '';
		$line_no = $token->getLine();
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
			if($line[0] == '@')
			{
				try {
					$tag = YaPhpDoc_Tag_Abstract_Abstract::getTag($line);
					if($tag->isMultipleUsage())
						$this->_tags[$tag->getName()][] = $tag;
					else
						$this->_tags[$tag->getName()] = $tag;	
				} catch(YaPhpDoc_Tag_Exception $e)
				{
					Ypd::getInstance()->warning($e->getMessage().sprintf(
					Ypd::getInstance()->getTranslation()->_(' in %s at line %d'),
					Ypd::getInstance()->getParser()->getCurrentFile(), $line_no));
				}
			}
			else
				$content .= $line."\n";
			
			++$line_no;
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
	 * @return YaPhpDoc_Tag_Abstract_Abstract|array|null
	 */
	public function getTags($tagname = null)
	{
		if(null === $tagname)
			return $this->_tags;

		if(isset($this->_tags[$tagname]))
		{
			return $this->_tags[$tagname];
			array_push($this->_usedTags, $tagname);		
		}
		return null;
	}
	
	/**
	 * Returns tags that have not been retrieved yet.
	 * 
	 * @return array
	 */
	public function getNotUsedTags()
	{
		$tags = $this->_tags;
		foreach($this->_usedTags as $tagname)
		{
			unset($tags[$tagname]);
		}
		return $tags;
	}
}
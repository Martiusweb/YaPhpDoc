<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represent a PHP file.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_File extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * File constructor.
	 * @param string $filename
	 * @param YaPhpDoc_Token_Abstract $parent
	 */
	public function __construct($filename, YaPhpDoc_Token_Abstract $parent)
	{
		parent::__construct($filename, YaPhpDoc_Token_Abstract::FILE, $parent);
	}
	
	/**
	 * Parse a PHP file.
	 * 
	 * @param ArrayIterator $tokensIterator
	 * @return YaPhpDoc_Token_File
	 */
	public function parse(ArrayIterator $tokensIterator)
	{
		$docblock = null;
		while($tokensIterator->valid())
		{
			$token = $tokensIterator->current();
			/* @var $token YaPhpDoc_Tokenizer_Token */
			
			# Skip whitespace
			if($token->isWhitespace())
			{
				$tokensIterator->next();
				continue;
			}
			
			if($token->isDocBlock())
			{
				$docblock = new YaPhpDoc_Token_DocBlock($this);
				$docblock->parse($tokensIterator);
			}
			
			if($token->isClass())
			{
				// TODO parse class
			}
			elseif($token->isFunction())
			{
				// TODO parse function
			}
			elseif($token->isConst())
			{
				$const = new YaPhpDoc_Token_Const($this);
				$const->parse($tokensIterator);
				if($docblock !== null)
				{
					$const->setStandardTags($docblock);
					$docblock = null;
				}
				$this->addChild($const);
			}
			elseif($token->isGlobal())
			{
				$global = new YaPhpDoc_Token_Global($this);
				$global->parse($tokensIterator);
				if($docblock !== null)
				{
					$global->setStandardTags($docblock);
					$docblock = null;
				}
				$this->addChild($global);
				unset($global);
			}
			else
			{
				# The last found docblock is not before a documentable token.
				if(null !== $docblock)
				{
					$this->setStandardTags($docblock);
					$docblock = null;
				}
			}
			
			$tokensIterator->next();
		}
		
		return $this;
	}
}
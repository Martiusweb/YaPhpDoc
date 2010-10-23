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
	 * Current namespace
	 * @var YaPhpDoc_Token_Namespace
	 */
	protected $_currentNamespace;
	
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
	 * @todo A refactoring will be performed in 1.X+, management of namespaces is not fully stable
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_File
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		$docblock = null;
		$nested = 0;
		$is_global_namespace = false;
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
			elseif($token->isUse())
			{
				$use = new YaPhpDoc_Token_Use($this->_getCurrentParent());
				$use->parse($tokensIterator);
				$this->_getCurrentParent()->addChild($use);
			}
			elseif($token->isNamespace())
			{
				$this->_currentNamespace = new YaPhpDoc_Token_Namespace($this);
				$this->_currentNamespace->parse($tokensIterator);
				$this->addChild($this->_currentNamespace);
				
				$tokensIterator->next();
				if($token->getType() != '{')
				{
					$is_global_namespace = true;
				}
				continue;
			}
			elseif($token->getType() == '{')
			{
				++$nested;
			}
			elseif($token->getType() == '}')
			{
				--$nested;
				if(!$is_global_namespace && $nested == 0)
				{
					$this->_currentNamespace = null;
				}
			}
			elseif($token->isAbstract())
			{
				$this->getParser()->setAbstract();
			}
			elseif($token->isFinal())
			{
				$this->getParser()->setFinal();
			}
			elseif($token->isInterface() || $token->isClass())
			{
				$token_type = 'YaPhpDoc_Token_';
				$token_type .= $token->isInterface() ? 'Interface' : 'Class';
				$class = new $token_type($this->_getCurrentParent());
				$class->parse($tokensIterator);
				
				if($docblock !== null)
				{
					$class->setStandardTags($docblock);
					$docblock = null;
				}
				$this->_getCurrentParent()->addChild($class);
				unset($class);
			}
			elseif($token->isFunction())
			{
				$function = new YaPhpDoc_Token_Function($this->_getCurrentParent());
				$function->parse($tokensIterator);
				if($docblock !== null)
				{
					$function->setStandardTags($docblock);
					$docblock = null;
				}
				$this->_getCurrentParent()->addChild($function);
				unset($function);
			}
			elseif($token->isConst())
			{
				$const = new YaPhpDoc_Token_Const($this->_getCurrentParent());
				$const->parse($tokensIterator);
				if($docblock !== null)
				{
					$const->setStandardTags($docblock);
					$docblock = null;
				}
				$this->_getCurrentParent()->addChild($const);
				unset($const);
			}
			elseif($token->isGlobal())
			{
				$global = new YaPhpDoc_Token_Global($this->_getCurrentParent());
				$global->parse($tokensIterator);
				if($docblock !== null)
				{
					$global->setStandardTags($docblock);
					$docblock = null;
				}
				$this->_getCurrentParent()->addChild($global);
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
	
	/**
	 * Returns the current parent (a namespace or a file).
	 * 
	 * @return YaPhpDoc_Token_Abstract
	 */
	protected function _getCurrentParent()
	{
		if($this->_currentNamespace != null)
		{
			return $this->_currentNamespace;
		}
		return $this;
	}
	
	/**
	 * Return the filename without the parsing root.
	 * @return string
	 */
	public function getFilename()
	{
		return str_replace($this->getParser()->getDirectories(), '', $this->getName());
	}
}
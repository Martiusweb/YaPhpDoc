<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * Represents a namespace.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Token_Namespace extends YaPhpDoc_Token_Structure_Abstract
{
	/**
	 * Namespace separator character.
	 * @var string
	 */
	const NS_SEPARATOR = '\\';
	
	/**
	 * Constructor of a namespace.
	 * @param YaPhpDoc_Token_File $parent
	 */
	public function __construct(YaPhpDoc_Token_File $parent)
	{
		parent::__construct('unknown', T_NAMESPACE, $parent);
	}
	
	/**
	 * Parses the iterator
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Namespace
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		if($tokensIterator->current()->isNamespace())
		{
			$tokensIterator->next();
			
			$this->_name = '';
			while($tokensIterator->valid())
			{
				$token = $tokensIterator->current();
				if($token->isConstantString())
				{
					$this->_name .= $token->getConstantContent();
				}
				elseif($token->isNamespaceSeparator())
				{
					$this->_name .= self::NS_SEPARATOR;
				}
				elseif($token->getType() == ';' || $token->getType() == '{')
				{
					break;
				}
				$tokensIterator->next();
			}
		}
		
		return $this;
	}
}
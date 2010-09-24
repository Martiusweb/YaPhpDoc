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
class YaPhpDoc_Token_Use extends YaPhpDoc_Token_Abstract
{
	/**
	 * Alias of the use statement.
	 * @var string|NULL
	 */
	protected $_alias;
	
	/**
	 * Constructor of a use token.
	 * @param YaPhpDoc_Token_File $parent
	 */
	public function __construct(YaPhpDoc_Token_File $parent)
	{
		parent::__construct('unknown', T_NAMESPACE, $parent);
	}
	
	/**
	 * Parses the iterator
	 * @param YaPhpDoc_Tokenizer_Iterator $tokensIterator
	 * @return YaPhpDoc_Token_Use
	 */
	public function parse(YaPhpDoc_Tokenizer_Iterator $tokensIterator)
	{
		if($tokensIterator->current()->isUse())
		{
			$tokensIterator->next();
			
			$is_name_definition = true;
			$this->_name = '';
			while($tokensIterator->valid())
			{
				$token = $tokensIterator->current();
				if($token->isConstantString())
				{
					if($is_name_definition)
						$this->_name .= $token->getConstantContent();
					else
						$this->_alias = $token->getConstantContent();
				}
				elseif($token->isNamespaceSeparator())
				{
					$this->_name .= YaPhpDoc_Token_Namespace::NS_SEPARATOR;
				}
				elseif($token->isAs())
				{
					$is_name_definition = false;
				}
				elseif($token->getType() == ';')
				{
					break;
				}
				$tokensIterator->next();
			}
		}
		
		return $this;
	}
	
	/**
	 * Returns the use statement alias.
	 * @return string|NULL
	 */
	public function getAlias()
	{
		return $this->_alias;
	}
}
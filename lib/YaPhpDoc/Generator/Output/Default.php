<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * This is the standard generator class.
 * 
 * The generated output is in HTML with Javascript auto-completion. 
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Output_Default extends YaPhpDoc_Generator_Abstract
{
	/**
	 * Theme name (matches the theme directory name).
	 * @var string
	 */
	private $_theme = 'default';
	
	/**
	 * Twig environment object.
	 * @var Twig_Environment
	 */
	protected $_twig;
	
	public function initialize()
	{
		$loader = new Twig_Loader_Filesystem($this->getDataPath().'/templates/'.
			$this->_theme);
		
		# TODO Use a configuration system to set cache path
		$this->_twig = new Twig_Environment($loader, array(
			'debug'	=> false,
			'cache' => '/tmp'
		));
	}
	
	/**
	 * Set the theme name (matches the theme directory in data/templates).
	 * @param string $theme
	 * @return YaPhpDoc_Generator_Output_Default
	 */
	public function setTheme($theme)
	{
		$this->_theme = $theme;
		return $this;
	}
	
	/**
	 * Returns the theme name.
	 * @return string
	 */
	public function getTheme()
	{
		return $this->_theme;
	}
}
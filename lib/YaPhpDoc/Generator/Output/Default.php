<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * This is the standard generator class.  The generated output is in HTML with
 * Javascript auto-completion (HtmlDefault theme).
 * 
 * You can use this class as a base to write your own generator in HTML. It
 * uses Twig, the Symfony 2 templating engine and supports themes templating.
 * You can create a new theme based on the HtmlDefault one if you don't have
 * specifics needs here.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Output_Default extends YaPhpDoc_Generator_Abstract
{
	/**
	 * Theme name (matches the theme directory name).
	 * @var string
	 */
	private $_theme = 'HtmlDefault';
	
	/**
	 * Theme templates directory.
	 * @var string
	 */
	protected $_themeDir = '';
	
	/**
	 * Twig environment object.
	 * @var Twig_Environment
	 */
	protected $_twig;
	
	/**
	 * Global context for twig rendering.
	 * @var array
	 */
	protected $_globalContext = array();
	
	/**
	 * @see lib/YaPhpDoc/Generator/YaPhpDoc_Generator_Abstract#initialize()
	 */
	protected function _initialize()
	{
		# Try to find the theme in the configuration
		$this->_theme = $this->getGeneratorConfig()->get('theme', $this->_theme);
		$this->out()->verbose(sprintf($this->l10n()->getTranslation('generator')
			->_('Generating documentation with theme %s'), $this->_theme),
		false);
		
		$this->_themeDir = $this->_dataDir.'/templates/'.$this->_theme;
		
		try {
			$loader = new Twig_Loader_Filesystem($this->_themeDir);
			$this->_twig = new Twig_Environment($loader, array(
				'debug' => true,
				'strict_variables' => true
			));
		}
		catch(Exception $e)
		{
			throw new YaPhpDoc_Generator_Exception($e->getMessage());
		}
		
		$this->_globalContext['config'] = $this->_config;
		# TODO decorates the root with an object translating values in HTML
		$this->_globalContext['code']	= $this->_root;
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
	
	/**
	 * Adds a value to global twig template context.
	 * @param string $var
	 * @param string $val
	 * @return YaPhpDoc_Generator_Output_Default
	 */
	public function setGlobalContextValue($var, $val)
	{
		$this->_globalContext[$var] = $val;
		return $this;
	}
	
	/**
	 * @see YaPhpDoc/Generator/YaPhpDoc_Generator_Abstract#build()
	 */
	protected function _build()
	{
		try {
			$this->_copyResources()
				->_buildIndex();
		}
		catch(YaPhpDoc_Core_Exception $e)
		{
			throw $e;
		}
		catch(Exception $e)
		{
			$this->getOutputManager()->error($e->getMessage());
			# TODO : Hide twig & compilation exceptions
			throw new YaPhpDoc_Generator_Exception($this->l10n()->getTranslation('generator')->_(
				'Compilation failed'));
		}
	}
	
	/**
	 * Copy resources in the destination directory.
	 * @throws YaPhpDoc_Generator_Exception
	 * @return YaPhpDoc_Generator_Output_Default
	 */
	protected function _copyResources()
	{
		$resources = $this->getGeneratorConfig()->get('resources', null);
		
		if($resources !== null)
		{
			$this->out()->verbose('Copying theme resources', true, 'generator');
			
			if(is_dir($this->_themeDir.DIRECTORY_SEPARATOR.$resources))
				$method = '_copyDir';
			else
				$method = '_copy';
			$this->$method($this->_themeDir.DIRECTORY_SEPARATOR.$resources,
					$resources);
		}
		
		return $this;
	}
	
	/**
	 * @todo to be done.
	 * @return YaPhpDoc_Generator_Output_Default
	 */
	protected function _buildIndex()
	{
		$template = $this->_twig->loadTemplate('index.html');
		$this->_write('index.html', $this->_render($template));
		
		return $this;
	}
	
	/**
	 * Renders a template (proxy for the Twig_Template render method), adding
	 * global context variables.
	 * 
	 * @param Twig_TemplateInterface $template
	 * @param array $context
	 * @return string
	 */
	protected function _render($template, $context = array())
	{
		return $template->render(array_merge($context, $this->_globalContext));
	}
}
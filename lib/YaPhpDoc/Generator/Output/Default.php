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
 * Each file in the theme directory, except those which begin by an underscore
 * "_" or located under the resources directory ("inc/" in HtmlDefault) are
 * rendered with twig.
 * 
 * Each template using the standard layout must start by
 * <code>{% extends "_layout.html" %}</code>.
 * 
 *  This template will so includes parts of the layout, described into block
 *  markups :
 *    * its content between {% block _content %} and {% endblock %} markups.
 *    * its breadcrumbs is between {% block _breadcrumbs %} and {% endblock %}
 * 
 * Since Twig only supports gettext as translation tool, documentation
 * translations catalogs are using this format. 
 * @todo Uniformize translation with YaPhpDoc core
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
	 * Root of the parsed tokens.
	 * @var YaPhpDoc_Generator_Decorator_Structure
	 */
	protected $_decoratedRoot;
	
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
		
		# Load twig
		try {
			$loader = new Twig_Loader_Filesystem($this->_themeDir);
			$this->_twig = new Twig_Environment($loader, array(
				'debug' => true,
				'strict_variables' => true
			));
			
			# i18n support
			$this->_twig->addExtension(new Twig_Extension_I18n());
		}
		catch(Exception $e)
		{
			throw new YaPhpDoc_Generator_Exception($e->getMessage());
		}
		
		$this->_decoratedRoot = YaPhpDoc_Generator_Decorator_Token
			::getDecorator($this->getDecoratorType(), $this->_root);
		
		# Set twig templates context
		$this->_globalContext['config'] = $this->_config;
		$this->_globalContext['code']	= $this->_decoratedRoot;
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
	 * Returns the decorator type guessed according to the chosen theme or
	 * if not defined for the theme, according to this generator
	 * configuration.
	 * 
	 * The configuration key must exists (this is not checked).
	 * 
	 * @return string
	 */
	public function getDecoratorType()
	{
		return $this->_getConfigKey('decorator');
	}
	
	/**
	 * @see YaPhpDoc/Generator/YaPhpDoc_Generator_Abstract#build()
	 */
	protected function _build()
	{
		try {
			$this->_copyResources();
			
			# Explore theme directory and build all files.
			$this->_buildDirectory();
			
			# Explore tokens and build them
			foreach($this->_decoratedRoot as $token)
			{
				$filename = $token->getUrl();
				
				$this->out()->verbose(sprintf(
					$this->l10n()->getTranslation("generator")->_(
					'Writing file %s'), $filename), false);
				
				if(($tpl_file = $this->_getTemplateFile($token->getTokenType())) !== null)
				{
					# Load the template matching the token type.
					$template = $this->_twig->loadTemplate($tpl_file);
					
					# render and save the file.
					$this->_write($filename, $this->_render(
						$template,
						array('base_url' => $this->_findBaseUrl($filename))
					));
				}
			}
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
		$resources = $this->_getConfigKey('resources');
		
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
	protected function _buildDirectory()
	{
		$dirIterator = new FilesystemIterator($this->_themeDir);
		$resources_dir = $this->_getConfigKey('resources');
		while($dirIterator->valid())
		{
			$current = $dirIterator->current();
			/* @var $current SplFileInfo */
			$filename = $current->getFilename();
			if($current->isDir())
			{
				// Build sub directory
				if($filename != $resources_dir)
					$this->_buildDirectory($dirIterator->key());	
			}
			elseif($filename[0] != '_')
			{
				$this->out()->verbose(sprintf(
					$this->l10n()->getTranslation("generator")->_(
					'Writing file %s'),$filename), false);
				
				$template = $this->_twig->loadTemplate($filename);
				$this->_write($filename, $this->_render(
					$template,
					array('base_url' => $this->_findBaseUrl($filename))
				));
			}
			
			$dirIterator->next();
		}
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
		return $template->render(array_merge($this->_globalContext, $context));
	}
	
	/**
	 * Returns a config key according to the selected theme or the key defined
	 * for the generator scope if not overriden.
	 * 
	 * If the key is not defined for the both scopes, $default is returned.
	 * 
	 * @param string $key
	 * @param mixed $default  
	 * @return mixed
	 */
	protected function _getConfigKey($key, $default = null)
	{
		$cfg = $this->getGeneratorConfig();
		$theme_cfg = $cfg->get($this->_theme);
		
		if($theme_cfg !== null && ($value = $theme_cfg->get($key)) !== null)
		{
			return $value;
		}
		
		return $cfg->get($key, $default);
	}
	
	/**
	 * Find the name of the template to use for a token type.
	 * 
	 * @param string $token_type
	 * @return string|null
	 */
	protected function _getTemplateFile($token_type)
	{
		$cfg = $this->getGeneratorConfig();
		if(
			($theme_cfg = $cfg->get($this->_theme)) !== null
		 && ($tpl_cfg = $theme_cfg->get('templates')) !== null
		 && ($template = $tpl_cfg->get($token_type)) !== null
		)
		{
			return $template;
		}
		
		return $cfg->get('templates')->get($token_type);
	}
	
	/**
	 * Try to determinates the base url of included resources according to
	 * the depth of the file location.
	 * 
	 * @param string $filename
	 * @return string
	 */
	protected function _findBaseUrl($filename, $ds = '/')
	{
		if(strpos($filename, $ds) !== false 
		 && ($depth = substr_count($filename, $ds, 1, strlen($filename)-1)) > 0)
		{
			return str_repeat('..'.$ds, $depth);
		}
		return '';
	}
}

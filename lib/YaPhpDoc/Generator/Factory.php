<?php
/**
 * YaPhpDoc
 * @author Martin Richard
 * @license New BSD License
 */

/**
 * The Generator factory returns an object implementing
 * YaPhpDoc_Generator_Abstract which generates the documentation files
 * into the desired output format.
 * 
 * A generator must inherits YaPhpDoc_Generator_Abstract and be named
 * YaPhpDoc_Generator_Output_Format where 'Format' is the output format,
 * with a capitalized first letter. You should follow the filesystem
 * organization to allow the Zend autoloader to find thie file defining the
 * class.
 * 
 * This class can not be instanciated.
 * 
 * @author Martin Richard
 */
class YaPhpDoc_Generator_Factory
{
	/**
	 * Returns the generator for the given format.
	 * 
	 * Throws an exception if the generator can not be found.
	 * 
	 * @param string $output_format
	 * @param YaPhpDoc_Core_OutputManager_Interface $outputManager
	 * @param YaPhpDoc_Core_TranslationManager_Interface $translationManager
	 * @param string $data_dir Path to the data/ directory
	 * 
	 * @throws YaPhpDoc_Generator_Exception
	 * @return YaPhpDoc_Generator_Abstract
	 */
	public static function getGenerator($output_format,
		YaPhpDoc_Core_OutputManager_Interface $outputManager,
		YaPhpDoc_Core_TranslationManager_Interface $translationManager,
		$data_dir)
	{
		$output_generator_class = 'YaPhpDoc_Generator_Output_'
			.ucfirst($output_format);
		
		if(!YaPhpDoc_Tool_Loader::classExists($output_generator_class))
		{
			throw new YaPhpDoc_Generator_Exception(sprintf(
				$translationManager->getTranslation('generator')->_(
				'Generator for format %s does not exists.'),
				$output_format
			));
		}
		
		return new $output_generator_class($outputManager, $translationManager, $data_dir);
	}
	
	private function __construct()
	{
	}
}
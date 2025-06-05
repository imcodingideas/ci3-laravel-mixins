<?php

/**
 * CodeIgniter Test Bootstrap.
 * 
 * Simplified CodeIgniter initialization for testing
 * - No routing
 * - No controller execution
 * - No output rendering
 * - Only core classes initialization
 */
defined('BASEPATH') || exit('No direct script access allowed');

/*
 * ------------------------------------------------------
 *  Load the framework constants
 * ------------------------------------------------------
 */
	if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/constants.php'))
	{
		require_once APPPATH . 'config/' . ENVIRONMENT . '/constants.php';
	}

	if (file_exists(APPPATH . 'config/constants.php'))
	{
		require_once APPPATH . 'config/constants.php';
	}

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
	require_once BASEPATH . 'core/Common.php';

/*
 * ------------------------------------------------------
 * Security procedures (minimal for testing)
 * ------------------------------------------------------
 */
if ( !is_php('5.4'))
{
	ini_set('magic_quotes_runtime', 0);
}

/*
 * ------------------------------------------------------
 *  Define error handlers (suppress for testing)
 * ------------------------------------------------------
 */
	set_error_handler('_error_handler');
	set_exception_handler('_exception_handler');
	register_shutdown_function('_shutdown_handler');

/*
 * ------------------------------------------------------
 *  Set the subclass_prefix
 * ------------------------------------------------------
 */
	if ( !empty($assign_to_config['subclass_prefix']))
	{
		get_config(['subclass_prefix' => $assign_to_config['subclass_prefix']]);
	}

/*
 * ------------------------------------------------------
 *  Should we use a Composer autoloader?
 * ------------------------------------------------------
 */
	if ($composer_autoload = config_item('composer_autoload'))
	{
		if ($composer_autoload === TRUE)
		{
			file_exists(APPPATH . 'vendor/autoload.php')
				? require_once(APPPATH . 'vendor/autoload.php')
				: log_message('error', '$config[\'composer_autoload\'] is set to TRUE but ' . APPPATH . 'vendor/autoload.php was not found.');
		}
		elseif (file_exists($composer_autoload))
		{
			require_once $composer_autoload;
		}
	}

/*
 * ------------------------------------------------------
 *  Start the timer
 * ------------------------------------------------------
 */
	$BM = &load_class('Benchmark', 'core');
	$BM->mark('total_execution_time_start');
	$BM->mark('loading_time:_base_classes_start');

/*
 * ------------------------------------------------------
 *  Instantiate the config class
 * ------------------------------------------------------
 */
	$CFG = &load_class('Config', 'core');

	// Manual config items
	if (isset($assign_to_config) && is_array($assign_to_config))
	{
		foreach ($assign_to_config as $key => $value)
		{
			$CFG->set_item($key, $value);
		}
	}

/*
 * ------------------------------------------------------
 * Charset configuration
 * ------------------------------------------------------
 */
	$charset = strtoupper(config_item('charset'));
	ini_set('default_charset', $charset);

	if (extension_loaded('mbstring'))
	{
		define('MB_ENABLED', TRUE);
		@ini_set('mbstring.internal_encoding', $charset);
		mb_substitute_character('none');
	}
	else
	{
		define('MB_ENABLED', FALSE);
	}

	if (extension_loaded('iconv'))
	{
		define('ICONV_ENABLED', TRUE);
		@ini_set('iconv.internal_encoding', $charset);
	}
	else
	{
		define('ICONV_ENABLED', FALSE);
	}

	if (is_php('5.6'))
	{
		ini_set('php.internal_encoding', $charset);
	}

/*
 * ------------------------------------------------------
 *  Load compatibility features
 * ------------------------------------------------------
 */
	require_once BASEPATH . 'core/compat/mbstring.php';
	require_once BASEPATH . 'core/compat/hash.php';
	require_once BASEPATH . 'core/compat/password.php';
	require_once BASEPATH . 'core/compat/standard.php';

/*
 * ------------------------------------------------------
 *  Instantiate core classes needed for testing
 * ------------------------------------------------------
 */
	$UNI = &load_class('Utf8', 'core');
	$SEC = &load_class('Security', 'core');
	$IN = &load_class('Input', 'core');
	$LANG = &load_class('Lang', 'core');

/*
 * ------------------------------------------------------
 *  Load the base controller class (for get_instance)
 * ------------------------------------------------------
 */
	require_once BASEPATH . 'core/Controller.php';

	/**
	 * Reference to the CI_Controller method.
	 *
	 * Returns current CI instance object
	 *
	 * @return CI_Controller
	 */
	function &get_instance()
	{
		return CI_Controller::get_instance();
	}

	if (file_exists(APPPATH . 'core/' . $CFG->config['subclass_prefix'] . 'Controller.php'))
	{
		require_once APPPATH . 'core/' . $CFG->config['subclass_prefix'] . 'Controller.php';
	}

	// Set benchmark end point
	$BM->mark('loading_time:_base_classes_end');

/*
 * ------------------------------------------------------
 *  Create minimal CI instance for testing
 * ------------------------------------------------------
 */
	$CI = new CI_Controller();

/*
 * ------------------------------------------------------
 *  Test framework is ready
 * ------------------------------------------------------
 *  
 *  At this point we have:
 *  - All CodeIgniter core classes loaded
 *  - Configuration system initialized  
 *  - Database system available via $CI->load->database()
 *  - Model loading available via $CI->load->model()
 *  - No routing, no controller execution, no output
 */ 

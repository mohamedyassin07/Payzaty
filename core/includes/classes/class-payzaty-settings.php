<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Payzaty_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		PAYZATY
 * @subpackage	Classes/Payzaty_Settings
 * @author		PayZaty
 * @since		1.0.0
 */
class Payzaty_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;

	/**
	 * Our Payzaty_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){

		$this->plugin_name = PAYZATY_NAME;
	}

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'PAYZATY/settings/get_plugin_name', $this->plugin_name );
	}
}

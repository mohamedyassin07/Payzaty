<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Payzaty_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		PAYZATY
 * @subpackage	Classes/Payzaty_Run
 * @author		PayZaty
 * @since		1.0.0
 */
class Payzaty_Run{

	/**
	 * Our Payzaty_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
		add_action( 'plugin_action_links_' . PAYZATY_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
		add_action( 'plugins_loaded', array( $this, 'init_payzaty_payment_class' ) );
		add_filter( 'woocommerce_payment_gateways', array( $this,'add_payzaty_payment_class' ) );
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links ) {
		$settings_link 	= admin_url( 'admin.php?page=wc-settings&tab=checkout&section=payzaty', 'https' );
		$label_text		= __( 'Settings', 'payzaty' );
		$links['wc_settings'] = sprintf( '<a href="%s" title="%s" style="font-weight:700;">%s</a>', $settings_link, $label_text, $label_text );

		return $links;
	}

	/**
	* initilize Payzaty Payment Class
	*
	* @access	public
	* @since	1.0.0
	*/
	public function init_payzaty_payment_class() {
		require_once PAYZATY_PLUGIN_DIR . 'core/includes/classes/class-payzaty-wc-payment.php';
	}

	/**
	* Adds PayZaty Payment Class
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$methods An array of woocommerce payment gateways.
	*/
	public function add_payzaty_payment_class( $methods ) {
		$methods[] = 'Payzaty_WC_Payment'; 
		return $methods;
	}
}

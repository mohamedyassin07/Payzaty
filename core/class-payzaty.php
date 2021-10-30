<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Payzaty' ) ) :

	/**
	 * Main Payzaty Class.
	 *
	 * @package		PAYZATY
	 * @subpackage	Classes/Payzaty
	 * @since		1.0.0
	 * @author		Payzaty
	 */
	final class Payzaty {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Payzaty
		 */
		private static $instance;

		/**
		 * PAYZATY settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Payzaty_Settings
		 */
		public $settings;

		/**
		 * Website custom endpoints to recive responses and updates from the gateway.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Payzaty_Custom_End_Points
		 */
		public $payzaty_custom_endpoints;

		/**
		 * Throw error on object clone.
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'payzaty' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'payzaty' ), '1.0.0' );
		}

		/**
		 * Main Payzaty Instance.
		 * Insures that only one instance of Payzaty exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Payzaty	The one true Payzaty
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Payzaty ) ) {
				self::$instance								= new Payzaty;
				self::$instance->includes();
				self::$instance->base_hooks();
				self::$instance->settings					= new Payzaty_Settings();
				self::$instance->payzaty_custom_endpoints	= new Payzaty_Custom_End_Points();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'PAYZATY/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once PAYZATY_PLUGIN_DIR . 'core/includes/classes/class-payzaty-settings.php';
			require_once PAYZATY_PLUGIN_DIR . 'core/includes/classes/class-payzaty-gateway-api-connecting.php';
			require_once PAYZATY_PLUGIN_DIR . 'core/includes/classes/class-payzaty-custom-endpoints.php';
		}

		/**
		 * Add base hooks for the core functionality
		 * 
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
			add_action( 'plugin_action_links_' . PAYZATY_PLUGIN_BASE, array( $this, 'add_payment_settings_page_link' ), 20 );
			add_action( 'plugins_loaded', array( $this, 'init_payzaty_payment_class' ) );
			add_filter( 'woocommerce_payment_gateways', array( $this,'add_payzaty_payment_class' ) );	
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'payzaty', FALSE, dirname( plugin_basename( PAYZATY_PLUGIN_FILE ) ) . '/languages/' );
		}

		/**
		* Adds action links to the plugin list table
		*
		* @access	public
		* @since	1.0.0
		* @param	array	$links An array of plugin action links.
		* @return	array	An array of plugin action links.
		*/
		public function add_payment_settings_page_link( $links ) {
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
		* Adds Payzaty Payment Class
		*
		* @access	public
		* @since	1.0.0
		* @param	array	$methods An array of woocommerce payment gateways.
		*/
		public function add_payzaty_payment_class( $methods ) {
			$methods[] = 'Payzaty_WC_Payment'; 
			return $methods;
		}

	}

endif; // End if class_exists check.
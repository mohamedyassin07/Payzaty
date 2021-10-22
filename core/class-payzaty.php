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
	 * @author		PayZaty
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
		 * PAYZATY API handler object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Payzaty_API
		 */
		public $payzaty_api;

		/**
		 * Throw error on object clone.
		 *
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
		 *
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
				self::$instance					= new Payzaty;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->settings		= new Payzaty_Settings();
				self::$instance->payzaty_api	= new Payzaty_API();

				//Fire the plugin logic
				new Payzaty_Run();

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
			require_once PAYZATY_PLUGIN_DIR . 'core/includes/classes/class-payzaty-api.php';
			require_once PAYZATY_PLUGIN_DIR . 'core/includes/classes/class-payzaty-run.php';
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

	}

endif; // End if class_exists check.
<?php
/**
 * Payzaty
 *
 * @package       PAYZATY
 * @author        Payzaty
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Payzaty
 * Plugin URI:    https://www.payzaty.com
 * Description:   Payzaty WooCommerce plugin
 * Version:       1.0.0
 * Author:        Payzaty
 * Author URI:    https://www.payzaty.com
 * Text Domain:   payzaty
 * Domain Path:   /languages
 */

// Don't work if WooCommerce is not installed or being updating.
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin name
define( 'PAYZATY_NAME',			'Payzaty' );

// Plugin version
define( 'PAYZATY_VERSION',		'1.0.0' );

// Plugin Root File
define( 'PAYZATY_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'PAYZATY_PLUGIN_BASE',	plugin_basename( PAYZATY_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'PAYZATY_PLUGIN_DIR',	plugin_dir_path( PAYZATY_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'PAYZATY_PLUGIN_URL',	plugin_dir_url( PAYZATY_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'CHECKBOX_TRUE_VAL',	'yes' );

/**
 * Load the main class for the core functionality
 */
require_once PAYZATY_PLUGIN_DIR . 'core/class-payzaty.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Payzaty
 * @since   1.0.0
 * @return  object|Payzaty
 */
function PAYZATY() {
	return Payzaty::instance();
}

PAYZATY();
<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Payzaty_WC_Payment
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package		PAYZATY
 * @subpackage	Classes/Payzaty_WC_Payment
 * @author		Payzaty
 * @since		1.0.0
 */

class Payzaty_WC_Payment extends WC_Payment_Gateway {

	/**
	 * checkbox true value
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $checkbox_true_val =  'yes';

	public function __construct(){
		// Set General Payment Method Data
		$this->id = 'payzaty';
		$this->icon = PAYZATY_PLUGIN_URL .'/assets/payzaty-logo.png';
		$this->has_fields =  false ;
		$this->method_title = __( 'Payzaty', 'payzaty' );
		$this->method_description = __( 'Payzaty Gateway Settings', 'payzaty' );

		$this->init_form_fields(); // admin settings page fields
		
		// Load the settings.
		$this->init_settings();
		$this->title = $this->get_option( 'title' );

		// add the related hooks
		$this->add_hooks();
	}

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 */
	private function add_hooks(){
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_api_wc_gateway_paypal', array( $this, 'check_ipn_response' ) );
	}

	/**
	 * Registers WooCommerce Admin Fields
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'payzaty' ),
				'type' => 'checkbox',
				'label' => __( 'Enable Payzaty', 'payzaty' ),
				'default' => $this->checkbox_true_val,
			),
			'title' => array(
				'title' => __( 'Title', 'payzaty' ),
				'type' => 'text',
				'description' => __( 'This controls the title which will appears in chechout page.', 'payzaty' ),
				'default' => __( 'Payzaty Payment', 'payzaty' ),
			),
			'sandbox' => array(
				'title' => __( 'Enable Sandbox', 'payzaty' ),
				'type' => 'checkbox',
				'label' => __( 'Sandbox enables a testing environment to test the whole process before you go production.', 'payzaty' ),
				'default' => $this->checkbox_true_val,
			),
			'merchant_id' => array(
				'title' => __( 'Merchant No', 'payzaty' ),
				'type' => 'text',
			),
			'secret_key' => array(
				'title' => __( 'Secret Key', 'payzaty' ),
				'type' => 'text',
			),
		);			
	}

	/**
	 * Process the payment
	 *
	 * @access	public
	 * @since	1.0.0
	 */
	public function process_payment( $order_id ) {
		$billing_details =  $this->billing_details($order_id);

		if($billing_details ==  false){
			wc_add_notice( __('Missing Data' , 'payzaty')  , 'error' );
			return;
		}

		$connection = new Payzaty_Gate_Way_API_Connecting( $this->get_option('sandbox'), $this->get_option('merchant_id'), $this->get_option('secret_key') );
		$checkout_data = $connection->create_new_chechout_order( $billing_details, $order_id);
		
		if($checkout_data === false){
			wc_add_notice( __('Some things went wrong when connecting to Payzaty, Please try again.' , 'payzaty')  , 'error' );
			return;
		}

		update_post_meta( $order_id, 'payzaty_checkout_id', $checkout_data['checkout_id'] );

		return array(
			'result' => 'success',
			'redirect' => $checkout_data['url']
		);
	}

	/**
	 * billing_details 
	 * 
	 * @access public
	 * @since	1.0.0
	 * 
	 * @param	string $order_id is the current order id
	 * 
	 * @return	array prepared array of the billings details contains all the required data
	 */
	public function billing_details($order_id){
		$order = new WC_Order( $order_id );

		return array(
			'Name'  => $order->get_billing_first_name(). ' ' . $order->get_billing_last_name(),
			'Email' => $order->get_billing_email(),
			'PhoneCode' => '000', // no more codes untill now
			'PhoneNumber' => $order->get_billing_phone(),
			'Amount' => $order->get_total(),
			'CurrencyID' => 1, // no more curruncies untill now
			'UDF1' => $order_id,
			'UDF2' => '',
			'UDF3' => '',
		);
	}

}
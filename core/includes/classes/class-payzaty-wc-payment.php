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
 * @author		PayZaty
 * @since		1.0.0
 */

class Payzaty_WC_Payment extends WC_Payment_Gateway {
	public function __construct(){
		// Set General Payment Method Data
		$this->id = 'payzaty';
		$this->icon = PAYZATY_PLUGIN_URL .'/assets/payzaty-logo.png';
		$this->has_fields =  false ;
		$this->method_title = __( 'PayZaty', 'payzaty' );
		$this->method_description = __( 'PayZaty Gateway Settings', 'payzaty' );

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
	 * @return	void
	 */
	private function add_hooks(){
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_api_wc_gateway_paypal', array( $this, 'check_ipn_response' ) );
	}

	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'payzaty' ),
				'type' => 'checkbox',
				'label' => __( 'Enable PayZaty Payment Gateway', 'payzaty' ),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __( 'Title', 'payzaty' ),
				'type' => 'text',
				'description' => __( 'This controls the title which will appears in chechout page.', 'payzaty' ),
				'default' => __( 'PayZaty Payment Gateway', 'payzaty' ),
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

	public function process_payment( $order_id ) {
		$billing_details =  $this->billing_details($order_id);
		if($billing_details ==  false){
			wc_add_notice( __('Missing Data' , 'payzaty')  , 'error' );
			return;
		}

		$checkout_data = $this->get_checkout_data($billing_details);

		if($checkout_data === false){
			wc_add_notice( __('Some thing Went Wrong' , 'payzaty')  , 'error' );
			return;
		}
		return array(
			'result' => 'success',
			'redirect' => $checkout_data['url']
		);
	}

	public function billing_details($order_id){
		$order = new WC_Order( $order_id );
		
		return array(
			'Name'  => $_POST['billing_first_name'] . ' ' . $_POST['billing_last_name']  ,
			'Email' => $_POST['billing_email'],
			'PhoneCode' => '000',
			'PhoneNumber' => $_POST['billing_phone'],
			'Amount' => $order->get_total(),
			'CurrencyID' => 1,
			'UDF1' => $order_id,
			'UDF2' => '',
			'UDF3' => '',
			'ResponseUrl' => $this->get_response_url($order_id),
		);
	}


	public function get_response_url($order_id){
		return get_rest_url() . 'wc/v3/payzaty_confirmation/'.$order_id;
	}

	public function get_checkout_data($billing_details){
		$api_url = 'https://www.payzaty.com/payment/checkout';			
		$headers = array(
		  'X-Source' => 8,
		  'X-Build' => 1,
		  'X-Version' => 1,
		  'X-Language' => 'ar',
		  'X-MerchantNo' => $this->get_option('merchant_id'),
		  'X-SecretKey' => $this->get_option('secret_key') , 
		  'Content-Type' => 'application/x-www-form-urlencoded',
		);
		echo "sss " .  
		$response = wp_remote_post( 
		  $api_url,
		  array(
			'timeout' => 10,
			'headers' => $headers,
			'body'        => $billing_details,
			'method'      => 'POST',
		  )
		);
		$body = wp_remote_retrieve_body($response);
		$body = json_decode($body ,true);

		if($body['success'] == true && isset($body['checkoutUrl']) ){
			return array('id' => $body['checkoutId'] , 'url' => $body['checkoutUrl']);
		}
		return false;
	}
}
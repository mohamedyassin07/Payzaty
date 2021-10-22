<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Payzaty_API
 *
 * This class contains repetitive functions that
 * are create the api related functions
 *
 * @package		PAYZATY
 * @subpackage	Classes/Payzaty_API
 * @author		PayZaty
 * @since		1.0.0
 */

class Payzaty_API  {
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'payzaty_confirmation_endpoint') );
	}

	/**
	 * Endpoint for payment confirmation 
	 *
	 * Payzaty need this endpoint to forward the payment process result.
	 *
	 * @access		public
	 * @since		1.0.0
	 */
	public function payzaty_confirmation_endpoint(){
		register_rest_route( 'wc/v3', 'payzaty_confirmation/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array($this,'payzaty_confirmation_endpoint_callback'),
			'permission_callback' => '__return_true'
		));
	}

	public function payzaty_confirmation_endpoint_callback( $request ) {
		global $woocommerce;
		$order = new WC_Order( $request->get_params()['id'] );
		$order->payment_complete();

		wp_redirect( $order->get_checkout_order_received_url() );
		exit();

		return array( 'custom' => 'Data' , "request"=> $request->get_params() , 'resp' => $order->payment_complete() , 'url' => $order->get_checkout_order_received_url() );
	}
}
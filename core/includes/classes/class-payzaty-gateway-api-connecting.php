<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Payzaty_Gate_Way_API_Connecting
 *
 * This class contains repetitive functions that
 * are create the api related functions
 *
 * @package		PAYZATY
 * @subpackage	Classes/Payzaty_Gate_Way_API_Connecting
 * @author		Payzaty
 * @since		1.0.0
 */

class Payzaty_Gate_Way_API_Connecting  {
	
	public function __construct($sandbox, $no, $secret){
		$this->sandbox = $sandbox;
		$this->merchant_no = $no;
		$this->merchant_secret = $secret;
	}

	/**
	 * Execute a connection to Payzaty API
	 * @access	public
	 * @since	1.0.0
	 * @return	array	needed data from the connection
	 */
	public function create_connection( $url, $type = 'POST', $body = array() ){
		$headers = array(
		  'X-Source' => 8, // 8:WooCommerce
		  'X-Build' => 1,
		  'X-Version' => 1,
		  'X-Language' => 'ar',
		  'X-MerchantNo' => $this->merchant_no,
		  'X-SecretKey' => $this->merchant_secret , 
		  'Content-Type' => 'application/x-www-form-urlencoded',
		);
		$response = wp_remote_post( 
		  $url,
		  array(
			'method' => $type,
			'headers' => $headers,
			'timeout' => 10,
			'body' => $body,
		  )
		);
		return json_decode( wp_remote_retrieve_body( $response ), true );

		if( $body['success'] == true && isset($body['checkoutUrl']) ){
			return array( 'id' => $body['checkoutId'], 'url' => $body['checkoutUrl'], 'checkout_id' => $body['checkoutId'] );
		}
		return false;
	}

	/**
	 * Get the full URL the connection will use
	 * 
	 * @access	public
	 * @since	1.0.0
	 * @return	string	url to the sandbox/live enviroment and the required end point
	 */
	public function get_url( $path = '' ){
		return $this->sandbox === CHECKBOX_TRUE_VAL ? 'https://sandbox.payzaty.com/payment/'.$path : 'https://www.payzaty.com/payment/'.$path;
	}

	/**
	 * Get checkout status from payzaty API
	 * 
	 * @access	public
	 * @since	1.0.0
	 * @return	array	paymanet process status
	 */
	public function get_checkout_status( $code ){
		$url = $this->get_url('status/'. $code );
		return $this->create_connection( $url, 'GET');
	}

	/**
	 * create a new checkout request
	 * 
	 * @access	public
	 * @since	1.0.0
	 * @return	array	new chechout request data
	 */
	public function create_new_chechout_order( $body, $order_id ){
		$confirmation_endpoint_data = Payzaty_Custom_End_Points::confirmation_endpoint_data();
		$body['ResponseUrl'] = get_rest_url(). $confirmation_endpoint_data['namespace']. '/'. $confirmation_endpoint_data['route']. '/'. $order_id;

		$url 		= $this->get_url('checkout');
		$response 	= $this->create_connection($url, 'POST', $body);

		if( $response['success'] == true && isset($response['checkoutUrl']) ){
			return array( 'id' => $response['checkoutId'], 'url' => $response['checkoutUrl'], 'checkout_id' => $response['checkoutId'] );
		}

		return false;
	}

}
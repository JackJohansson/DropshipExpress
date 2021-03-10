<?php

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Api_zinc
	 *
	 * Class to interact with ZincAPI to process
	 * the orders
	 *
	 */
	class DSE_Api_zinc {

		const API_URL = 'https://api.zinc.io/v1/orders';

		/**
		 * @param array $product_data
		 * @param array $data
		 *
		 * @return \WP_Error|string
		 */
		public static function Create_Order( array $product_data, array $data ) {

			// Request data
			$arguments = [
				'idempotency_key'      => $product_data[ 'uniqid' ],
				'retailer'             => $product_data[ 'source' ],
				'products'             => [
					'product_id' => $product_data[ 'sku' ],
					'quantity'   => $product_data[ 'quantity' ],
				],
				'shipping_address'     => [
					'first_name'    => $data[ 'shipping_address' ][ 'first_name' ],
					'last_name'     => $data[ 'shipping_address' ][ 'last_name' ],
					'address_line1' => $data[ 'shipping_address' ][ 'address_line1' ],
					'address_line2' => $data[ 'shipping_address' ][ 'address_line2' ],
					'zip_code'      => $data[ 'shipping_address' ][ 'zip_code' ],
					'city'          => $data[ 'shipping_address' ][ 'city' ],
					'state'         => $data[ 'shipping_address' ][ 'state' ],
					'country'       => $data[ 'shipping_address' ][ 'country' ],
					'phone_number'  => $data[ 'shipping_address' ][ 'phone_number' ],
				],
				'billing_address'      => [
					'first_name'    => $data[ 'billing_address' ][ 'first_name' ],
					'last_name'     => $data[ 'billing_address' ][ 'last_name' ],
					'address_line1' => $data[ 'billing_address' ][ 'address_line1' ],
					'address_line2' => $data[ 'billing_address' ][ 'address_line2' ],
					'zip_code'      => $data[ 'billing_address' ][ 'zip_code' ],
					'city'          => $data[ 'billing_address' ][ 'city' ],
					'state'         => $data[ 'billing_address' ][ 'state' ],
					'country'       => $data[ 'billing_address' ][ 'country' ],
					'phone_number'  => $data[ 'billing_address' ][ 'phone_number' ],
				],
				'is_gift'              => FALSE,
				'retailer_credentials' => [
					'email'        => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'login_user' ),
					'password'     => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'login_pass' ),
					'totp_2fa_key' => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'login_auth' ),
				],
				'webhooks'             => [
					'request_succeeded' => DSE_shipping::Callback_Url( 'success' ),
					'request_failed'    => DSE_shipping::Callback_Url( 'failed' ),
					'tracking_obtained' => DSE_shipping::Callback_Url( 'track' ),
				],
				'payment_method'       => [
					'name_on_card'     => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'card_name' ),
					'number'           => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'card_number' ),
					'security_code'    => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'card_cvv' ),
					'expiration_month' => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'card_expiry_m' ),
					'expiration_year'  => DSE_Settings::Get_Setting( $product_data[ 'source' ], 'card_expiry_y' ),
					'use_gift'         => FALSE,
				],
				'max_price'            => -1,
				'shipping_method'      => 'cheapest ',
				'client_note'          => [
					'uniqid'     => $product_data[ 'uniqid' ],
					'product_id' => $product_data[ 'id' ],
					'order_id'   => $product_data[ 'order_id' ],
				],
			];

			// Make the request
			$request_args = [
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( DSE_Settings::Get_Setting( $product_data[ 'source' ], 'zinc_token' ) ),
				],
				'body'    => $arguments,
			];

			$request       = wp_remote_post( self::API_URL, $request_args );
			$response_body = wp_remote_retrieve_body( $request );

			if ( ! is_wp_error( $request ) ) {
				// If response is empty
				if ( empty( $response_body ) ) {
					return new WP_Error( 'dse_order_api_empty', esc_html__( 'Failed to purchase the product. Ordering API has replied with an empty response.', 'dropshipexpress' ) );
				}
				// Return the tracking ID
				return $response_body;
			} else {
				return $request;
			}

		}
	}
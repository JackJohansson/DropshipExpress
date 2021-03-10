<?php

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_shipping
	 *
	 * Used to integrate with shipping APIs for
	 * automated product shipping
	 *
	 */
	class DSE_Shipping {

		/**
		 * Private property to hold the instance of
		 * a shipping api
		 *
		 * @var \DSE_Api_zinc $api_instance
		 */
		private static $api_instance;

		/**
		 * Holds the name of the current api
		 *
		 * @var $api_name
		 */
		private static $api_name;

		/**
		 * DSE_Shipping constructor.
		 *
		 * @param $args
		 */
		public function __construct( $args ) {

		}

		/**
		 * Add a cron job to automatically purchase the
		 * placed orders
		 *
		 * @param int $order_id
		 */
		public static function Add_Order_Cron( int $order_id ) {

			$orders = get_option( 'dse_orders', [] );

			// If the order does not exist, queue it
			if ( ! in_array( $order_id, $orders ) ) {
				$orders[] = $order_id;
			}

		}

		/**
		 * Method to get a callback webhook based on
		 * the callback's action
		 *
		 * @param string $string Name of the callback action
		 *
		 * @return string        Url to the callback action
		 */
		public static function Callback_Url( string $string ) {
			$routes = [
				'success' => get_rest_url( NULL, '/dse/v1/order-api/success/' ),
				'failed'  => get_rest_url( NULL, '/dse/v1/order-api/failed/' ),
				'track'   => get_rest_url( NULL, '/dse/v1/order-api/track/' ),
			];

			// Return the proper route
			if ( key_exists( $string, $routes ) ) {
				return $routes[ $string ];
			}

			return '';
		}

		/**
		 * Method to process the queued orders via the
		 * order API
		 *
		 */
		public static function Process_Crons() {

			$queued_orders = get_option( 'dse_orders', [] );

			if ( $queued_orders ) {
				foreach ( $queued_orders as $order_id ) {
					// Schedule a cronjob
					if ( ! wp_next_scheduled( 'dse_process_order_cron', [ 'dse_order_id' => $order_id ] ) ) {
						wp_schedule_event( time(), 'dse_order_cron', 'dse_process_order_cron', [ 'dse_order_id' => $order_id ] );
					}
				}
			}
		}

		/**
		 * Method to purchase and send a product automatically
		 * to a customer using shipping Apis
		 *
		 * @param $args
		 *
		 * @return bool
		 */
		public static function Process_Order( $args ) {

			if ( ! isset( $args[ 'dse_order_id' ] ) ) {
				return FALSE;
			}

			$order_id = (int) $args[ 'dse_order_id' ];

			// Get the order and verify its product is managed by this plugin
			$order = wc_get_order( $order_id );

			if ( FALSE === $order ) {
				return FALSE;
			}

			// First, check if this order has been processed before
			if ( 'yes' === get_post_meta( $order_id, 'dse_order_processing', TRUE ) ) {
				return self::Update_Order( $order_id );
			}

			$dse_products      = [];
			$extra_products     = [];
			$purchased_products = [];
			$api_errors         = [];

			// Loop through all the items in the order
			foreach ( $order->get_items() as $item_id => $item ) {

				if ( 'yes' === get_post_meta( $item->get_product_id(), 'dse_product', TRUE ) ) {

					$source     = get_post_meta( $item->get_product_id(), 'dse_source', TRUE );
					$remote_url = get_post_meta( $item->get_product_id(), 'dse_product_url', TRUE );
					$product_id = get_post_meta( $item->get_product_id(), 'dse_product_id', TRUE );

					// Check if the auto shipping is enabled
					if ( 'yes' !== DSE_Settings::Get_Setting( $source, 'auto_ship' ) ) {
						continue;
					}

					// Check if the credentials are set
					if ( ! self::Check_Credentials( $source ) ) {
						continue;
					}

					// Push this product into the array
					if ( ! empty( $source ) && ! empty( $product_id ) ) {

						// Get product's variation sku, if available
						if ( $item->get_variation_id() ) {
							$product = wc_get_product( $item->get_variation_id() );
						} else {
							$product = wc_get_product( $item->get_variation_id() );
						}

						$dse_products[ uniqid() ] = [
							'uniqid'       => uniqid(),
							'id'           => $item->get_product_id(),
							'order_id'     => $order_id,
							'product_id'   => $product_id,
							'product_url'  => get_permalink( $product_id ),
							'remote_url'   => $remote_url,
							'variation_id' => $item->get_variation_id(),
							'quantity'     => $item->get_quantity(),
							'sku'          => $product && '' !== $product->get_sku() ? $product->get_sku() : FALSE, // sku is required
							'source'       => $source,
							'api_name'     => 'zinc', // todo:add other apis
							'enabled_api'  => DSE_Settings::Get_Setting( $source, 'auto_ship' ),
						];
					}
				} else {
					// Set a flag if there's a product in the order that is not
					// handled by this plugin
					$extra_products[] = [
						'id'    => $item->get_product_id(),
						'title' => get_the_title( $item->get_id() ),
						'url'   => get_permalink( $item->get_id() ),
					];
				}
			}

			// If there's any product added by this plugin, proceed
			if ( empty( $dse_products ) ) {
				return FALSE;
			}

			// Fill the rest of the order data
			$api_basic_data = [
				'order_id'         => $order_id,
				'shipping_address' => [
					'first_name'    => $order->get_shipping_first_name(),
					'last_name'     => $order->get_shipping_last_name(),
					'address_line1' => $order->get_shipping_address_1(),
					'address_line2' => $order->get_shipping_address_2(),
					'zip_code'      => $order->get_shipping_postcode(),
					'city'          => $order->get_shipping_city(),
					'state'         => $order->get_shipping_state(),
					'country'       => $order->get_shipping_country(),
					'phone_number'  => $order->get_billing_phone(),
				],
				'billing_address'  => [
					'first_name'    => $order->get_billing_first_name(),
					'last_name'     => $order->get_billing_last_name(),
					'address_line1' => $order->get_billing_address_1(),
					'address_line2' => $order->get_billing_address_2(),
					'zip_code'      => $order->get_billing_postcode(),
					'city'          => $order->get_billing_city(),
					'state'         => $order->get_billing_state(),
					'country'       => $order->get_billing_country(),
					'phone_number'  => $order->get_billing_phone(),
				],
			];

			// Load the proper Api and process the request
			foreach ( $dse_products as $dse_product ) {

				$api_loaded = self::Load_API( $dse_product[ 'api_name' ] );

				// If the Api exists, call it and place the order
				if ( FALSE !== $api_loaded && FALSE !== $dse_product[ 'sku' ] ) {

					$api_response = $api_loaded::Create_Order( $dse_product, $api_basic_data );

					// Check the response
					if ( ! is_wp_error( $api_response ) ) {
						$purchased_products [ $dse_product[ 'uniqid' ] ] = [
							'id'         => $dse_product[ 'id' ],
							'request_id' => $api_response,
							'title'      => get_the_title( $dse_product[ 'id' ] ),
							'url'        => $dse_product[ 'product_url' ],
							'remote_url' => $dse_product[ 'remote_url' ],
						];
					} else {
						// Log the error
						DSE_Core::Log_Error(
							sprintf(
								wp_kses(
									/* translators: %1$d is replaced with order's id */
									/* translators: %2$s is replaced with the error message */
									__( 'Can not place the order number %1$d. The API replied with the following response: %2$s', 'dropshipexpress' ),
									[
										'a' => [
											'href'   => [],
											'target' => [],
										],
									]
								),
								$order_id,
								$api_response->get_error_message()
							)
						);
						$api_errors[ $dse_product[ 'id' ] ] = [
							'message'    => $api_response->get_error_message(),
							'url'        => $dse_product[ 'product_url' ],
							'remote_url' => $dse_product[ 'remote_url' ],
							'title'      => get_the_title( $dse_product[ 'id' ] ),
						];
					}

				} else {
					// Log the error
					DSE_Core::Log_Error(
						sprintf(
							/* translators: %1$s is replaced with retailer's name */
							esc_html__( 'Can not load the order API to place the order. The api "%1$s" is not supported by this plugin.', 'dropshipexpress' ),
							ucwords( $dse_product[ 'api_name' ] )
						)
					);
					continue;
				}

			}

			// Add metadata to the order regarding the process
			if ( isset( $purchased_products ) ) {
				foreach ( $purchased_products as $id => $purchased_product ) {
					$order->add_meta_data( '_dse_api_order', $purchased_product[ 'id' ], FALSE );
					$order->add_meta_data( "_dse_api_order_hash_{$purchased_product['id']}", $id, TRUE );
					$order->add_meta_data( "_dse_api_order_status_{$purchased_product['id']}", 'purchased', TRUE );
					$order->add_meta_data( "_dse_api_order_tracking_{$purchased_product['id']}", $purchased_product[ 'request_id' ], TRUE );
				}

				// Update the status of the order
				$order->add_meta_data( 'dse_order_processing', 'yes', TRUE );

				// Save the order
				$order->save();
			}

			// Send an email to the admin
			if ( 'yes' === DSE_Settings::Get_Setting( 'general', 'purchase_email' ) ) {
				self::Send_Email( $order_id, $purchased_products, $api_errors, $extra_products );
			}

			return TRUE;
		}

		/**
		 * Method to process and update an order
		 * that has been processed before
		 *
		 * @param int $order_id
		 *
		 * @return \WP_Error|bool
		 */
		public static function Update_Order( int $order_id ) {

			return TRUE;
		}

		private static function Check_Credentials( $source ) {
			return TRUE;
		}

		/**
		 * Static method to load an api
		 *
		 * @param $api_name
		 *
		 * @return bool|\DSE_Api_zinc
		 */
		public static function Load_API( $api_name ) {

			$class_name = "DSE_Api_{$api_name}";

			if ( self::$api_name !== $api_name || ! ( self::$api_instance instanceof $class_name ) ) {

				// Load the API file
				$file = DSE_PLUGIN_FOLDER . '/includes/apis/shipment/' . $api_name . '.php';

				if ( file_exists( $file ) ) {
					require_once( $file );
				} else {
					return FALSE;
				}

				// Set the static properties
				self::$api_name     = $api_name;
				self::$api_instance = new $class_name;

			}

			return self::$api_instance;
		}

		/**
		 * Method to send an email to the admin whenever a purchase
		 * is done using the ordering api
		 *
		 * @param int   $order_id
		 * @param array $purchased_products
		 * @param array $api_errors
		 * @param array $extra_products
		 */
		private static function Send_Email( int $order_id, array $purchased_products = [], array $api_errors = [], array $extra_products = [] ) {

			// Admin's email
			$admin_email = get_option( 'admin_email' );
			$site_name   = get_bloginfo( 'name' );
			$subject     = esc_html__( 'DropshipExpress New Order Fulfillment', 'dropshipexpress' );
			$headers     = [
				'Content-Type: text/html; charset=UTF-8',
				"From: {$site_name} <{$admin_email}>",
			];

			$content_header = esc_html__( 'An order has been processed by the DropshipExpress plugin', 'dropshipexpress' );
			$content_intro  = esc_html__( 'An order has been placed on your website and automatically processed by the DropshipExpress because you have enabled automatic shipment.', 'dropshipexpress' );
			$content_text   = esc_html__( 'Below is the list of products that have been purchased, alongside their link to the retailer store and your store.', 'dropshipexpress' );
			$content_error  = esc_html__( 'The API was unable to purchase the below products. You need to manually purchase them and process the order.', 'dropshipexpress' );
			$content_outro  = esc_html__( 'This email was sent to you because you have enabled the option to receive notification when a purchase is processed via DropshipExpress plugin.', 'dropshipexpress' );

			$email = "<html lang='en_US'>";
			$email .= "<body>";
			$email .= "<h2>{$content_header}</h2>";
			$email .= "<p>{$content_intro}</p>";
			$email .= "<p>{$content_text}</p>";

			// Successful purchases
			if ( $purchased_products ) {
				$email .= '<table rules="all" style="border-color: #666;" cellpadding="10">';

				$email .= '<tr style="background: #eee;">';
				$email .= '<td><strong>' . esc_html__( 'Item Name', 'dropshipexpress' ) . '</strong></td>';
				$email .= '<td><strong>' . esc_html__( 'Product\'s Link', 'dropshipexpress' ) . '</strong></td>';
				$email .= '<td><strong>' . esc_html__( 'Retailer\'s Link', 'dropshipexpress' ) . '</strong></td>';
				$email .= '</tr>';

				foreach ( $purchased_products as $purchased_product ) {
					$email .= '<tr>';
					$email .= "<td>{$purchased_product['title']}</td>";
					$email .= "<td><a target='_blank' href='{$purchased_product['url']}'>" . esc_html__( 'View', 'dropshipexpress' ) . "</a></td>";
					$email .= "<td><a target='_blank' href='{$purchased_product['remote_url']}'>" . esc_html__( 'View', 'dropshipexpress' ) . "</a></td>";
					$email .= '</tr>';
				}

				$email .= '</table>';
			}

			// Failed purchases
			if ( $api_errors ) {
				$email .= "<br>";
				$email .= "<p>{$content_error}</p>";

				$email .= '<table rules="all" style="border-color: #666;" cellpadding="10">';

				$email .= '<tr style="background: #eee;">';
				$email .= '<td><strong>' . esc_html__( 'Item Name', 'dropshipexpress' ) . '</strong></td>';
				$email .= '<td><strong>' . esc_html__( 'Product\'s Link', 'dropshipexpress' ) . '</strong></td>';
				$email .= '<td><strong>' . esc_html__( 'Retailer\'s Link', 'dropshipexpress' ) . '</strong></td>';
				$email .= '</tr>';

				foreach ( $api_errors as $api_error ) {
					$email .= '<tr>';
					$email .= "<td>{$api_error['title']}</td>";
					$email .= "<td><a target='_blank' href='{$api_error['url']}'>" . esc_html__( 'View', 'dropshipexpress' ) . "</a></td>";
					$email .= "<td><a target='_blank' href='{$api_error['remote_url']}'>" . esc_html__( 'View', 'dropshipexpress' ) . "</a></td>";
					$email .= '</tr>';

					$email .= '<tr>';
					$email .= "<td>{{$api_error['message']}}</td>";
					$email .= '</tr>';
				}

				$email .= '</table>';

			}

			// Extra products
			if ( $extra_products ) {
				$email .= "<br>";
				$email .= "<p>{$content_error}</p>";

				$email .= '<table rules="all" style="border-color: #666;" cellpadding="10">';

				$email .= '<tr style="background: #eee;">';
				$email .= '<td><strong>' . esc_html__( 'Item Name', 'dropshipexpress' ) . '</strong></td>';
				$email .= '<td><strong>' . esc_html__( 'Product\'s Link', 'dropshipexpress' ) . '</strong></td>';
				$email .= '</tr>';

				foreach ( $extra_products as $extra_product ) {
					$email .= '<tr>';
					$email .= "<td>{$extra_product['title']}</td>";
					$email .= "<td><a target='_blank' href='{$extra_product['url']}'>" . esc_html__( 'View', 'dropshipexpress' ) . "</a></td>";
					$email .= '</tr>';
				}

				$email .= '</table>';
			}

			$email .= "<p>{$content_outro}</p>";

			$email .= "</body>";
			$email .= "</html>";

			// Send the email
			$email_sent = wp_mail( $admin_email, $subject, $email, $headers );

			// Log the error
			if ( ! $email_sent ) {
				DSE_Core::Log_Error( esc_html__( 'Failed to notify admin via email about the new order. Please check your email settings.', 'dropshipexpress' ) );
			}
		}

		public static function Register_Failed_Rest_Route() {
			return TRUE;
		}

		public static function Register_Success_Rest_Route() {
			return TRUE;
		}

		public static function Register_Track_Rest_Route() {
			return TRUE;
		}

		/**
		 * Return an array containing a list of supported Apis
		 *
		 * @param array $apis
		 *
		 * @return array
		 */
		public static function Supported_Apis( $apis = [] ) {

			// An array of apis and their supported stores
			$supported_apis = [
				'zinc' => [
					'amazon',
					'amazon_uk',
					'amazon_ca',
					'amazon_de',
					'amazon_mx',
					'costco',
					'walmart',
					'homedepot',
					'lowes',
					'aliexpress',
				],
			];

			return array_merge( $apis, $supported_apis );
		}

		/**
		 * Method to check if all the required payment
		 * fields are filled
		 *
		 * @param $source
		 *
		 * @return bool
		 */
		public static function Validate_Payment_Method( $source ) {
			$required_fields = [
				'login_username',
				'login_pass',
				'card_name',
				'card_number',
				'card_expiry_year',
				'card_expiry_month',
				'card_cvv',
			];

			foreach ( $required_fields as $field ) {
				if ( '' === DSE_Settings::Get_Setting( $source, $field ) ) {
					return FALSE;
				}
			}

			return TRUE;

		}

		/**
		 * Callback function to add a custom meta box
		 * to wc's order page
		 *
		 * @param $post \WP_Post
		 */
		public static function WC_Add_Order_Data( $post ) {

			// Get a list of orders that have been processed by this plugin
			$dse_item_ids = get_post_meta( $post->ID, '_dse_api_order', FALSE );

			if ( $dse_item_ids ) {

				echo "<p>" . esc_html__( 'Below is a list of products that have been processed by DropshipExpress\'s ordering API.', 'dropshipexpress' ) . "</p>";

				echo "<div class='dse-order-details-wrapper'>";
				echo "<table cellpadding='0' cellspacing='0'>";
				echo "<thead>";
				echo "<tr>";
				echo "<th class='dse-order-title' colspan='2'>" . esc_html__( 'Item', 'dropshipexpress' ) . "</th>";
				echo "<th class='dse-order-retailer'>" . esc_html__( 'Retailer', 'dropshipexpress' ) . "</th>";
				echo "<th class='dse-order-status'>" . esc_html__( 'Status', 'dropshipexpress' ) . "</th>";
				echo "</tr>";
				echo "</thead>";

				echo "<tbody>";

				foreach ( $dse_item_ids as $dse_item_id ) {

					// Check if the product has been removed
					if ( FALSE === $item_object = wc_get_product( $dse_item_id ) ) {
						echo "<tr class='dse-order-item'>";
						echo "<td>" . esc_html__( 'This item no longer exists on your store.', 'dropshipexpress' ) . "</td>";
						echo "</tr>";

						// Skip to next item
						continue;
					}

					// Get information regarding each item
					$item_status      = get_post_meta( $post->ID, "_dse_api_order_status_{$dse_item_id}", TRUE );
					$item_tracking_id = get_post_meta( $post->ID, "_dse_api_order_tracking_{$dse_item_id}", TRUE );

					// Display the informations
					echo "<tr class='dse-order-item'>";

					// Product image
					echo "<td class='dse-order-thumb'><div>" . $item_object->get_image() . "</div></td>";

					// Product Title
					echo "<td class='dse-order-title'>";

					echo "<a href='" . $item_object->get_permalink() . "' target='_blank'>" . $item_object->get_title() . "</a>";

					// Product SKU
					if ( '' !== $item_object->get_sku() ) {
						echo "<div class='dse-order-sku'><strong>" . esc_html__( 'SKU:', 'dropshipexpress' ) . "</strong> " . $item_object->get_sku() . "</div>";
					}

					// Tracking ID
					echo "<div class='dse-order-tracking-id'><strong>" . esc_html__( 'Tracking ID:', 'dropshipexpress' ) . "</strong> " . esc_html( $item_tracking_id ) . "</div>";

					echo "</td>";

					// Retailer
					echo "<td class='dse-order-retailer' width='1%'><div>";
					echo "<a href='" . esc_url( get_post_meta( $item_object->get_id(), 'dse_product_url', TRUE ) ) . "' target='_blank'>" . esc_html__( 'View on retailer', 'dropshipexpress' ) . "</a>";
					echo "</div></td>";

					// Order status
					echo "<td class='dse-order-status' width='1%'><div>" . esc_html( ucwords( $item_status ) ) . "</div></td>";

					echo "</tr>";
				}

				echo "</tbody>";

				echo "</table>";
				echo "</div>";

			} else {
				echo "<p>" . esc_html__( 'This order does not contain any item that has been processed by DropshipExpress\'s order API.', 'dropshipexpress' ) . "</p>";
			}

		}
	}
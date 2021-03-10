<?php
	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Import
	 *
	 * Parent class used to handle everything that
	 * is related to importing and publishing the
	 * products
	 *
	 */
	class DSE_Import {

		/**
		 * Holds an instance of the loaded api
		 **
		 *
		 * @var \DSE_Api_aliexpress $api_instance
		 */
		private static $api_instance;

		/**
		 * Holds an instance of the api name
		 *
		 * @var $api_name
		 */
		private static $api_name;


		/**
		 * Method to add a new import rule
		 *
		 */
		public static function Add_Import_Rule() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to perform this operation.', 'dropshipexpress' ) );
			}

			check_admin_referer( 'dse-save-import-rules-nonce-action', 'dse_import_rules_nonce' );

			// Check the api
			$api_list = array_keys( self::Get_Sections() );
			$api_name = $_POST[ 'dse_import_rule_api' ];

			if ( in_array( $api_name, $api_list ) ) {
				// Load the proper class to save it
				$api = self::Load_API( $api_name );

				if ( FALSE !== $api ) {
					$api->Add_Import_Rule();
				}
			}

			// Redirect the user to the setting page
			wp_safe_redirect( admin_url( 'admin.php?page=dse-import-rules' ), 303 );
			exit();
		}

		/**
		 * Method to return a list of available services
		 *
		 * @return array
		 */
		public static function Get_Sections() {
			$apis = [
				'aliexpress' => [
					'title' => esc_html__( 'AliExpress', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/aliexpress.svg',
				],
				'amazon'     => [
					'title' => esc_html__( 'Amazon', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/amazon.svg',
				],
				'walmart'    => [
					'title' => esc_html__( 'Walmart', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/walmart.svg',
				],
				'costco'     => [
					'title' => esc_html__( 'Costco', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/costco.svg',
				],
				'ebay'       => [
					'title' => esc_html__( 'Ebay', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/ebay.svg',
				],
				'gearbest'   => [
					'title' => esc_html__( 'GearBest', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/gearbest.svg',
				],
				'vip'        => [
					'title' => esc_html__( 'Vip.com', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/vip.svg',
				],
				'homedepot'  => [
					'title' => esc_html__( 'Homedepot', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/homedepot.svg',
				],
				'lowes'      => [
					'title' => esc_html__( 'Lowe\'s', 'dropshipexpress' ),
					'logo'  => DSE_PLUGIN_URL . 'assets/images/svg/api-logo/lowes.svg',
				],
			];

			return $apis;
		}

		/**
		 * Load an instance of the proper API
		 *
		 * @param $api
		 *
		 * @return boolean|\DSE_Api_aliexpress
		 */
		public static function Load_API( $api ) {

			$class_name = "DSE_Api_{$api}";

			// Check if the property is not already set
			if ( self::$api_name !== $api || ! ( self::$api_instance instanceof $class_name ) ) {

				// Load the API file
				$file = DSE_PLUGIN_FOLDER . '/includes/apis/stores/' . $api . '.php';

				if ( file_exists( $file ) ) {
					require_once( $file );
				} else {
					return FALSE;
				}

				// Support for premium API
				$class_name = apply_filters( 'dse_filter_class_name', $class_name, $api );

				// Set the static properties
				self::$api_name     = $api;
				self::$api_instance = new $class_name;

			}

			return self::$api_instance;

		}

		/**
		 * Method to publish a product that has been imported
		 * to the store via ajax
		 *
		 */
		public static function Ajax_Publish_Product() {

			// Check if the user is allowed to publish a product
			if ( ! is_user_logged_in() ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'Only logged in users are allowed to perform this action.', 'dropshipexpress' ) ], 400 );
			}

			// Get the product ID
			if ( ! isset( $_POST[ 'dse_single_import_id' ] ) || empty( $_POST[ 'dse_single_import_id' ] ) ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'Product ID seems to be missing. Please refresh the page and try again.', 'dropshipexpress' ) ], 400 );
			}

			if ( ! wp_verify_nonce( $_POST[ 'dse_single_publish_nonce_' . intval( $_POST[ 'dse_single_import_id' ] ) ], 'dse--publish-product-nonce-3312' ) ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'Can not verify your request. Please refresh the page and try again.', 'dropshipexpress' ) ], 400 );
			}

			// Get the permission set in the options
			$permission = DSE_Settings::Get_Setting( 'general', 'permission_publish_access' );

			$user = wp_get_current_user();

			// If user is admin or he is permitted
			if ( ! current_user_can( 'manage_options' ) && ! in_array( $permission, $user->roles ) ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'You do not have the required permissions to perform this task.', 'dropshipexpress' ) ], 400 );
			}

			// Get the product source and load the API
			$source = $_POST[ 'dse_single_import_source' ];
			$api    = self::Load_API( $source );

			if ( FALSE === $api ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'There was a problem loading the proper API. Please refresh the page and try again.', 'dropshipexpress' ) ], 400 );
			}

			// Check the action
			if ( 'delete' === $_POST[ 'dse_single_publish_submit' ] ) {
				// Also remove the product from the import list
				$delete_result = $api->Remove_Queued_Item( (int) $_POST[ 'dse_single_import_id' ] );

				if ( ! is_wp_error( $delete_result ) ) {
					wp_send_json( [ 'success' => TRUE, 'message' => esc_html__( 'Successfully Deleted the product.', 'dropshipexpress' ) ], 200 );
				} else {
					wp_send_json( [ 'success' => FALSE, 'message' => $delete_result->get_error_message() ], 200 );
				}

			}

			// Queue the single Item to be imported
			$queue_results = $api->Queue_Import();

			if ( ! is_wp_error( $queue_results ) ) {
				wp_send_json( [ 'success' => TRUE, 'message' => esc_html__( 'Successfully queued the product to be published.', 'dropshipexpress' ) ], 200 );
			} else {
				wp_send_json( [ 'success' => FALSE, 'message' => $queue_results->get_error_message() ], 200 );
			}
		}

		/**
		 * Method to clear the import log
		 *
		 */
		public static function Clear_Log() {

			/**
			 * @var $wp_filesystem \WP_Filesystem_Direct
			 */
			global $wp_filesystem;

			// Check if the user is allowed to clear the log
			if (
				! is_user_logged_in() ||
				! check_admin_referer( 'dse_clear_log_nonce_8907', 'dse--clear-log-nonce' ) ||
				! current_user_can( 'manage_options' )
			) {
				wp_die( esc_html__( 'You do not have permission to perform this task.', 'dropshipexpress' ) );
			}

			// Clear the log
			if ( WP_Filesystem() ) {
				$wp_filesystem->put_contents( DSE_LOG_FILE, '', 0755 );
			}

			// Redirect the user
			wp_safe_redirect( admin_url( 'admin.php?page=dse-logs' ) );
			exit();

		}

		/**
		 * Method to get a list of the product categories published on
		 * the website
		 *
		 * @param int $current_cat
		 *
		 * @return string
		 */
		public static function Get_Categories( $current_cat = 0 ) {
			// Get a list of product categories and select a default category
			$product_categories = get_terms(
				[
					'taxonomy'   => 'product_cat',
					'hide_empty' => FALSE,
					'orderby'    => 'name',
					'order'      => 'ASC',
				]
			);

			$html = '';

			if ( ! empty( $product_categories ) ) {
				foreach ( $product_categories as $product_category ) {
					if ( $product_category->parent === 0 ) {
						// If the parent category is selected
						$parent_selected = $current_cat === $product_category->term_id ? 'selected' : '';

						$html .= "<optgroup label='" . esc_attr( $product_category->name ) . "'>";
						// Output the parent category
						$html .= "<option {$parent_selected} value='" . esc_attr( $product_category->term_id ) . "'>" . sprintf( '%1$s %2$s', esc_html( $product_category->name ), esc_html__( '(All)', 'dropshipexpress' ) ) . "</option>";
						// Output the child categories
						foreach ( $product_categories as $child_category ) {
							if ( $child_category->parent === $product_category->term_id ) {
								// If the child category is selected
								$child_selected = $current_cat === $child_category && '' === $parent_selected ? 'selected' : '';

								$html .= "<option {$child_selected} value='" . esc_attr( $child_category->term_id ) . "'>" . esc_html( $child_category->name ) . "</option>";
							}
						}
						$html .= "</optgroup>";
					}
				}
			}

			return $html;
		}

		/**
		 * Method to get category index for a store
		 *
		 * @param $api
		 *
		 * @return array|mixed
		 */
		public static function Get_Category_Index( $api ) {

			$api = self::Load_API( $api );

			if ( FALSE === $api ) {
				return [];
			}

			return $api::Get_Category_Indexes();
		}

		/**
		 * Method to return a list of supported currencies for each
		 * store
		 *
		 * @param $api
		 *
		 * @return array|mixed
		 */
		public static function Get_Currency_Index( $api ) {

			$api = self::Load_API( $api );

			if ( FALSE === $api ) {
				return [];
			}

			return $api::Get_Currency_Indexes();
		}

		/**
		 * Method to return the proper currency symbol for an API
		 *
		 * @param $store
		 *
		 * @return string
		 */
		public static function Get_Currency_Symbol( $store ) {

			$api = self::Load_API( $store );

			if ( FALSE === $api ) {
				return 'N/A';
			}

			// Get a list of supported currencies for this api
			$currencies_supported = $api::Get_Currency_Indexes();

			// Get the selected currency and default woocommerce currency
			$currency    = DSE_Settings::Get_Setting( $store, 'currency' );
			$wc_currency = get_woocommerce_currency();

			// Check if it is supported
			if ( ! in_array( $currency, $currencies_supported ) ) {
				// Fallback to woocommerce's currency
				if ( in_array( $wc_currency, $currencies_supported ) ) {
					$currency = $wc_currency;
				} else {
					// Fallback to USD
					$currency = 'USD';
				}
			}

			// If the intl extension is not installed
			if ( ! extension_loaded( 'intl' ) ) {
				return $currency;
			}

			// Translate the currency symbol
			$locale = DSE_Settings::Get_Setting( $store, 'language' );
			$locale = 'auto' === $locale ? get_locale() : $locale;
			$locale = $api::Translate_Locale( $locale );

			$formatter = new NumberFormatter( "{$locale}@currency={$currency}", NumberFormatter::CURRENCY );

			return $formatter->getSymbol( NumberFormatter::CURRENCY_SYMBOL );

		}

		/**
		 * Output the pagination template for
		 * the imported products page
		 *
		 * @param $current
		 * @param $total
		 */
		public static function Get_Import_Pagination( $current, $total ) {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/imported-products/pagination.php' );
		}

		/**
		 * Method to output a total number of imported products
		 *
		 */
		public static function Get_Imported_Count() {
			$number = wp_count_posts( 'dse_imported' );
			return $number->draft;
		}

		/**
		 * Method to view a list of imported products
		 *
		 */
		public static function Get_Imported_List() {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/imported-products/wrapper-list.php' );
		}

		/**
		 * Output the pagination template for the
		 * search product page
		 *
		 * @param $current
		 * @param $total
		 */
		public static function Get_Search_Pagination( $current, $total ) {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/search-import/pagination.php' );
		}

		/**
		 * Method to import a single item
		 *
		 */
		public static function Import_Item() {
			// Check if the user is allowed to perform this
			if ( ! is_user_logged_in() ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'Only logged in users are allowed to perform this action.', 'dropshipexpress' ) ], 400 );
			}

			// Get the product ID
			if ( ! isset( $_POST[ 'dse_product_id' ] ) || empty( $_POST[ 'dse_product_id' ] ) ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'Product ID seems to be missing. Please refresh the page and try again.', 'dropshipexpress' ) ], 400 );
			}

			if ( ! wp_verify_nonce( $_POST[ 'dse_import_product_nonce_' . intval( $_POST[ 'dse_product_id' ] ) ], 'dse--import-single-item-action' ) ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'Can not verify your request. Please refresh the page and try again.', 'dropshipexpress' ) ], 400 );
			}

			// Get the permission set in the options
			$permission = DSE_Settings::Get_Setting( 'general', 'permission_import_access' );

			$user = wp_get_current_user();

			// If user is admin or he is permitted
			if ( ! current_user_can( 'manage_options' ) && ! in_array( $permission, $user->roles ) ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'You do not have the required permissions to perform this task.', 'dropshipexpress' ) ], 400 );
			}

			// Get the product source and load the API
			$source = $_POST[ 'dse_import_source' ];
			$api    = self::Load_API( $source );

			if ( FALSE === $api ) {
				wp_send_json( [ 'success' => FALSE, 'message' => esc_html__( 'There was a problem loading the proper API. Please refresh the page and try again.', 'dropshipexpress' ) ], 400 );
			}

			// Queue the single Item to be imported
			$import_result = $api->Import_Product( $_POST[ 'dse_product_id' ] );

			if ( ! is_wp_error( $import_result ) ) {

				// Send an email
				if ( 'yes' == DSE_Settings::Get_Setting( 'general', 'notification_import' ) ) {
					self::Send_Email(
						esc_html__( 'New product import', 'dropshipexpress' ),
						esc_html__( 'An item has been imported to your store', 'dropshipexpress' ),
						esc_html__( 'A product has been manually or automatically imported to your store. The newly imported product can be accessed from your dashboard.', 'dropshipexpress' ),
						esc_html__( 'This email has been sent to you by DropshipExpress plugin because you have enabled the option to receive these notifications.', 'dropshipexpress' )
					);
				}

				wp_send_json( [ 'success' => TRUE ], 200 );
			} else {
				// Log the error
				DSE_Core::Log_Error( $import_result->get_error_message() );
				wp_send_json( [ 'success' => FALSE, 'message' => $import_result->get_error_message() ], 200 );
			}
		}

		/**
		 * Method to send an admin notification
		 *
		 * @param       $title
		 * @param       $header
		 * @param       $content
		 * @param       $content_end
		 * @param array $extra_headers
		 */
		public static function Send_Email( $title, $header, $content, $content_end, $extra_headers = [] ) {

			if ( FALSE === ( $admin_email = is_email( DSE_Settings::Get_Setting( 'general', 'notification_mail_address' ) ) ) ) {
				$admin_email = get_option( 'admin_email' );
			}
			$site_name = get_bloginfo( 'name' );

			$headers = [
				'Content-Type: text/html; charset=UTF-8',
				"From: {$site_name} <{$admin_email}>",
			];

			$headers = array_merge( $headers, $extra_headers );

			$email = "<html lang='en_US'>";
			$email .= "<body>";
			$email .= "<h2>{$header}</h2>";
			$email .= "<p>{$content}</p>";
			$email .= "<p>{$content_end}</p>";
			$email .= "</body>";
			$email .= "</html>";

			// Send the email
			$email_sent = wp_mail( $admin_email, $title, $email, $headers );

			// Log the error
			if ( ! $email_sent ) {
				DSE_Core::Log_Error( esc_html__( 'Failed to notify admin via email about the new order. Please check your email settings.', 'dropshipexpress' ) );
			}

		}

		/**
		 * Callback function to output the import product
		 * page
		 *
		 */
		public static function Output_Import_Menu_CB() {
			// Check if the plugin is activated
			if ( ! DSE_Settings::Is_Activated() ) {
				wp_safe_redirect( admin_url( 'admin.php?page=dse-activation' ), 303 );
				exit();
			}

			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/settings-import-new.php' );
		}

		/**
		 * Method to convert an imported product to an instance
		 * of the DSE_Product class
		 *
		 * @param int    $product_id
		 * @param string $source
		 *
		 * @return \DSE_Product|\WP_Error
		 */
		public static function Post_to_Product( int $product_id, string $source = '' ) {

			// Check if the post exists
			if ( ! get_post( $product_id ) ) {
				return new WP_Error( 'dse_invalid_product_id', esc_html__( 'The supplied product ID is not valid.', 'dropshipexpress' ) );
			}

			if ( '' === $source & '' === $source = get_post_meta( $product_id, 'dse_source', 'true' ) ) {
				return new WP_Error( 'dse_invalid_store', esc_html__( 'The requested store is not enabled on this plugin.', 'dropshipexpress' ) );
			}

			// Load the API
			$api = self::Load_API( $source );

			// Convert the post to a product and return it
			if ( FALSE !== $api ) {
				/**
				 * @var $class_name \DSE_Api_aliexpress
				 */
				$class_name = "DSE_Api_{$source}";
				return $class_name::Post_To_Product( $product_id );
			} else {
				return new WP_Error( 'dse_invalid_source', esc_html__( 'The requested store does not exist on this website.', 'dropshipexpress' ) );
			}

		}


		/**
		 * Method to query a store based on
		 * user input
		 *
		 * @param $data
		 * @param $get
		 *
		 * @return array \DSE_Product|\WP_Error
		 */
		public static function Query_Store( $data, $get = [] ) {

			// Load the proper store
			$api = self::Load_API( $data[ 'source' ] );

			if ( FALSE === $api ) {
				return [ 'errors' => new WP_Error( 'dse_invalid_api', esc_html__( 'The requested API does not exist.', 'dropshipexpress' ) ) ];
			}

			// If it's a direct product request
			if ( TRUE === $data[ 'is_single' ] ) {
				return self::Query_Store_Single( $data, $get );
			}

			// Return an array of results
			return $api->Search( $data, $get );
		}

		/**
		 * Method to check whether the given item exists
		 * on the store at all.
		 *
		 * @param $id
		 * @param $store
		 *
		 * @return bool
		 */
		public static function Is_On_Shop( $id, $store ) {
			if ( self::Is_Imported( $id, $store ) || self::Is_Published( $id, $store ) ) {
				return TRUE;
			}
			return FALSE;
		}

		/**
		 * Query a single product from a store
		 *
		 * @param $data
		 * @param $get
		 *
		 * @return array DSE_Product
		 */
		public static function Query_Store_Single( $data, $get = [] ) {

			// Load the proper store
			$api = self::Load_API( $data[ 'source' ] );

			if ( FALSE === $api ) {
				return [ 'errors' => new WP_Error( 'dse_invalid_api', esc_html__( 'The requested API does not exist.', 'dropshipexpress' ) ) ];
			}

			// Return an array containing a single product
			$result = $api->Download_Product( $data[ 'single_id' ], $get );

			if ( is_wp_error( $result ) ) {
				return [ 'errors' => $result ];
			}

			return $result;
		}

		/**
		 * Method to check whether a product has been already
		 * added to the import list
		 *
		 * @param $id
		 * @param $store
		 *
		 * @return bool
		 */
		public static function Is_Imported( $id, $store ) {
			// Get the list of imported products
			$imported_list = get_option( 'dse_imported_list' );

			if ( isset( $imported_list[ $store ] ) && FALSE !== array_search( $id, $imported_list[ $store ] ) ) {
				return TRUE;
			}

			return FALSE;
		}

		/**
		 * Method to check if a product has been already
		 * published on the store
		 *
		 * @param $id
		 * @param $store
		 *
		 * @return bool
		 */
		public static function Is_Published( $id, $store ) {
			// Get the list of published products
			$published_list = get_option( 'dse_published_list' );

			if ( isset( $published_list[ $store ] ) && FALSE !== array_search( $id, $published_list[ $store ] ) ) {
				return TRUE;
			}

			return FALSE;
		}

		/**
		 * Method to process the cronjobs and schedule them
		 *
		 */
		public static function Process_Crons() {

			// Process the cron job for queued products
			if ( ! wp_next_scheduled( 'dse_process_queued_cron' ) ) {
				wp_schedule_event( time(), 'dse_queue_cron', 'dse_process_queued_cron' );
			}

			// Process the import rule crons
			$import_rules = get_option( 'dse_import_rules', '' );

			if ( $import_rules ) {
				foreach ( $import_rules as $key => $import_rule ) {
					// Check if the option is enabled by the user
					if ( 'yes' === DSE_Settings::Get_Setting( $import_rule[ 'api' ], 'auto_import' ) ) {
						if ( ! wp_next_scheduled( 'dse_process_autoimport_cron', [ 'dse_import_key' => $key ] ) ) {
							wp_schedule_event( time(), "dse_autoimport_{$key}", 'dse_process_autoimport_cron', [ 'dse_import_key' => $key ] );
						}
					}
				}
			}

			// Process the autopublish crons
			$apis = DSE_Import::Get_Sections();
			foreach ( $apis as $key => $api ) {
				if ( 'yes' === DSE_Settings::Get_Setting( $key, 'auto_publish' ) ) {
					if ( ! wp_next_scheduled( 'dse_process_autopublish_cron', [ 'dse_publish_key' => $key ] ) ) {
						wp_schedule_event( time(), "dse_autopublish_{$key}", 'dse_process_autopublish_cron', [ 'dse_publish_key' => $key ] );
					}
				}
			}
		}

		/**
		 * Method to automatically import products that
		 * are configured in a single import rule
		 *
		 * @param $import_rule_key
		 */
		public static function Process_Autoimport( $import_rule_key ) {

			// Get a list of import rules
			$import_rules = get_option( 'dse_import_rules', [] );


			// Double check that this option is enabled, it might be disabled after the task has been scheduled
			if (
				isset( $import_rules[ $import_rule_key ] ) &&
				'yes' === DSE_Settings::Get_Setting( $import_rules[ $import_rule_key ][ 'api' ], 'auto_import' )
			) {
				// Construct the query
				$page     = 1;
				$imported = FALSE;

				$data = [
					'source'    => $import_rules[ $import_rule_key ][ 'api' ],
					'keyword'   => $import_rules[ $import_rule_key ][ 'keyword_text' ],
					'page'      => $page,
					'is_single' => FALSE,
					'single_id' => '',
				];

				// Reconstruct the $_GET data
				$get = [];

				foreach ( $import_rules[ $import_rule_key ][ 'get_data' ] as $get_key => $get_data ) {
					$get[ $get_key ] = $get_data;
				}

				// Load the proper API
				$api = self::Load_API( $import_rules[ $import_rule_key ][ 'api' ] );

				if ( FALSE !== $api ) {

					// Keep importing and querying products until we import enough
					do {
						// Query the store
						$queried_products = self::Query_Store( $data, $get );

						if ( ! is_wp_error( $queried_products[ 'errors' ] ) ) {

							// Keep importing products of the same page
							foreach ( $queried_products[ 'products' ] as $count => $product ) {

								// Check if we already have imported enough products
								if ( $imported ) {
									break 2;
								}

								// If the item is not already published
								if ( ! self::Is_On_Shop( $product->product_id, $import_rules[ $import_rule_key ][ 'api' ] ) ) {

									// Import a single item
									$import_result = $api->Import_Product( $product->url );

									// If imported successfully, add to the counter
									if ( ! is_wp_error( $import_result ) ) {
										$imported = TRUE;
									} else {
										// Log the error
										DSE_Core::Log_Error(
											sprintf(
											/* translators: %1$s is replaced with product's id */
											/* translators: %2$s is replaced with retailer's name */
											/* translators: %3$s is replaced with error message */
												esc_html__( 'Can not automatically import product id %1$s from %2$s. %3$s', 'dropshipexpress' ),
												$product->product_id,
												$import_rules[ $import_rule_key ][ 'api' ],
												$import_result->get_error_message()
											)
										);
									}
								}
							}

							// If we've reached the end of the loop and still not enough items
							$data[ 'page' ] = $queried_products[ 'current_page' ] + 1;
						} else {
							// Log the error
							DSE_Core::Log_Error(
								sprintf(
								/* translators: %1$s is replaced with retailer's name */
								/* translators: %2$s is replaced with error message */
									esc_html__( 'Can not automatically import products from %1$s. %2$s', 'dropshipexpress' ),
									$import_rules[ $import_rule_key ][ 'api' ],
									$queried_products[ 'errors' ]->get_error_message()
								)
							);
							break;
						}
					} while ( FALSE === $imported );
				}

			}
		}

		/**
		 * Method to automatically publish the imported products
		 * belonging to a specific store
		 *
		 * @param $args
		 */
		public static function Process_Autopublish( $args ) {

			if ( ! isset( $args[ 'dse_publish_key' ] ) ) {
				return;
			}

			$api = $args[ 'dse_publish_key' ];

			// Check if this option is enabled. The user might have disabled this option after an schedule.
			if ( 'yes' === DSE_Settings::Get_Setting( $api, 'auto_publish' ) ) {

				$posts_per_page = (int) DSE_Settings::Get_Setting( $api, 'schedule_count' );

				$imported_products_query = [
					'post_type'      => 'dse_imported',
					'posts_per_page' => $posts_per_page,
					'post_status'    => 'draft',
					'tax_query'      => [
						[
							'taxonomy' => 'dse_source',
							'field'    => 'slug',
							'terms'    => $api,
						],
					],
				];

				$imported_products = new WP_Query( $imported_products_query );

				if ( $imported_products->have_posts() ) {

					// Load the API
					$api_instance = self::Load_API( $api );

					if ( FALSE !== $api_instance ) {
						while ( $imported_products->have_posts() ) {
							$imported_products->the_post();
							$api_instance->Publish_Product( get_the_ID() );
						}
					} else {
						// Log the error if the product data is corrupt
						DSE_Core::Log_Error( esc_html__( 'Failed to autopublish imported product. The product data is corrupt.', 'dropshipexpress' ) );

						// Delete the product
						wp_delete_post( get_the_ID(), TRUE );
					}
				}

			}
		}

		/**
		 * Method to publish the products that have been
		 * imported and queued by the user
		 *
		 */
		public static function Process_Queued_Products() {

			$queued_products_query = [
				'post_type'      => 'dse_imported',
				'post_status'    => 'private',
				'posts_per_page' => 1,
			];

			// Query a list of imported products
			$queued_products = new WP_Query( $queued_products_query );

			if ( $queued_products ) {
				while ( $queued_products->have_posts() ) {
					$queued_products->the_post();

					// Get the proper API and publish the product
					$api_name = get_post_meta( get_the_ID(), 'dse_source', TRUE );
					$api      = self::Load_API( $api_name );

					if ( FALSE !== $api ) {
						$product_id = $api->Publish_Product( get_the_ID() );

						// Send an admin notification
						if ( ! is_wp_error( $product_id ) && 'yes' == DSE_Settings::Get_Setting( 'general', 'notification_publish' ) ) {

							self::Send_Email(
								esc_html__( 'New product published', 'dropshipexpress' ),
								esc_html__( 'An item has been published on your store', 'dropshipexpress' ),
								sprintf(
									wp_kses(
									/* translators: %1$s is replaced with retailer's name */
										__( 'A product has been automatically published on your store by DropshipExpress plugin. The newly imported product can be accessed from your dashboard, or visited <a href="%1$s" target="_blank">here</a>.', 'dropshipexpress' ),
										[
											'a' => [
												'href'   => [],
												'target' => [],
											],
										]
									),
									get_permalink( $product_id )
								),
								esc_html__( 'This email has been sent to you by DropshipExpress plugin because you have enabled the option to receive these notifications.', 'dropshipexpress' )
							);
						}

					} else {
						// Log the error if the product data is corrupt
						DSE_Core::Log_Error( esc_html__( 'Failed to publish imported product. The product data is corrupt.', 'dropshipexpress' ) );

						// Delete the product
						wp_delete_post( get_the_ID() );
					}
				}
			}
		}

		/**
		 * Method to update a product whenever
		 * a user views the page
		 *
		 */
		public static function Product_Viewed() {
			// Only trigger on product pages that were published by this plugin
			$product_id = (int) $_GET[ 'product_id' ];

			if (
				is_singular( 'product' ) &&
				'yes' === get_post_meta( $product_id, 'dse_product', TRUE ) &&
				'yes' !== get_post_meta( $product_id, 'dse_disable_sync', TRUE )
			) {

				// Source store
				$source = get_post_meta( $product_id, 'dse_source', TRUE );

				// Check if the option has been enabled
				if ( 'yes' !== DSE_Settings::Get_Setting( $source, 'enable' ) ) {
					wp_send_json( [ 'status' => 400 ] );
				}

				// Do not update product if it's been recently updated, to avoid loops
				$modified_date = strtotime( get_post_modified_time( 'Y-m-d H:i:s', TRUE, $product_id ) );
				$time_now      = strtotime( '+1 day', strtotime( gmdate( 'Y-m-d H:i:s' ) ) );

				if ( $modified_date + 3600 > $time_now ) {
					wp_send_json( [ 'status' => 400 ] );
				}

				// Get the configurations
				$config_array = [
					'auto_sync_title'      => FALSE,
					'auto_sync_images'     => FALSE,
					'auto_sync_desc'       => FALSE,
					'auto_sync_price'      => FALSE,
					'auto_sync_stock'      => FALSE,
					'auto_sync_reviews'    => FALSE,
					'auto_sync_variations' => FALSE,
				];

				foreach ( $config_array as $key => $value ) {
					$config_array[ $key ] = 'yes' === DSE_Settings::Get_Setting( $source, $key );
				}

				// Update the product
				$updated = self::Update_Product( $product_id, $config_array );

				if ( $updated ) {
					wp_send_json( [ 'status' => 200 ] );
				}

			}

			wp_send_json( [ 'status' => 400 ] );
		}

		/**
		 * Method to update certain fields of a published product based
		 * on post ID
		 *
		 * @param int   $post_id
		 * @param array $fields
		 *
		 * @return \WP_Error|bool
		 */
		private static function Update_Product( int $post_id, array $fields = [] ) {
			// Nothing to update
			if ( empty( $fields ) ) {
				return FALSE;
			}

			// Parse the args
			$fields = wp_parse_args(
				$fields,
				[
					'auto_sync_title'      => FALSE,
					'auto_sync_images'     => FALSE,
					'auto_sync_desc'       => FALSE,
					'auto_sync_price'      => FALSE,
					'auto_sync_stock'      => FALSE,
					'auto_sync_reviews'    => FALSE,
					'auto_sync_variations' => FALSE,
				]
			);

			$product = get_post( $post_id );

			// Wrong product ID, or not a product created by this plugin
			if (
				! $product ||
				'yes' !== get_post_meta( $post_id, 'dse_product', TRUE ) ||
				'yes' === get_post_meta( $post_id, 'dse_disable_sync', TRUE )
			) {
				return FALSE;
			}

			// Load the proper API
			$api = self::Load_API( $source = get_post_meta( $post_id, 'dse_source', TRUE ) );

			// Can't load the API
			if ( FALSE === $api ) {
				return FALSE;
			}

			// Get the updated remote product
			$remote_product = $api->Fetch_Product( get_post_meta( $post_id, 'dse_product_url', TRUE ) );

			if ( is_wp_error( $remote_product ) ) {
				return FALSE;
			}

			// Start comparing the products
			$updated = FALSE;

			// Get the config
			$enabled_replace       = DSE_Settings::Get_Setting( $source, 'enable_replacements' );
			$replace_rules         = DSE_Settings::Get_Setting( $source, 'replace_rule' );
			$dup_image_config      = DSE_Settings::Get_Setting( $source, 'check_duplicate_images' );
			$desc_image_config     = DSE_Settings::Get_Setting( $source, 'import_desc_images' );
			$product_images_config = DSE_Settings::Get_Setting( $source, 'import_product_images' );
			$price_config          = DSE_Settings::Get_Setting( $source, 'price_type' );
			$stock_sync_config     = DSE_Settings::Get_Setting( $source, 'enable_stock_manager' );
			$stock_config          = DSE_Settings::Get_Setting( $source, 'stock_update' );

			// Update the title
			if ( $fields[ 'auto_sync_title' ] ) {
				if ( $remote_product[ 'title' ] !== get_post_meta( $post_id, 'dse_title', TRUE ) ) {

					// Perform search & replace
					if ( 'yes' === $enabled_replace && ! empty( $replace_rules ) ) {
						foreach ( $replace_rules as $rule ) {
							if ( 'yes' === $rule[ 'apply_title' ] ) {
								$remote_product[ 'title' ] = str_replace( $rule[ 'search' ], $rule[ 'value' ], $remote_product[ 'title' ] );
							}
						}
					}

					// Update the post
					wp_update_post( [ 'ID' => $post_id, 'post_title' => $remote_product[ 'title' ] ] );

					// Update the flag
					$updated = TRUE;
				}
			}

			// Update the description
			if ( $fields[ 'auto_sync_desc' ] ) {

				if ( 'drop' !== $desc_image_config ) {
					$desc_dom = new DOMDocument();
					$desc_dom->loadHTML( $fields[ 'auto_sync_desc' ] );
					$desc_images = $desc_dom->getElementsByTagName( 'img' );

					if ( $desc_images ) {
						if ( 'download' === $desc_image_config ) {
							foreach ( $desc_images as $desc_image ) {
								// Download the image
								$downloaded_desc_image = self::Download_Image( $desc_image->getAttribute( 'src' ), $dup_image_config );
								if ( $downloaded_desc_image ) {
									// Update the description
									str_replace(
										$desc_image->getAttribute( 'src' ),
										wp_get_attachment_image_url( $downloaded_desc_image ),
										$remote_product[ 'description' ]
									);
								}
							}
						} else {
							$remote_product[ 'description' ] = preg_replace( "/<img[^>]+\>/i", "", $remote_product[ 'description' ] );
						}
					}
				}

				// Compare the values
				if ( $remote_product[ 'description' ] !== get_post_field( 'post_content', $post_id, 'raw' ) ) {

					// Perform search & replace
					if ( 'yes' === $enabled_replace && ! empty( $replace_rules ) ) {
						foreach ( $replace_rules as $rule ) {
							$remote_product[ 'description' ] = str_replace( $rule[ 'search' ], $rule[ 'value' ], $remote_product[ 'description' ] );
						}
					}

					// Update the post
					wp_update_post( [ 'ID' => $post_id, 'post_content' => $remote_product[ 'description' ] ] );

					// Update the flag
					$updated = TRUE;
				}
			}

			// Get an instance of the product to work with its attributes
			$wc_product = wc_get_product( $post_id );

			// Update the images
			if ( $wc_product && $fields[ 'auto_sync_images' ] ) {
				// Clear the images if the product has no image
				if ( 'drop' === $product_images_config ) {
					$wc_product->set_gallery_image_ids( [] );
				} elseif ( ! empty( $remote_product[ 'images' ] ) ) {
					// Get the new images
					foreach ( $remote_product[ 'images' ] as $image ) {
						$downloaded_product_image = self::Download_Image( $image, $dup_image_config, $product_images_config );
						// Add to the list of successful
						if ( ! is_wp_error( $downloaded_product_image ) ) {
							$new_product_images[] = $downloaded_product_image;
						}
					}

					// We don't want to lose the old product images
					if ( ! empty( $new_product_images ) ) {
						$wc_product->set_gallery_image_ids( $new_product_images[] );
					}
				}
			}

			// Update the price
			if ( $fields[ 'auto_sync_price' ] ) {

				// Check if price has changed
				$old_price            = get_post_meta( $post_id, 'dse_price', TRUE );
				$old_discounted_value = get_post_meta( $post_id, 'dse_discounted_value', TRUE );

				if ( 'percent' === $price_config ) {
					// Get the value to increase
					$perc_price_conf = (int) DSE_Settings::Get_Setting( $source, 'price_percent_value' );

					if ( $old_price !== $remote_product[ 'price' ] ) {
						$wc_product->set_regular_price( $remote_product[ 'price' ] * ( 1 + $perc_price_conf / 100 ) );
						$wc_product->set_price( $remote_product[ 'price' ] * ( 1 + $perc_price_conf / 100 ) );
						$updated = TRUE;
					}
					// If it's on sale
					if ( $remote_product[ 'is_discounted' ] && $old_discounted_value !== $remote_product[ 'discounted_value' ] ) {
						$wc_product->set_sale_price( $remote_product[ 'discounted_value' ] * ( 1 + $perc_price_conf / 100 ) );
						$updated = TRUE;
					}

				} elseif ( 'flat' === $price_config ) {
					// Get the value to increase
					$flat_price_conf = (int) DSE_Settings::Get_Setting( $source, 'price_flat_value' );
					if ( $old_price !== $remote_product[ 'price' ] ) {
						$wc_product->set_regular_price( $remote_product[ 'price' ] + $flat_price_conf );
						$wc_product->set_price( $remote_product[ 'price' ] + $flat_price_conf );
						$updated = TRUE;
					}
					// If it's on sale
					if ( $remote_product[ 'is_discounted' ] && $old_discounted_value !== $remote_product[ 'discounted_value' ] ) {
						$wc_product->set_sale_price( $remote_product[ 'discounted_value' ] + $flat_price_conf );
						$updated = TRUE;
					}

				} else {

					if ( $old_price !== $remote_product[ 'price' ] ) {
						// Set the original price
						$wc_product->set_regular_price( $remote_product[ 'price' ] );
						$wc_product->set_price( $remote_product[ 'price' ] );
						$updated = TRUE;
					}
					// If it's on sale
					if ( $remote_product[ 'is_discounted' ] && $old_discounted_value !== $remote_product[ 'discounted_value' ] ) {
						$wc_product->set_sale_price( $remote_product[ 'discounted_value' ] );
						$updated = TRUE;
					}
				}

			}

			// Update the stock value
			if ( $fields[ 'auto_sync_stock' ] ) {
				if ( 'yes' === $stock_sync_config ) {

					if ( $remote_product[ 'quantity' ] !== $wc_product->get_stock_quantity() ) {
						$wc_product->set_stock_quantity( $remote_product[ 'quantity' ] );

						// Also update the stock status
						if ( $remote_product[ 'quantity' ] < 1 ) {
							switch ( $stock_config ) {
								case 'draft':
									{
										wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );
										break;
									}
								case 'trash':
									{
										wp_delete_post( $post_id );
										return TRUE;
									}
								case 'outofstock':
									{
										$wc_product->set_stock_status( 'outofstock' );
										break;
									}
							}
						}

						$updated = TRUE;
					}
				}
			}

			// Update the reviews
			if ( $fields[ 'auto_sync_reviews' ] ) {

				// Get a list of reviews added by this plugin
				$review_ids = get_post_meta( $post_id, 'dse_review_ids', TRUE );

				if ( is_array( $review_ids ) ) {
					foreach ( $review_ids as $id ) {
						// Get the review
						if ( $comment = get_comment( $id ) ) {

							// Check if this review has been added before
							$comment_search = array_search( $comment->comment_author, array_column( $remote_product[ 'reviews' ], 'username' ) );

							// Remove the duplicate comments
							if ( FALSE !== $comment_search ) {
								unset( $remote_product[ 'reviews' ][ $comment_search ] );
							}
						}
					}
				}

				// Import the remainder of the reviews
				foreach ( $remote_product[ 'reviews' ] as $review ) {
					DSE_Import::Import_Review( $review, $post_id );

					// Update the flag
					$updated = TRUE;
				}
			}

			// Update the variations
			if ( $fields[ 'auto_sync_variations' ] ) {

				if ( $wc_product->is_type( 'variable' ) ) {

					$synced_variations = $remote_product[ 'variations' ];
					$synced_attributes = $remote_product[ 'variations' ];

					//$data_store = new WC_Product_Data_Store_CPT();

					$wc_variations = $wc_product->get_available_variations( 'object' );

					foreach ( $wc_variations as $wc_variation ) {

						/**
						 * @var $wc_variation \WC_Product_Variation
						 */

						// The variation exists. Update if necessary
						if ( isset( $synced_variations[ $wc_variation->get_sku() ] ) ) {

							// Check the price
							$new_price = self::Calculate_Price( $synced_variations[ $wc_variation->get_sku() ][ 'price' ], $source );

							if ( $new_price !== $wc_variation->get_price() ) {
								// $wc_variation->set_price( $new_price );
								$wc_variation->set_regular_price( $new_price );
								$updated = TRUE;
							}

							// Check if the product's sale status has changed
							if ( $wc_variation->is_on_sale() xor $synced_variations[ $wc_variation->get_sku() ][ 'is_discount' ] ) {
								// The product has gone on sale
								if ( $synced_variations[ $wc_variation->get_sku() ][ 'is_discount' ] ) {
									$wc_variation->set_sale_price( $synced_variations[ $wc_variation->get_sku() ][ 'discounted_value' ] );
								} else {
									// The product is no longer on sale
									$wc_variation->set_sale_price( NULL );
								}
								$updated = TRUE;
							} else {
								// Status hasn't changed
								if ( $synced_variations[ $wc_variation->get_sku() ][ 'discounted_value' ] !== $wc_variation->get_sale_price() ) {
									$wc_variation->set_sale_price( $synced_variations[ $wc_variation->get_sku() ][ 'discounted_value' ] );
									$updated = TRUE;
								}
							}

							// Remove this variation from the list
							unset( $synced_variations[ $wc_variation->get_sku() ] );
						} else {
							// This variation has been removed
							$wc_variation->delete( TRUE );
							$updated = TRUE;
						}
					}

					// Add the missing variations
					foreach ( $synced_variations as $synced_variation_key => $synced_variation ) {
						try {
							$variation_attributes = [];

							$wc_variation = new WC_Product_Variation();

							// $wc_variation->set_price( self::Calculate_Price( $synced_variation[ 'price' ], $source ) );
							$wc_variation->set_regular_price( self::Calculate_Price( $synced_variation[ 'price' ], $source ) );
							$wc_variation->set_parent_id( $post_id );
							$wc_variation->set_manage_stock( TRUE );
							$wc_variation->set_stock_quantity( $synced_variation[ 'quantity' ] );
							$wc_variation->set_stock_status( $synced_variation[ 'quantity' ] > 0 ? 'instock' : 'outofstock' );

							if ( ! empty( $synced_variation[ 'sku' ] ) ) {
								$wc_variation->set_sku( $synced_variation[ 'sku' ] );
							}

							// If on discount
							if ( $synced_variation[ 'is_discount' ] ) {
								$wc_variation->set_sale_price( self::Calculate_Price( $synced_variation[ 'discount_value' ], $source ) );
							}

							// Set the combination
							foreach ( $synced_variation[ 'combination' ] as $combination_key => $combination ) {
								$variation_attributes[ $synced_attributes[ $combination_key ][ 'name' ] ] = $synced_attributes[ $combination_key ][ 'values' ][ $combination ][ 'name' ];
							}

							$wc_variation->set_attributes( $variation_attributes );

							// Try to find a thumbnail
							foreach ( $synced_variation[ 'combination' ] as $key => $value ) {
								if ( isset( $attributes[ $key ][ 'values' ][ $value ][ 'image' ] ) ) {
									if ( 'drop' !== $product_images_config ) {
										// Strip the unnecessary characters from the thumbnail
										$variation_image = preg_replace( '/(?<=\.jpg|png|jpeg|webp)_[0-9]+x[0-9]+\.jpg|png|jpeg|webp$/', '', $synced_attributes[ $key ][ 'values' ][ $value ][ 'image' ] );
										$variation_image = self::Download_Image( $variation_image, $dup_image_config, $product_images_config );
										// Set the variation image
										if ( ! is_wp_error( $variation_image ) ) {
											$wc_variation->set_image_id( $variation_image );
										}
									}
									break;
								}
							}

							// Save the variation
							$new_variation_id = $wc_variation->save();

							if ( $new_variation_id ) {
								$updated = TRUE;
							}

						} catch ( Exception $error ) {
							// If something goes wrong, log it
							DSE_Core::Log_Error( $error->getMessage() );
						}
					}
				}
			}

			// Save the changes
			if ( $updated ) {
				$wc_product->save();

				// Send a notification to admin
				if ( 'yes' == DSE_Settings::Get_Setting( 'general', 'notification_update' ) ) {

					self::Send_Email(
						esc_html__( 'Product Updated', 'dropshipexpress' ),
						esc_html__( 'An item on your store has been updated', 'dropshipexpress' ),
						sprintf(
							wp_kses(
							/* translators: %1$s is replaced with product's URL */
								__( 'A product has been automatically updated on your store by DropshipExpress plugin. The updated product can be visited <a href="%1$s" target="_blank">here</a>.', 'dropshipexpress' ),
								[
									'a' => [
										'href'   => [],
										'target' => [],
									],
								]
							),
							$wc_product->get_permalink()
						),
						esc_html__( 'This email has been sent to you by DropshipExpress plugin because you have enabled the option to receive these notifications.', 'dropshipexpress' )
					);
				}
			}

			return $updated;
		}

		/**
		 * Method to download a remote image and return the attachment ID
		 *
		 * @param        $url
		 * @param string $check_duplicate
		 * @param string $type
		 * @param int    $post_id
		 *
		 * @return int|\WP_Error
		 */
		public static function Download_Image( $url, $check_duplicate = 'yes', $type = 'download', int $post_id = 0 ) {

			// Require the dependencies
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// Add the missing scheme
			if ( FALSE === strpos( $url, 'http' ) ) {
				$url = 'https:' . $url;
			}

			// Check if the image already exists
			if ( 'yes' === $check_duplicate ) {
				if ( $duplicate = self::Find_Image_By_Url( $url ) ) {
					return $duplicate;
				}
			}

			// If there's no downloaded needed
			if ( 'external' === $type ) {
				$attachment_type = wp_check_filetype( self::Path_To_Basename( $url ) );
				$attachment_data = [
					'guid'           => $url,
					'post_mime_type' => $attachment_type[ 'type' ],
					'post_title'     => sanitize_file_name( self::Path_To_Basename( $url ) ),
					'post_content'   => '',
					'post_status'    => 'inherit',
				];

				// Insert a new attachment
				$attachment_id = wp_insert_attachment( $attachment_data, esc_url( $url ), $post_id, TRUE );

				if ( is_wp_error( $attachment_id ) ) {
					return $attachment_id;
				} else {
					// Save the original URL in metadata
					add_post_meta( $attachment_id, 'dse_attachment_source', $url, TRUE );
					add_post_meta( $attachment_id, 'dse_image_is_external', 'yes', TRUE );
					return $attachment_id;
				}

			} else {
				$attachment_id = media_sideload_image( $url, $post_id, NULL, 'id' );
				if ( ! is_wp_error( $attachment_id ) ) {

					// Update the GUID
					global $wpdb;
					$wpdb->update( $wpdb->posts, [ 'guid' => $url ], [ 'ID' => $attachment_id, ], [ '%s' ], [ '%d' ] );

					// Add the source URL to the metadata
					add_post_meta( $attachment_id, 'dse_attachment_source', $url, TRUE );
				}
				return $attachment_id;
			}
		}

		/**
		 * Method to import a single review
		 *
		 * @param $review
		 * @param $product_id
		 *
		 * @return false|int
		 */
		public static function Import_Review( $review, $product_id ) {

			$review_id = wp_insert_comment(
				[
					'comment_post_ID'  => $product_id,
					'comment_author'   => $review[ 'username' ],
					'comment_content'  => $review[ 'content' ],
					'comment_type'     => 'review',
					'comment_date'     => date( 'Y-m-d H:i:s', strtotime( $review[ 'date' ] ) ),
					'comment_approved' => 1,
					'comment_meta'     => [
						'dse_comment' => 'yes',
					],
				]
			);
			update_comment_meta( $review_id, 'rating', $review[ 'rating' ] );
			update_comment_meta( $review_id, 'verified', 1 );

			return $review_id ? $review_id : FALSE;
		}

		/**
		 * Method to calculate the product price
		 * based on the configuration
		 *
		 * @param float  $price
		 * @param string $source
		 *
		 * @return float|int
		 */
		public static function Calculate_Price( float $price, string $source ) {

			if ( ! array_key_exists( $source, self::Get_Sections() ) ) {
				return $price;
			}

			// Get the price config
			$price_setting = DSE_Settings::Get_Setting( $source, 'price_type' );

			// If the price needs to be calculated
			if ( 'original' !== $price_setting ) {

				// For flat values
				if ( 'flat' === $price_setting ) {

					// Get the value
					$price_setting_value = (float) DSE_Settings::Get_Setting( $source, 'price_flat_value' );

					return $price + absint( $price_setting_value );

				} elseif ( 'percent' === $price_setting ) {

					// For percentage based prices
					$price_setting_value = DSE_Settings::Get_Setting( $source, 'price_percent_value' );

					return round( $price * abs( 1 + $price_setting_value / 100 ), 2 );
				}
			}

			return $price;
		}

		/**
		 * Method to find an attachment ID based on its basename
		 *
		 * @param $url
		 *
		 * @return false|int
		 */
		public static function Find_Image_By_Url( $url ) {

			global $wpdb;

			$image_sql = $wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE guid = '%s'",
				$url
			);

			$image_sql_results = $wpdb->get_results( $image_sql );

			if ( $image_sql_results ) {

				$post_id = reset( $image_sql_results )->ID;

				if ( count( $image_sql_results ) > 1 ) {
					foreach ( $image_sql_results as $result ) {
						$post_id = $result->ID;
					}
				}

				return $post_id;
			}

			return FALSE;
		}

		/**
		 * Method to convert an array or string of path type to
		 * an array or string of basename
		 *
		 * @param $path
		 *
		 * @return array|string
		 */
		public static function Path_To_Basename( $path ) {
			if ( is_array( $path ) ) {
				$basenames = [];
				foreach ( $path as $item ) {
					$basenames[] = basename( parse_url( $item, PHP_URL_PATH ) );
				}
			} else {
				$basenames = basename( parse_url( $path, PHP_URL_PATH ) );
			}
			return $basenames;
		}

		/**
		 * Method to remove the traces of an imported product
		 *
		 * @param $product_id
		 * @param $post \WP_Post
		 *
		 * @return bool
		 */
		public static function Remove_After_Delete( $product_id, $post ) {
			// Try to get the product's source
			$product_source = get_post_meta( $product_id, 'dse_source', TRUE );
			$apis           = DSE_Import::Get_Sections();

			// If it's a product added by this plugin, proceed
			if ( array_key_exists( $product_source, $apis ) && 'product' === $post->post_type ) {

				$source_product_id = get_post_meta( $product_id, 'dse_product_id', TRUE );

				$imported  = get_option( 'dse_imported_list' );
				$published = get_option( 'dse_published_list' );

				$imported[ $product_source ]  = array_diff( $imported[ $product_source ], [ $source_product_id ] );
				$published[ $product_source ] = array_diff( $published[ $product_source ], [ $source_product_id ] );

				update_option( 'dse_imported_list', $imported );
				update_option( 'dse_published_list', $published );

				return TRUE;
			}
			return FALSE;
		}

		/**
		 * Callback function to render the import rules
		 * section
		 *
		 */
		public static function Render_Import_Rules() {

			// Check if the plugin is activated
			if ( ! DSE_Settings::Is_Activated() ) {
				wp_safe_redirect( admin_url( 'admin.php?page=dse-activation' ), 303 );
				exit();
			}

			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/settings-import-rules.php' );
		}

		/**
		 * Method to render the logs page
		 *
		 */
		public static function Render_Logs() {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/settings-logs.php' );
		}

		/**
		 * Method to search a store and return the required
		 * results
		 *
		 */
		public static function Search_Stores() {

			// Check if the user is allowed to perform this
			if ( ! is_user_logged_in() ) {
				wp_die( esc_html__( 'You are not allowed to perform this action.', 'dropshipexpress' ) );
			}

			check_admin_referer( 'dse--search-import-nonce-action', 'dse_search_stores_nonce' );

			// Get the permission set in the options
			$permission = DSE_Settings::Get_Setting( 'general', 'permission_import_access' );

			$user = wp_get_current_user();

			// If user is admin or he is permitted
			if ( ! current_user_can( 'manage_options' ) && ! in_array( $permission, $user->roles ) ) {
				wp_die( esc_html__( 'You do not have the required permissions to perform this task.', 'dropshipexpress' ) );
			}

			// Get the form data. These are not sensitive.
			$keyword   = sanitize_text_field( $_GET[ 'dse_product_search_keyword' ] );
			$single_id = sanitize_text_field( $_GET[ 'dse_product_search_single_id' ] );

			$page = (int) $_GET[ 'dse_product_search_page' ];

			$is_single = $single_id ? TRUE : FALSE;

			// If the entire request is empty
			if ( empty( $keyword ) && FALSE === $is_single ) {
				self::Query_is_Empty();
				exit();
			}

			// Set the source
			if (
				isset( $_GET[ 'dse_product_search_source' ] ) &&
				in_array( $_GET[ 'dse_product_search_source' ], array_keys( self::Get_Sections() ) )
			) {
				$source = sanitize_text_field( $_GET[ 'dse_product_search_source' ] );
			} else {
				self::Output_Error_Template( new WP_Error( 'dse_invalid_store', esc_html__( 'Requested API is invalid.', 'dropshipexpress' ) ) );
				exit();
			}

			$data = [
				'source'    => $source,
				'keyword'   => $keyword,
				'page'      => $page,
				'is_single' => $is_single,
				'single_id' => $single_id,
			];

			// Query the results
			$results = self::Query_Store( $data, $_GET );

			// Output the error template if there's any error
			if ( is_wp_error( $results[ 'errors' ] ) ) {
				self::Output_Error_Template( $results[ 'errors' ] );
				exit();
			}

			// Output the results
			if ( TRUE === $results[ 'success' ] ) {
				self::Output_HTML_Results( $results, $data );
			} else {
				self::No_Results_Template( $data );
			}

			exit();

		}

		/**
		 * Method to alert the user that their query has no
		 * content
		 *
		 */
		public static function Query_is_Empty() {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/search-import/query-is-empty.php' );
		}

		/**
		 * Method to output an error template in case of
		 * any errors
		 *
		 * @param $wp_error
		 */
		public static function Output_Error_Template( $wp_error ) {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/search-import/error-results.php' );
		}

		/**
		 * Method to output an HTML template for results
		 *
		 * @param $results_array
		 * @param $search_input
		 */
		public static function Output_HTML_Results( $results_array, $search_input ) {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/search-import/search-results.php' );
		}

		/**
		 * Method to output an HTML template
		 * when the search has no result
		 *
		 * @param $search_input
		 */
		public static function No_Results_Template( $search_input ) {
			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/search-import/no-search-results.php' );
		}

		/**
		 * Callback function to output a list of imported
		 * products
		 *
		 */
		public static function View_Imported_Products_CB() {
			// Check if the plugin is activated
			if ( ! DSE_Settings::Is_Activated() ) {
				wp_safe_redirect( admin_url( 'admin.php?page=dse-activation' ), 303 );
				exit();
			}

			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/imported-products/wrapper-page.php' );

		}

		/**
		 * Save the metaboxes added by WC
		 *
		 * @param $post_id
		 */
		public static function WC_Save_Metabox_Data( $post_id ) {

			// Only products that are handled by this plugin
			if ( 'yes' !== get_post_meta( $post_id, 'dse_product', TRUE ) ) {
				return;
			}

			// Save the new product ID
			if ( isset( $_POST[ 'dse_wc_product_id' ] ) && ! empty( $_POST[ 'dse_product_id' ] ) ) {
				update_post_meta( $post_id, 'dse_product_id', sanitize_text_field( $_POST[ 'dse_wc_product_id' ] ) );
			}

			// Save the product URL
			if ( isset( $_POST[ 'dse_wc_product_url' ] ) && ! empty( $_POST[ 'dse_wc_product_url' ] ) ) {
				update_post_meta( $post_id, 'dse_product_url', esc_url_raw( $_POST[ 'dse_wc_product_url' ] ) );
			}

			// Save the sync option
			if ( isset( $_POST[ 'dse_wc_product_sync' ] ) && 'yes' === $_POST[ 'dse_wc_product_sync' ] ) {
				update_post_meta( $post_id, 'dse_disable_sync', 'yes' );
			} else {
				delete_post_meta( $post_id, 'dse_disable_sync', 'no' );
			}

		}

	}
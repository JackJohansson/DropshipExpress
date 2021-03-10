<?php

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Settings
	 *
	 * Class to add and manage the settings page
	 *
	 */
	class DSE_Settings {

		/**
		 * Private variable to hold an instance of the
		 * class
		 *
		 * @var
		 */
		private static $instance;

		/**
		 * Static method to hold whether the plugin
		 * is premium version or not
		 *
		 * @var $is_premium
		 */
		private static $is_premium;

		/**
		 * Temporary array to hold the settings that
		 * are being processed for saving
		 *
		 * @var array
		 */
		private $setting_to_save;

		/**
		 * Private variable to hold the settings
		 *
		 * @var array
		 */
		private static $settings;

		/**
		 * DSE_Settings constructor.
		 *
		 */
		public function __construct() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = $this;
			}

			// Hook for saving the options
			add_action( 'admin_post_dse_save_settings', [ $this, 'Save_Options' ] );

			// Load the settings
			$this->Load_Settings();

		}

		/**
		 * Method to load the plugins setting statically
		 *
		 */
		private function Load_Settings() {

			// Current list of settings
			$settings = [ 'general', 'aliexpress' ];

			foreach ( $settings as $setting ) {
				self::$settings[ $setting ] = get_option( 'dse_' . $setting, [] );
			}

			// Premium settings
			if ( self::Is_Premium() ) {
				self::$is_premium = TRUE;
			} else {
				self::$is_premium = FALSE;
			}

		}

		/**
		 * Method to check whether the plugin is the
		 * premium version
		 *
		 * @return bool
		 */
		public static function Is_Premium() {
			if (
				in_array( 'dropship-express-pro/dropship-express-pro.php', get_option( 'active_plugins' ) ) &&
				'yes' === get_option( 'dse_is_pro' )
			) {
				return TRUE;
			}

			return FALSE;
		}

		/**
		 * Callback function to output the activation
		 * page
		 *
		 */
		public static function Activation_Page_CB() {

			$activated     = self::Is_Activated();
			$purchase_code = get_option( 'dse_purchase_code', '' );
			$token         = get_option( 'dse_api_token', '' );

			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/settings-activation.php' );

		}

		/**
		 * Check whether the plugin is activated or not
		 *
		 * @return bool
		 */
		public static function Is_Activated() {

			$is_activated = get_option( 'dse_is_activated', 'no' );

			if ( 'yes' === $is_activated ) {
				return TRUE;
			}

			return FALSE;
		}

		/**
		 * Method to output the general options page
		 *
		 */
		public static function General_Options_CB() {

			// Check if the plugin is activated
			if ( ! self::Is_Activated() ) {
				wp_safe_redirect( admin_url( 'admin.php?page=dse-activation' ), 303 );
				exit();
			}

			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/settings-apis.php' );

		}

		/**
		 * Method to load the text domain
		 *
		 */
		public static function Load_Text_Domain() {
			load_plugin_textdomain(
				'dropshipexpress',
				FALSE,
				DSE_PLUGIN_DIRNAME . '/languages/'
			);
		}

		/**
		 * Method to output a badge for premium
		 * features
		 *
		 */
		public static function Pro_Badge() {
			if ( TRUE !== self::$is_premium ) {
				echo '<span class="dse-badge adge-badge-pro dse-badge-brand dse-inline-badge dse-badge-pill dse-badge-rounded dse-popover" data-content="' . esc_html__( 'Only available in pro version', 'dropshipexpress' ) . '">' . esc_html__( 'Pro!', 'dropshipexpress' ) . '</span>';
			}
		}

		/**
		 * Callback method to save the settings
		 *
		 */
		public function Save_Options() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to perform this operation.', 'dropshipexpress' ) );
			}

			check_admin_referer( 'dse-save-settings-nonce-action', 'dse_settings_nonce' );

			$settings = self::Get_Settings();

			/******************************************************
			 *                    General Settings
			 ******************************************************/

			// Permissions tab
			global $wp_roles;

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}

			$settings[ 'general' ][ 'permission_import_access' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_permission_import_access' ], array_keys( $wp_roles->roles ) ),
				[
					sanitize_text_field( $_POST[ 'dse_permission_import_access' ] ),
					'administrator',
				]
			);

			$settings[ 'general' ][ 'permission_publish_access' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_permission_publish_access' ], array_keys( $wp_roles->roles ) ),
				[
					sanitize_text_field( $_POST[ 'dse_permission_publish_access' ] ),
					'administrator',
				]
			);

			$settings[ 'general' ][ 'permission_automation_access' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_permission_automation_access' ], array_keys( $wp_roles->roles ) ),
				[
					sanitize_text_field( $_POST[ 'dse_permission_automation_access' ] ),
					'administrator',
				]
			);

			$settings[ 'general' ][ 'permission_log_access' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_permission_log_access' ], array_keys( $wp_roles->roles ) ),
				[
					sanitize_text_field( $_POST[ 'dse_permission_log_access' ] ),
					'administrator',
				]
			);

			// Notification tab
			$settings[ 'general' ][ 'notification_enable' ] = $this->Get_Value( isset( $_POST[ 'dse_notification_enable' ] ), [ 'yes', 'no' ] );

			$settings[ 'general' ][ 'notification_mail_address' ] = $this->Get_Value(
				FALSE !== filter_var( $_POST[ 'dse_notification_mail_address' ], FILTER_VALIDATE_EMAIL ),
				[
					sanitize_email( $_POST[ 'dse_notification_mail_address' ] ),
					get_bloginfo( 'admin_email' ),
				]
			);

			$settings[ 'general' ][ 'notification_import' ]       = $this->Get_Value( isset( $_POST[ 'dse_notification_import' ] ), [ 'yes', 'no' ] );
			$settings[ 'general' ][ 'notification_publish' ]      = $this->Get_Value( isset( $_POST[ 'dse_notification_publish' ] ), [ 'yes', 'no' ] );
			$settings[ 'general' ][ 'notification_update' ]       = $this->Get_Value( isset( $_POST[ 'dse_notification_update' ] ), [ 'yes', 'no' ] );
			$settings[ 'general' ][ 'notification_order' ]        = $this->Get_Value( isset( $_POST[ 'dse_notification_order' ] ), [ 'yes', 'no' ] );
			$settings[ 'general' ][ 'notification_order_update' ] = $this->Get_Value( isset( $_POST[ 'dse_notification_order_update' ] ), [ 'yes', 'no' ] );

			$settings[ 'general' ][ 'proxy' ]          = $this->Get_Value( isset( $_POST[ 'dse_proxy' ] ), [ 'yes', 'no' ] );
			$settings[ 'general' ][ 'proxy_domain' ]   = $this->Get_Value(
				isset( $_POST[ 'dse_proxy_domain' ] ),
				[
					sanitize_text_field( trim( $_POST[ 'dse_proxy_domain' ] ) ),
					'',
				]
			);
			$settings[ 'general' ][ 'proxy_login' ]    = $this->Get_Value(
				isset( $_POST[ 'dse_proxy_login' ] ),
				[
					sanitize_text_field( trim( $_POST[ 'dse_proxy_login' ] ) ),
					'',
				]
			);
			$settings[ 'general' ][ 'proxy_password' ] = $this->Get_Value(
				isset( $_POST[ 'dse_proxy_password' ] ),
				[
					sanitize_text_field( trim( $_POST[ 'dse_proxy_password' ] ) ),
					'',
				]
			);

			/******************************************************
			 *                    AliExpress Settings
			 ******************************************************/

			/**
			 * Credentials tab
			 */

			$settings[ 'aliexpress' ][ 'enable' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_enable' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'official_api' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_official_api' ] ), [ 'yes', 'no' ] );
			$settings[ 'aliexpress' ][ 'api_key' ]      = isset( $_POST[ 'dse_aliexpress_api_key' ] ) ? sanitize_text_field( trim( $_POST[ 'dse_aliexpress_api_key' ] ) ) : '';
			if ( isset( $_POST[ 'dse_aliexpress_api_secret' ] ) && ! empty( $_POST[ 'dse_aliexpress_api_secret' ] ) ) {
				$settings[ 'aliexpress' ][ 'api_secret' ] = sanitize_text_field( trim( $_POST[ 'dse_aliexpress_api_secret' ] ) );
			}

			/*
			 * Automation tab
			 */

			$settings[ 'aliexpress' ][ 'auto_publish' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_auto_publish' ] ), [ 'yes', 'no' ] );

			// Prevent flooding the server with imports
			$aliexpress_schedule_count                    = absint( $_POST[ 'dse_aliexpress_schedule_count' ] );
			$aliexpress_schedule_count_valid              = 0 < $aliexpress_schedule_count && $aliexpress_schedule_count < 11;
			$settings[ 'aliexpress' ][ 'schedule_count' ] = $this->Get_Value( $aliexpress_schedule_count_valid, [ $aliexpress_schedule_count, 1 ] );

			$aliexpress_schedule_interval                 = absint( $_POST[ 'dse_aliexpress_schedule_every' ] );
			$aliexpress_schedule_valid                    = 1 < $aliexpress_schedule_interval && $aliexpress_schedule_interval < 61;
			$settings[ 'aliexpress' ][ 'schedule_every' ] = $this->Get_Value( $aliexpress_schedule_valid, [ $aliexpress_schedule_interval, 30 ] );

			$settings[ 'aliexpress' ][ 'schedule_delay' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_aliexpress_schedule_delay' ], [ 'minute', 'hour', 'day' ] ),
				[
					sanitize_text_field( $_POST[ 'dse_aliexpress_schedule_delay' ] ),
					'hour',
				]
			);

			$settings[ 'aliexpress' ][ 'auto_import' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_auto_import' ] ), [ 'yes', 'no' ] );

			/**
			 * AliExpress Sync
			 */
			$sync_keys = [
				'auto_sync_title',
				'auto_sync_images',
				'auto_sync_desc',
				'auto_sync_price',
				'auto_sync_stock',
				'auto_sync_reviews',
				'auto_sync_variations',
				'publish_sync_title',
				'publish_sync_images',
				'publish_sync_desc',
				'publish_sync_price',
				'publish_sync_stock',
				'publish_sync_reviews',
				'publish_sync_variations',
			];

			foreach ( $sync_keys as $sync_key ) {
				$settings[ 'aliexpress' ][ $sync_key ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_' . $sync_key ] ), [ 'yes', 'no' ] );
			}

			/**
			 * AliExpress Shipment
			 */
			$settings[ 'aliexpress' ][ 'auto_ship' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_auto_ship' ] ), [ 'yes', 'no' ] );

			if ( isset( $_POST[ 'dse_aliexpress_zinc_token' ] ) && ! empty( $_POST[ 'dse_aliexpress_zinc_token' ] ) ) {
				$settings[ 'aliexpress' ][ 'zinc_token' ] = sanitize_text_field( trim( $_POST[ 'dse_aliexpress_zinc_token' ] ) );
			}

			$settings[ 'aliexpress' ][ 'login_username' ] = isset( $_POST[ 'dse_aliexpress_login_username' ] ) ? sanitize_text_field( trim( $_POST[ 'dse_aliexpress_login_username' ] ) ) : '';
			if ( isset( $_POST[ 'dse_aliexpress_login_pass' ] ) && ! empty( $_POST[ 'dse_aliexpress_login_pass' ] ) ) {
				$settings[ 'aliexpress' ][ 'login_pass' ] = sanitize_text_field( trim( $_POST[ 'dse_aliexpress_login_pass' ] ) );
			}

			$settings[ 'aliexpress' ][ 'card_name' ]         = isset( $_POST[ 'dse_aliexpress_card_name' ] ) ? sanitize_text_field( trim( $_POST[ 'dse_aliexpress_card_name' ] ) ) : '';
			$settings[ 'aliexpress' ][ 'card_number' ]       = isset( $_POST[ 'dse_aliexpress_card_number' ] ) ? sanitize_text_field( trim( $_POST[ 'dse_aliexpress_card_number' ] ) ) : '';
			$settings[ 'aliexpress' ][ 'card_expiry_year' ]  = isset( $_POST[ 'dse_aliexpress_card_expiry_year' ] ) ? intval( trim( $_POST[ 'dse_aliexpress_card_expiry_year' ] ) ) : '';
			$settings[ 'aliexpress' ][ 'card_expiry_month' ] = isset( $_POST[ 'dse_aliexpress_card_expiry_month' ] ) ? intval( trim( $_POST[ 'dse_aliexpress_card_expiry_month' ] ) ) : '';
			if ( isset( $_POST[ 'dse_aliexpress_card_cvv' ] ) && ! empty( $_POST[ 'dse_aliexpress_card_cvv' ] ) ) {
				$settings[ 'aliexpress' ][ 'card_cvv' ] = sanitize_text_field( trim( $_POST[ 'dse_aliexpress_card_cvv' ] ) );
			}


			/**
			 * AliExpress Prices
			 */
			if (
				! isset( $_POST[ 'dse_aliexpress_price_type' ] ) ||
				! in_array( $_POST[ 'dse_aliexpress_price_type' ], [ 'original', 'flat', 'percent' ] )
			) {
				$settings[ 'aliexpress' ][ 'price_type' ] = 'original';
			} else {

				switch ( $_POST[ 'dse_aliexpress_price_type' ] ) {
					case 'flat':
						{
							if ( 0 < floatval( $_POST[ 'dse_aliexpress_price_flat_value' ] ) ) {
								$settings[ 'aliexpress' ][ 'price_type' ]       = 'flat';
								$settings[ 'aliexpress' ][ 'price_flat_value' ] = floatval( $_POST[ 'dse_aliexpress_price_flat_value' ] );
							} else {
								$settings[ 'aliexpress' ][ 'price_type' ] = 'original';
							}
							break;
						}
					case 'percent':
						{
							if ( 0 < floatval( $_POST[ 'dse_aliexpress_price_percent_value' ] ) ) {
								$settings[ 'aliexpress' ][ 'price_type' ]          = 'percent';
								$settings[ 'aliexpress' ][ 'price_percent_value' ] = floatval( $_POST[ 'dse_aliexpress_price_percent_value' ] );
							} else {
								$settings[ 'aliexpress' ][ 'price_type' ] = 'original';
							}
							break;
						}
					case 'original':
						{
							$settings[ 'aliexpress' ][ 'price_type' ] = 'original';
							break;
						}
					default :
						{
							$settings[ 'aliexpress' ][ 'price_type' ] = 'original';
						}
				}

			}

			$aliexpress_currencies = array_keys( self::Get_Currencies( 'aliexpress' ) );

			$settings[ 'aliexpress' ][ 'currency' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_aliexpress_currency' ], $aliexpress_currencies ),
				[ sanitize_text_field( $_POST[ 'dse_aliexpress_currency' ] ), 'auto' ]
			);

			/**
			 * AliExpress Stock Management
			 */
			$settings[ 'aliexpress' ][ 'enable_stock_manager' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_enable_stock_manager' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'stock_update' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_aliexpress_stock_update' ], [ 'outofstuck', 'draft', 'trash', 'nothing' ] ),
				[
					sanitize_text_field( $_POST[ 'dse_aliexpress_stock_update' ] ),
					'nothing',
				]
			);

			/**
			 * AliExpress Review Settings
			 */
			$settings[ 'aliexpress' ][ 'import_reviews' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_import_reviews' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'review_import_count' ] = $this->Get_Value(
				( isset( $_POST[ 'dse_aliexpress_review_import_count' ] ) && 0 !== intval( $_POST[ 'dse_aliexpress_review_import_count' ] ) ),
				[
					absint( $_POST[ 'dse_aliexpress_review_import_count' ] ),
					30,
				]
			);

			$settings[ 'aliexpress' ][ 'import_review_images' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_import_review_images' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'import_review_translate' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_import_review_translate' ] ), [ 'Y', 'N' ] );

			/**
			 * AliExpress Search & Replace
			 */
			$settings[ 'aliexpress' ][ 'enable_replacements' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_enable_replacements' ] ), [ 'yes', 'no' ] );

			if (
				isset( $_POST[ 'dse_aliexpress_replace_rule' ] ) &&
				is_array( $_POST[ 'dse_aliexpress_replace_rule' ] ) &&
				! empty( $_POST[ 'dse_aliexpress_replace_rule' ] )
			) {

				// Clear temp array
				$aliexpress_replace_rules = [];

				foreach ( $_POST[ 'dse_aliexpress_replace_rule' ] as $item ) {

					// Don't save the empty records
					if ( empty( $item[ 'search' ] ) ) {
						continue;
					}

					$aliexpress_replace_rules[] = [
						'search'        => sanitize_text_field( $item[ 'search' ] ),
						'value'         => sanitize_text_field( $item[ 'value' ] ),
						'apply_title'   => isset( $item[ 'apply_title' ] ) ? 'yes' : 'no',
						'apply_desc'    => isset( $item[ 'apply_desc' ] ) ? 'yes' : 'no',
						'apply_attr'    => isset( $item[ 'apply_attr' ] ) ? 'yes' : 'no',
						'apply_tags'    => isset( $item[ 'apply_tags' ] ) ? 'yes' : 'no',
						'apply_reviews' => isset( $item[ 'apply_reviews' ] ) ? 'yes' : 'no',
					];

				}

				if ( ! empty( $aliexpress_replace_rules ) ) {
					$settings[ 'aliexpress' ][ 'replace_rule' ] = $aliexpress_replace_rules;
				}
			}

			/**
			 * AliExpress Miscellaneous Settings
			 */
			$aliexpress_languages = array_keys( self::Get_Languages( 'aliexpress' ) );

			$settings[ 'aliexpress' ][ 'language' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_aliexpress_language' ], $aliexpress_languages ),
				[ sanitize_text_field( $_POST[ 'dse_aliexpress_language' ] ), 'auto' ]
			);

			$settings[ 'aliexpress' ][ 'import_content_desc' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_import_content_desc' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'import_content_attr' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_import_content_attr' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'import_content_cat' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_import_content_cat' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'import_content_tags' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_import_content_tags' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'check_duplicate_images' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_check_duplicate_images' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'import_product_images' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_aliexpress_import_product_images' ], [ 'download', 'external', 'none' ] ),
				[ sanitize_text_field( $_POST[ 'dse_aliexpress_import_product_images' ] ), 'none' ]
			);

			$settings[ 'aliexpress' ][ 'import_desc_images' ] = $this->Get_Value(
				in_array( $_POST[ 'dse_aliexpress_import_desc_images' ], [ 'download', 'external', 'drop' ] ),
				[ sanitize_text_field( $_POST[ 'dse_aliexpress_import_desc_images' ] ), 'drop' ]
			);

			$settings[ 'aliexpress' ][ 'default_product_type' ] = $this->Get_Value(
				'external' === $_POST[ 'dse_aliexpress_default_product_type' ],
				[ 'external', 'simple' ]
			);

			$settings[ 'aliexpress' ][ 'dynamic_cat' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_dynamic_cat' ] ), [ 'yes', 'no' ] );

			$settings[ 'aliexpress' ][ 'default_cat' ] = $this->Get_Value( isset( $_POST[ 'dse_aliexpress_default_cat' ] ), [ intval( $_POST[ 'dse_aliexpress_default_cat' ] ), 0 ] );

			// Save the settings
			foreach ( $settings as $key => $setting ) {
				update_option( 'dse_' . $key, $setting, TRUE );
			}

			DSE_Core::Throw_Admin_Notice( esc_html__( 'Successfully saved settings.', 'dropshipexpress' ) );

			// Redirect the user to the setting page
			wp_safe_redirect( admin_url( 'admin.php?page=dropship-express' ), 303 );
			exit();

		}

		/**
		 * Public method to retrieve all the settings of
		 * the plugin
		 *
		 * @return array
		 */
		public static function Get_Settings() {
			return self::$settings;
		}

		/**
		 * Private setter method to set the internal
		 * values
		 *
		 * @param       $input
		 * @param array $assign_array
		 * @param array $compare_to
		 *
		 * @return bool|mixed
		 */
		private function Get_Value( $input, array $assign_array, array $compare_to = [] ) {
			// If there's an array to compare to, and the condition is true
			if ( ! empty( $compare_to ) && ! empty( $input ) ) {
				// Check each value and see if the input matches it
				foreach ( $compare_to as $key => $compare_value ) {
					if ( $compare_value === $input ) {
						return $assign_array[ $key ];
					}
				}
			} else {
				if ( TRUE === $input ) {
					return ( $assign_array[ 0 ] );
				} else {
					return $assign_array[ 1 ];
				}
			}

			return FALSE;
		}

		/**
		 * Method to get a list of supported currencies by a
		 * retailer
		 *
		 * @param $store
		 *
		 * @return array
		 */
		public static function Get_Currencies( $store ) {

			$currencies = [
				'aliexpress' => [
					'AUD' => esc_html__( 'Australian dollar ($)', 'dropshipexpress' ),
					'BRL' => esc_html__( 'Brazilian cruzeiro real (CR$)', 'dropshipexpress' ),
					'CAD' => esc_html__( 'Canadian Dollar ($)', 'dropshipexpress' ),
					'EUR' => esc_html__( 'Euro (€)', 'dropshipexpress' ),
					'GBP' => esc_html__( 'United Kingdom Pound (£)', 'dropshipexpress' ),
					'IDR' => esc_html__( 'Indonesian rupiah (Rp)', 'dropshipexpress' ),
					'INR' => esc_html__( 'Indian rupee (₹)', 'dropshipexpress' ),
					'JPY' => esc_html__( 'Japan Yen (¥)', 'dropshipexpress' ),
					'KRW' => esc_html__( 'Korea Won (₩)', 'dropshipexpress' ),
					'MXN' => esc_html__( 'Mexican peso ($)', 'dropshipexpress' ),
					'RUB' => esc_html__( 'Russia Ruble (₽)', 'dropshipexpress' ),
					'SEK' => esc_html__( 'Sweden Krona (kr)', 'dropshipexpress' ),
					'TRY' => esc_html__( 'Turkish lira (₺)', 'dropshipexpress' ),
					'UAH' => esc_html__( 'Ukrainian hryvnia (₴)', 'dropshipexpress' ),
					'USD' => esc_html__( 'United States Dollar ($)', 'dropshipexpress' ),
				],
			];

			if ( isset( $currencies[ $store ] ) ) {
				return $currencies[ $store ];
			}

			return [];

		}

		/**
		 * Method to get an array of languages supported
		 * by a retailer
		 *
		 * @param $store
		 *
		 * @return array
		 */
		public static function Get_Languages( $store ) {

			$langs = [
				'aliexpress' => [
					'AR' => esc_html__( 'العربية', 'dropshipexpress' ),
					'DE' => esc_html__( 'Deutsch', 'dropshipexpress' ),
					'EN' => esc_html__( 'English', 'dropshipexpress' ),
					'ES' => esc_html__( 'Español', 'dropshipexpress' ),
					'CL' => esc_html__( 'Español (Chile)', 'dropshipexpress' ),
					'MX' => esc_html__( 'Español (Mexico)', 'dropshipexpress' ),
					'HE' => esc_html__( 'עברית', 'dropshipexpress' ),
					'IW' => esc_html__( 'Modern Hebrew', 'dropshipexpress' ),
					'FR' => esc_html__( 'Français', 'dropshipexpress' ),
					'ID' => esc_html__( 'Bahasa Indonesia', 'dropshipexpress' ),
					'IT' => esc_html__( 'Italiano', 'dropshipexpress' ),
					'JA' => esc_html__( '日本語 (にほんご)', 'dropshipexpress' ),
					'KO' => esc_html__( '한국어', 'dropshipexpress' ),
					'NL' => esc_html__( 'Nederlands', 'dropshipexpress' ),
					'PL' => esc_html__( 'Polszczyzna', 'dropshipexpress' ),
					'PT' => esc_html__( 'Português', 'dropshipexpress' ),
					'RU' => esc_html__( 'Русский', 'dropshipexpress' ),
					'TH' => esc_html__( 'ไทย', 'dropshipexpress' ),
					'TR' => esc_html__( 'Türkçe', 'dropshipexpress' ),
					'VI' => esc_html__( 'Tiếng Việt', 'dropshipexpress' ),
				],
			];

			if ( isset( $langs[ $store ] ) ) {
				return $langs[ $store ];
			}

			return [];
		}

		/**
		 * Method to set the default settings for a section
		 *
		 * @param string $setting
		 *
		 * @return bool
		 */
		public static function Set_Defaults( string $setting ) {
			$sections = [ 'general', 'aliexpress' ];

			if ( ! in_array( $setting, $sections ) ) {
				return FALSE;
			}

			$default_values = [
				'general'    => [
					'permission_import_access'     => 'administrator',
					'permission_publish_access'    => 'administrator',
					'permission_automation_access' => 'administrator',
					'permission_log_access'        => 'administrator',
					'notification_enable'          => 'no',
					'notification_mail_address'    => get_bloginfo( 'admin_email' ),
					'notification_import'          => 'no',
					'notification_publish'         => 'no',
					'notification_update'          => 'no',
					'notification_order'           => 'no',
					'notification_order_update'    => 'no',
					'proxy'                        => 'no',
					'proxy_domain'                 => '',
					'proxy_login'                  => '',
					'proxy_password'               => '',
				],
				'aliexpress' => [
					'enable'                  => 'no',
					'auto_publish'            => 'no',
					'official_api'            => 'no',
					'api_key'                 => '',
					'api_secret'              => '',
					'schedule_count'          => 1,
					'schedule_every'          => 1,
					'schedule_delay'          => 'hour',
					'auto_import'             => 'no',
					'auto_sync_title'         => 'no',
					'auto_sync_images'        => 'no',
					'auto_sync_desc'          => 'no',
					'auto_sync_price'         => 'no',
					'auto_sync_stock'         => 'no',
					'auto_sync_reviews'       => 'no',
					'auto_sync_variations'    => 'no',
					'publish_sync_title'      => 'no',
					'publish_sync_images'     => 'no',
					'publish_sync_desc'       => 'no',
					'publish_sync_price'      => 'no',
					'publish_sync_stock'      => 'no',
					'publish_sync_reviews'    => 'no',
					'publish_sync_variations' => 'no',
					'auto_ship'               => 'no',
					'zinc_token'              => '',
					'login_username'          => '',
					'login_pass'              => '',
					'login_auth'              => '',
					'card_name'               => '',
					'card_number'             => '',
					'card_expiry_year'        => '',
					'card_expiry_month'       => '',
					'card_cvv'                => '',
					'price_type'              => 'original',
					'currency'                => 'auto',
					'enable_stock_manager'    => 'yes',
					'stock_update'            => 'nothing',
					'import_reviews'          => 'no',
					'review_import_count'     => 5,
					'import_review_images'    => 'no',
					'import_review_translate' => 'Y',
					'enable_replacements'     => 'no',
					'language'                => 'auto',
					'import_content_desc'     => 'yes',
					'import_content_attr'     => 'yes',
					'import_content_cat'      => 'yes',
					'import_content_tags'     => 'yes',
					'check_duplicate_images'  => 'yes',
					'import_product_images'   => 'download',
					'import_desc_images'      => 'external',
					'default_product_type'    => 'simple',
					'dynamic_cat'             => 'no',
					'default_cat'             => 0,
				],
			];

			if ( FALSE === get_option( $setting ) ) {
				add_option( $setting, $default_values[ $setting ] );
			}

			return TRUE;
		}

		/**
		 * Function to assign a value to a setting to be saved
		 *
		 * @param $section
		 * @param $option
		 * @param $value
		 */
		private function Set_Value( $section, $option, $value ) {
			$this->setting_to_save[ $section ][ $option ] = $value;
		}

		/**
		 * Method to update the activation code entered by the
		 * user
		 *
		 */
		public static function Update_Activation() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to perform this operation.', 'dropshipexpress' ) );
			}

			// Check the nonce
			check_admin_referer( 'dse-validation-page', 'dse_activation_nonce' );

			// Check if the code is valid
			$is_valid = self::Register_License();

			wp_safe_redirect( admin_url( 'admin.php?page=dse-activation&dse-activation-response=' . base64_encode( $is_valid[ 'message' ] ) . '&dse-activation-status=' . $is_valid[ 'status' ] ), 303 );
			exit();
		}

		/**
		 * Method to check if a purchase code is valid or not
		 *
		 *
		 * @return array
		 */
		public static function Register_License() {

			if ( ! is_user_logged_in() ) {
				return [ 'message' => esc_html__( 'You must be logged in to perform this action.', 'dropshipexpress' ), 'status' => 'warning' ];
			}

			$user = wp_get_current_user();

			$api_url = add_query_arg(
				[
					'action'   => 'register',
					'email'    => get_option( 'admin_email' ),
					'domain'   => get_site_url(),
					'username' => $user->user_login,
					'token'    => get_option( 'dse_api_token', '' ),
				],
				DSE_API_URL . '/restful/dse/v1/users'
			);

			// Send the request
			$remote = wp_remote_get( $api_url );
			$body   = wp_remote_retrieve_body( $remote );

			// If something went wrong
			if ( empty( $body ) ) {
				return [ 'message' => esc_html__( 'Received an empty response from server. Please try again later.', 'dropshipexpress' ), 'status' => 'warning' ];
			}

			$response = json_decode( $body );

			// If the response is valid
			if ( isset( $response->status ) ) {
				// If all is correct
				if ( 200 === $response->status ) {

					// Activation status
					update_option( 'dse_is_activated', 'yes', TRUE );

					// Save the token
					if ( isset( $response->token ) && 20 === strlen( $response->token ) ) {
						update_option( 'dse_api_token', $response->token );
					}

					return [ 'message' => '', 'status' => 'success' ];
				} else {
					// If there's been an api error
					return [ 'message' => $response->message, 'status' => 'warning' ];
				}
			}

			return [ 'message' => esc_html__( 'An unknown error happened. Please try again later.', 'dropshipexpress' ), 'status' => 'success' ];
		}

		/**
		 * Method to upgrade from old values
		 * to new values
		 *
		 */
		public static function Upgrade_Helper() {

			// Check if this task has already been performed
			if ( 'yes' === get_option( 'dse_migrated' ) ) {
				return;
			}

			// Migrate the old options
			$options_array = [
				'general',
				'aliexpress',
				'orders',
				'import_rules',
				'is_activated',
			];

			foreach ( $options_array as $item ) {
				if ( FALSE !== $option = get_option( "adfw_{$item}" ) ) {
					$result = add_option( "dse_{$item}", $option );

					// If the new option is successfully added, delete the old one
					if ( TRUE === $result ) {
						delete_option( "adfw_{$item}" );
					}

					unset( $option, $result );
				}
			}

			// Migrate the post types
			global $wpdb;

			// Update the imported post type
			$wpdb->update(
				$wpdb->posts,
				[ 'post_type' => 'dse_imported' ],
				[ 'post_type' => 'adfw_imported' ]
			);

			// Update the published post type
			$wpdb->update(
				$wpdb->posts,
				[ 'post_type' => 'dse_published' ],
				[ 'post_type' => 'adfw_published' ]
			);

			// Update taxonomies
			$wpdb->update(
				$wpdb->term_taxonomy,
				[ 'taxonomy' => 'dse_source' ],
				[ 'taxonomy' => 'adfw_source' ],
			);

			// Update draft products
			$imported_sql     = $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = REPLACE( meta_key, 'adfw', 'dse' ) WHERE post_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type = 'adfw_imported' )" );
			$imported_results = $wpdb->query( $imported_sql );

			// Update published product
			$products_sql     = $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = REPLACE( meta_key, 'adfw', 'dse' ) WHERE post_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' )" );
			$products_results = $wpdb->query( $products_sql );

			// Set a flag to prevent running this code multiple times
			if ( FALSE !== $products_results && FALSE !== $imported_results ) {
				update_option( 'dse_migrated', 'yes' );
			}

		}

		/**
		 * Method to output the upgrade page
		 *
		 */
		public static function Upgrade_Page_CB() {
			// Check if the plugin is activated
			if ( ! DSE_Settings::Is_Activated() ) {
				wp_safe_redirect( admin_url( 'admin.php?page=dse-activation' ), 303 );
				exit();
			}

			require_once( DSE_PLUGIN_FOLDER . '/templates/admin/settings-upgrade.php' );

		}

		/**
		 * Method to check which user can perform an specific task.
		 *
		 * @param string $task
		 *
		 * @return string
		 */
		public static function Who_Can( string $task ) {

			// Only for logged-in users
			if ( ! is_user_logged_in() ) {
				return FALSE;
			}

			$required_role = DSE_Settings::Get_Setting( 'general', $task );

			$current_user = wp_get_current_user();

			if ( in_array( $required_role, $current_user->roles ) ) {
				return $required_role;
			}

			return 'manage_options';
		}

		/**
		 * Function to remove a single import rule
		 *
		 */
		public static function Remove_Single_Rule() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to perform this operation.', 'dropshipexpress' ) );
			}

			check_admin_referer( 'dse-single-import-rules-nonce-action', 'dse_single_import_rules_nonce' );

			if ( isset( $_POST[ 'dse_remove_rule_value' ] ) && ! empty( $_POST[ 'dse_remove_rule_value' ] ) ) {

				$import_rules = get_option( 'dse_import_rules', [] );

				// Remove the rule
				if ( array_key_exists( $_POST[ 'dse_remove_rule_value' ], $import_rules ) ) {
					unset( $import_rules[ $_POST[ 'dse_remove_rule_value' ] ] );
				}

				// Update the options
				update_option( 'dse_import_rules', $import_rules, TRUE );

			}

			// Redirect the user to the setting page
			wp_safe_redirect( admin_url( 'admin.php?page=dse-import-rules' ), 303 );
			exit();

		}

		/**
		 * Method to retrieve a single option
		 *
		 * @param $section
		 * @param $option
		 * @param $wp_error
		 *
		 * @return mixed|\WP_Error
		 */
		public static function Get_Setting( $section, $option, $wp_error = FALSE ) {

			if ( NULL === self::$settings ) {
				return new WP_Error( 'dse_too_soon', esc_html__( 'It\'s too soon to request this data. Please try a later hook.', 'dropshipexpress' ) );
			}

			$settings = self::$settings;

			if ( ! isset( $settings[ $section ] ) ) {
				if ( $wp_error ) {
					return new WP_Error( 'dse_wrong_section', esc_html__( 'This section does not exist in the plugin\'s settings.', 'dropshipexpress' ) );
				} else {
					return '';
				}
			}

			if ( ! isset( $settings[ $section ][ $option ] ) ) {
				if ( $wp_error ) {
					return new WP_Error( 'dse_wrong_option', esc_html__( 'This option does not exist in the plugin\'s settings.', 'dropshipexpress' ) );
				} else {
					return '';
				}
			}

			return $settings[ $section ][ $option ];

		}

	}
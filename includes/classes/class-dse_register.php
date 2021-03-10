<?php
	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Enqueue
	 *
	 * Class used to enqueue styles and scripts, register
	 * post types, taxonomies, rest routes and so.
	 *
	 */
	class DSE_Register {

		/*
		 * Hold an instance of the class
		 */
		private static $instance;

		/**
		 * DSE_Register constructor.
		 *
		 */
		public function __construct() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = $this;
			}

			// Register the hooks used by the plugin
			new DSE_Hooks();

			/// Register the filters used by the plugin
			new DSE_Filters();

			// Dynamically register cron job intervals and start processing them
			$this->Register_Crons();

			// Add the necessary user roles and capabilities
			//$this->Register_User_Roles();

		}

		/**
		 * Register a new tab to output the product data
		 * that belongs to this plugin in wc's data tab
		 *
		 * @param $product_data_tabs
		 *
		 * @return mixed
		 */
		public static function WC_Data_Tabs( $product_data_tabs ) {
			global $post;

			// Check if the product is managed by this plugin
			if ( 'yes' === $post->dse_product ) {
				$product_data_tabs[ 'dse-product-data' ] = [
					'label'  => esc_html__( 'DropshipExpress', 'dropshipexpress' ),
					'target' => 'dse_product_data_tab',
				];
			}

			return $product_data_tabs;
		}

		/**
		 * Callback function to output product data
		 * that belongs to this plugin in wc's data tab
		 *
		 */
		public static function WC_Data_Tab_Content() {
			global $woocommerce, $post;

			// Only for products managed by this plugin
			if ( 'yes' !== $post->dse_product ) {
				return;
			} ?>

			<!-- Begin DSE Product Data Tab -->
			<div id="dse_product_data_tab" class="panel woocommerce_options_panel">
				<div class="options_group">
					<?php
						// Product's ID
						woocommerce_wp_text_input(
							[
								'id'          => 'dse_wc_product_id',
								'label'       => esc_html__( 'Product ID', 'dropshipexpress' ),
								'value'       => get_post_meta( $post->ID, 'dse_product_id', TRUE ),
								'desc_tip'    => TRUE,
								'description' => esc_html__( 'Product ID on the retailer store', 'dropshipexpress' ),
								'placeholder' => esc_html__( 'Product ID', 'dropshipexpress' ),
							]
						);

						// Product's URL
						woocommerce_wp_text_input(
							[
								'id'          => 'dse_wc_product_url',
								'label'       => esc_html__( 'Product URL', 'dropshipexpress' ),
								'value'       => get_post_meta( $post->ID, 'dse_product_url', TRUE ),
								'desc_tip'    => TRUE,
								'description' => esc_html__( 'Full product link on the retailer store', 'dropshipexpress' ),
								'placeholder' => esc_html__( 'Product URL', 'dropshipexpress' ),
								'data_type'   => 'url',
								//'custom_attributes'=>['disabled'=>'disabled']
							]
						);
					?>
				</div>

				<div class="options_group">
					<?php
						// Syncing option
						woocommerce_wp_checkbox(
							[
								'id'          => 'dse_wc_product_sync',
								'label'       => esc_html__( 'Disable Sync?', 'dropshipexpress' ),
								'value'       => get_post_meta( $post->ID, 'dse_disable_sync', TRUE ),
								'desc_tip'    => FALSE,
								'description' => esc_html__( 'Disable syncing this product regardless of the syncing options', 'dropshipexpress' ),
								//'custom_attributes'=>['disabled'=>'disabled']
							]
						);
					?>
				</div>
			</div>
			<!-- End DSE Product Data Tab -->
			<?php
		}

		/**
		 * Process the cronjobs
		 */
		public function Register_Crons() {

			// Import cronjobs
			DSE_Import::Process_Crons();

			// Order cronjobs
			DSE_Shipping::Process_Crons();

		}

		/**
		 * Add the required roles and user capabilities
		 *
		 */
		private function Register_User_Roles() {
			// todo: add this
			//add_action( 'init', [ __CLASS__, 'Add_Roles' ] );
		}

		public static function Add_Roles( array $roles = [ 'dse_admin', 'dse_manager', 'dse_author' ] ) {


		}

		/**
		 * Method used to enqueue the registered scripts
		 *
		 * @param $hook
		 */
		public static function Enqueue_Scripts( $hook ) {

			// Register the scripts
			self::$instance->Register_Scripts( $hook );

			$plugin_pages = [
				'toplevel_page_dropship-express',
				'dropshipexpress_page_dse-import-products',
				'dropshipexpress_page_dse-import-rules',
				'dropshipexpress_page_dse-view-imported',
				'dropshipexpress_page_dse-statistics',
				'dropshipexpress_page_dse-logs',
				'dropshipexpress_page_dse-upgrade',
				'dropshipexpress_page_dse-support',
			];

			// Only enqueue the style on plugin's pages
			if ( is_admin() && in_array( $hook, $plugin_pages ) ) {

				wp_enqueue_script( 'dse-bootstrap' );
				wp_enqueue_script( 'dse-popper' );
				wp_enqueue_script( 'dse-bootstrap-select' );
				wp_enqueue_script( 'dse-select2' );
				wp_enqueue_script( 'dse-bootstrap-touchspin' );
				wp_enqueue_script( 'dse-repeater' );
				wp_enqueue_script( 'dse-blockUI' );
				wp_enqueue_script( 'dse-prism' );
				wp_enqueue_script( 'dse-cleave' );
				wp_enqueue_script( 'dse-toastr' );
				wp_enqueue_script( 'dse-admin-scripts' );

				// Localization for the admin scripts
				$aliexpress_import_rules  = DSE_Settings::Get_Setting( 'aliexpress', 'import_rule' );
				$aliexpress_replace_rules = DSE_Settings::Get_Setting( 'aliexpress', 'replace_rule' );

				/**
				 * Import rules for AliExpress
				 */
				if ( ! empty( $aliexpress_import_rules ) ) {
					foreach ( $aliexpress_import_rules as $rule ) {
						$localization_data[ 'aliexpress_import_rules' ][] = $rule;
					}
				}

				/**
				 * Search & Replace rules for AliExpress
				 */
				if ( ! empty( $aliexpress_replace_rules ) ) {
					foreach ( $aliexpress_replace_rules as $rule ) {
						$localization_data[ 'aliexpress_replace_rules' ][] = $rule;
					}
				}

				/**
				 * i18n
				 */
				$localization_data[ 'i18n' ] = [
					'processing'    => esc_html__( 'Processing ...', 'dropshipexpress' ),
					'searching'     => esc_html__( 'Searching ...', 'dropshipexpress' ),
					'error_unknown' => esc_html__( 'An unknown error has occurred while trying to process. Please try again in a moment.', 'dropshipexpress' ),
				];

				if ( isset( $localization_data ) ) {
					wp_localize_script( 'dse-admin-scripts', 'dse_admin_localization', $localization_data );
				}

			}
		}

		/**
		 * Method used to register the required scripts
		 *
		 * @param $hook
		 */
		public function Register_Scripts( $hook ) {

			/**
			 * Popper.js
			 *
			 */
			wp_register_script(
				'dse-popper',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/popper.js/popper.min.js',
				[ 'jquery' ],
				'1.15.0',
				TRUE
			);

			/**
			 * Bootstrap
			 *
			 */
			wp_register_script(
				'dse-bootstrap',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/bootstrap/bootstrap.min.js',
				[ 'jquery', 'dse-popper' ],
				'4.3.1',
				TRUE
			);

			/**
			 * Bootstrap Select
			 *
			 */
			wp_register_script(
				'dse-bootstrap-select',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/bootstrap-select/bootstrap-select.min.js',
				[ 'jquery', 'dse-bootstrap', 'dse-popper' ],
				'1.13.5',
				TRUE
			);

			/**
			 * Select2
			 *
			 */
			wp_register_script(
				'dse-select2',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/select2/select2.full.min.js',
				[ 'jquery' ],
				'4.0.6',
				TRUE
			);

			/**
			 * Bootstrap Touchspin
			 *
			 */
			wp_register_script(
				'dse-bootstrap-touchspin',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js',
				[ 'jquery', 'dse-bootstrap' ],
				'4.2.5',
				TRUE
			);

			/**
			 * jQuery Repeater
			 *
			 */
			wp_register_script(
				'dse-repeater',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/jquery.repeater/jquery.repeater.min.js',
				[ 'jquery' ],
				'1.2.1',
				TRUE
			);

			/**
			 * jQuery blockUI
			 *
			 */
			wp_register_script(
				'dse-blockUI',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/block-ui/jquery.blockUI.js',
				[ 'jquery' ],
				'2.70.0',
				TRUE
			);

			/**
			 * Prism.js
			 *
			 */
			wp_register_script(
				'dse-prism',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/prism.js/prism.js',
				[ 'jquery' ],
				'1.21.0',
				TRUE
			);

			/**
			 * Cleave.js
			 *
			 */
			wp_register_script(
				'dse-cleave',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/cleave.js/cleave.min.js',
				[ 'jquery' ],
				'1.6.0',
				TRUE
			);

			/**
			 * Toastr
			 *
			 */
			wp_register_script(
				'dse-toastr',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/toastr/toastr.min.js',
				[ 'jquery' ],
				'2.1.4',
				TRUE
			);

			/**
			 * Main Script file
			 */
			wp_register_script(
				'dse-admin-scripts',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/js/admin-scripts.js',
				[ 'jquery', 'dse-bootstrap-select', 'dse-toastr' ],
				'1.0.0',
				TRUE
			);
		}

		/**
		 * Method used to enqueue the styles
		 *
		 * @param $hook
		 */
		public static function Enqueue_Styles( $hook ) {

			// Register the styles
			self::$instance->Register_Styles( $hook );

			$plugin_pages = [
				'toplevel_page_dropship-express',
				'dropshipexpress_page_dse-import-products',
				'dropshipexpress_page_dse-view-imported',
				'dropshipexpress_page_dse-import-rules',
				'dropshipexpress_page_dse-statistics',
				'dropshipexpress_page_dse-logs',
				'dropshipexpress_page_dse-activation',
				'dropshipexpress_page_dse-upgrade',
				'dropshipexpress_page_dse-support',
			];

			// Only enqueue the style on plugin's pages
			if ( is_admin() && in_array( $hook, $plugin_pages ) ) {

				wp_enqueue_style( 'dse-bootstrap-select' );
				wp_enqueue_style( 'dse-select2' );
				wp_enqueue_style( 'dse-touchspin' );
				wp_enqueue_style( 'dse-fontawesome' );
				wp_enqueue_style( 'dse-prism' );
				wp_enqueue_style( 'dse-toastr' );
				wp_enqueue_style( 'dse-fonts' );
				wp_enqueue_style( 'dse-admin-styles' );

			}

			// General admin styles
			if ( is_admin() ) {
				wp_enqueue_style( 'dse-general-styles' );
			}
		}

		/**
		 * Method used to register the required styles
		 *
		 * @param $hook
		 */
		public function Register_Styles( $hook ) {

			/**
			 * General styles
			 */
			wp_register_style(
				'dse-general-styles',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/css/general-styles.css',
				NULL,
				'1.0.0'
			);

			/**
			 * Main Style file
			 */
			wp_register_style(
				'dse-admin-styles',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/css/admin-styles.css',
				NULL,
				'1.0.0'
			);

			/**
			 * Google Fonts
			 */
			wp_register_style(
				'dse-fonts',
				'https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700',
				NULL,
				NULL
			);

			/**
			 * Font-awesome
			 *
			 */
			wp_register_style(
				'dse-fontawesome',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/fonts/fontawesome/css/all.css',
				NULL,
				'5.8.1'
			);

			/**
			 * Bootstrap Select
			 *
			 */
			wp_register_style(
				'dse-bootstrap-select',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/bootstrap-select/bootstrap-select.min.css',
				NULL,
				'1.13.5'
			);

			/**
			 * Bootstrap Select
			 *
			 */
			wp_register_style(
				'dse-select2',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/select2/select2.min.css',
				NULL,
				'4.0.6'
			);

			/**
			 * Bootstrap Touchspin
			 *
			 */
			wp_register_style(
				'dse-touchspin',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css',
				NULL,
				'4.2.5'
			);

			/**
			 * Prism.js Styles
			 *
			 */
			wp_register_style(
				'dse-prism',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/prism.js/prism.css',
				NULL,
				'1.21.0'
			);

			/**
			 * Toastr styles
			 *
			 */
			wp_register_style(
				'dse-toastr',
				plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/toastr/toastr.css',
				NULL,
				'2.1.4'
			);

		}

		public static function Enqueue_User_Scripts( $hook ) {
			// Enqueue the front-end scripts
			if ( ! is_admin() && is_singular( 'product' ) ) {

				/**
				 * Toastr
				 *
				 */
				wp_register_script(
					'dse-toastr',
					plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/toastr/toastr.min.js',
					[ 'jquery' ],
					'2.1.4',
					TRUE
				);

				/**
				 * User scripts
				 *
				 */
				wp_register_script(
					'dse-user-scripts',
					plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/js/user-scripts.js',
					[ 'jquery', 'dse-toastr' ],
					'1.0.0',
					TRUE
				);

				wp_enqueue_script( 'toastr' );
				wp_enqueue_script( 'dse-user-scripts' );

				// Localize the front-end script
				$client_localization_data[ 'constants' ] = [
					'is_product'     => TRUE,
					'is_dse_product' => 'yes' === get_post_meta( get_the_ID(), 'dse_product', TRUE ),
					'ajax_url'       => admin_url( 'admin-ajax.php' ),
					'product_id'     => get_the_ID(),
				];
				$client_localization_data[ 'i18n' ]      = [
					'refresh_required' => esc_html__( 'The product you are viewing has been updated. Please refresh the page.', 'dropshipexpress' ),
				];
				wp_localize_script( 'dse-user-scripts', 'dse_client_localization', $client_localization_data );
			}
		}

		/**
		 * Method to output the client-side styles
		 *
		 * @param $hook
		 */
		public static function Enqueue_User_Styles( $hook ) {

			// Enqueue the client-side styles
			if ( ! is_admin() && is_singular( 'product' ) ) {

				/**
				 * Toastr styles
				 *
				 */
				wp_register_style(
					'dse-toastr',
					plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/vendors/toastr/toastr.css',
					NULL,
					'2.1.4'
				);

				/**
				 * Front-end styles
				 *
				 */
				wp_register_style(
					'dse-user-styles',
					plugin_dir_url( DSE_PLUGIN_FILE ) . 'assets/css/user-styles.css',
					NULL,
					'1.0.0'
				);

				wp_enqueue_style( 'dse-toastr' );
				wp_enqueue_style( 'dse-user-styles' );
			}
		}

		public function Register_Admin_Requests() {

		}

		/**
		 * Method to register the required post types
		 * to store the queued and imported products
		 *
		 */
		public static function Register_CPT() {
			$imported_cpt_args = [
				'labels'             => [ 'name' => esc_html__( 'Imported Products' ) ],
				'public'             => FALSE,
				'publicly_queryable' => FALSE,
				'show_ui'            => FALSE,
				'show_in_menu'       => FALSE,
				'query_var'          => FALSE,
				'capability_type'    => 'post',
				'has_archive'        => FALSE,
				'hierarchical'       => FALSE,
				'menu_position'      => NULL,
				'supports'           => [ 'title', 'author', 'excerpt' ],
			];

			$queued_cpt_args = [
				'labels'             => [ 'name' => esc_html__( 'Published Products' ) ],
				'public'             => FALSE,
				'publicly_queryable' => FALSE,
				'show_ui'            => FALSE,
				'show_in_menu'       => FALSE,
				'query_var'          => FALSE,
				'capability_type'    => 'post',
				'has_archive'        => FALSE,
				'hierarchical'       => FALSE,
				'menu_position'      => NULL,
				'supports'           => [ 'title', 'author', 'excerpt' ],
			];

			register_post_type( 'dse_imported', $imported_cpt_args );
			register_post_type( 'dse_published', $queued_cpt_args );
		}

		/**
		 * Static method to register a new cron interval
		 * for our plugin
		 *
		 * @param $schedules
		 *
		 * @return mixed
		 */
		public static function Register_Cron_Interval( $schedules ) {

			// Equivalent of each word in seconds
			$delays_translation = [
				'minute' => 60,
				'hour'   => 60 * 60,
				'day'    => 24 * 60 * 60,
			];

			// Add the interval used to publish queued products
			$schedules[ 'dse_queue_cron' ] = [
				'interval' => apply_filters( 'dse_cron_interval', 60 ),
				'display'  => esc_html__( 'DropshipExpress: Default cron running every minute', 'dropshipexpress' ),
			];

			// Add the intervals used by the import rules
			$import_rules = get_option( 'dse_import_rules', '' );

			if ( $import_rules ) {
				foreach ( $import_rules as $key => $import_rule ) {
					$schedules[ "dse_autoimport_{$key}" ] = [
						'interval' => $import_rule[ 'timer' ] * $delays_translation[ $import_rule[ 'delay' ] ],
						'display'  => sprintf(
						/* translators: %1$s is replaced with the cronjob timer in seconds */
							esc_html__( 'DropshipExpress: Every %1$s seconds', 'dropshipexpress' ),
							$import_rule[ 'timer' ] * $delays_translation[ $import_rule[ 'delay' ] ]
						),
					];
				}
			}

			// Add the intervals used by automatic publish
			$apis = DSE_Import::Get_Sections();

			foreach ( $apis as $key => $api ) {
				// Check if api is enabled
				if ( 'yes' !== DSE_Settings::Get_Setting( $key, 'auto_publish' ) ) {
					continue;
				}
				// Calculate the value of interval
				$publish_interval = (int) DSE_Settings::Get_Setting( $key, 'schedule_every' );
				$publish_unit     = DSE_Settings::Get_Setting( $key, 'schedule_delay' );

				// Add the value to the array if it's valid
				if ( $publish_interval && in_array( $publish_unit, array_keys( $delays_translation ) ) ) {
					$schedules[ "dse_autopublish_{$key}" ] = [
						'interval' => $publish_interval * $delays_translation[ $publish_unit ],
						'display'  => sprintf(
						/* translators: %1$s is replaced with the cronjob timer in seconds */
							esc_html__( 'DropshipExpress: Every %1$s seconds', 'dropshipexpress' ),
							$publish_interval * $delays_translation[ $publish_unit ]
						),
					];
				}
			}

			// Cronjob for ordering API
			$schedules[ 'dse_order_cron' ] = [
				'interval' => apply_filters( 'dse_order_cron_interval', $delays_translation[ 'minute' ] ),
				'display'  => esc_html__( 'DropshipExpress: Cron interval to processed the order API', 'dropshipexpress' ),
			];

			return $schedules;
		}

		/**
		 * Method to register metaboxes used by
		 * this plugin
		 *
		 */
		public static function Register_Metaboxes() {

			/**
			 * Custom order information for woocommerce
			 *
			 */
			add_meta_box(
				'dse-wc-order-details',
				esc_html__( 'DropshipExpress Order Status', 'dropshipexpress' ),
				[ 'DSE_Shipping', 'WC_Add_Order_Data' ],
				'shop_order',
				'side',
				'default'
			);

		}

		/**
		 * Method to register a list of rest routes
		 *
		 */
		public static function Register_Rest_Routes() {

			$rest_endpoints = apply_filters( 'dse_rest_routes', self::Get_Rest_Routes() );

			if ( $rest_endpoints ) {
				foreach ( $rest_endpoints as $endpoint ) {
					register_rest_route( $endpoint[ 'namespace' ], $endpoint[ 'route' ], $endpoint[ 'args' ] );
				}
			}
		}

		/**
		 * Get the rest routes registered by this plugin
		 *
		 * @return array[]
		 */
		public static function Get_Rest_Routes() {

			$buildin_rest_routs = [
				[
					'namespace' => 'dse/v1',
					'route'     => '/order-api/success/',
					'args'      => [
						'methods'             => 'POST',
						'callback'            => [ 'DSE_Shipping', 'Register_Success_Rest_Route' ],
						'permission_callback' => '__return_true',
					],
				],
				[
					'namespace' => 'dse/v1',
					'route'     => '/order-api/failed/',
					'args'      => [
						'methods'             => 'POST',
						'callback'            => [ 'DSE_Shipping', 'Register_Failed_Rest_Route' ],
						'permission_callback' => '__return_true',
					],
				],
				[
					'namespace' => 'dse/v1',
					'route'     => '/order-api/track/',
					'args'      => [
						'methods'             => 'POST',
						'callback'            => [ 'DSE_Shipping', 'Register_Track_Rest_Route' ],
						'permission_callback' => '__return_true',
					],
				],
			];

			return $buildin_rest_routs;

		}

		/**
		 * Register the taxonomies required by the plugin
		 *
		 */
		public static function Register_Taxonomies() {
			// Register the taxonomy for sorting the products' source
			$api_taxomony_args = [
				'label'        => esc_html__( 'Product Source', 'dropshipexpress' ),
				'description'  => esc_html__( 'Internal taxonomy created by the plugin to manage the products. This should not be manually edited.', 'dropshipexpress' ),
				'public'       => FALSE,
				'rewrite'      => FALSE,
				'hierarchical' => FALSE,
				'show_in_rest' => FALSE,
				'query_var'    => FALSE,
			];
			register_taxonomy( 'dse_source', [ 'dse_import', 'dse_published' ], $api_taxomony_args );
		}
	}
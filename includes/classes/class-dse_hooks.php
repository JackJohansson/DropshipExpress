<?php
	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Hooks
	 *
	 * Contains all the hooks used by this
	 * plugin
	 *
	 */
	class DSE_Hooks {
		/**
		 * DSE_Hooks constructor.
		 *
		 * This method will loop through all the registered
		 * filters and connect them to the proper
		 * method.
		 */
		public function __construct() {

			// Register a list of internal hooks and their callbacks
			$hooks = [
				'init'                               => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Register_CPT' ] ],
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Register_Taxonomies' ] ],
				],
				'plugins_loaded'                     => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Settings', 'Load_Text_Domain' ] ],
				],
				'rest_api_init'                      => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Register_Rest_Routes' ] ],
				],
				'admin_enqueue_scripts'              => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Enqueue_Styles' ] ],
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Enqueue_Scripts' ] ],
				],
				'wp_enqueue_scripts'                 => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Enqueue_User_Styles' ] ],
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Enqueue_User_Scripts' ] ],
				],
				'wp_ajax_dse_search_stores'          => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Search_Stores' ] ],
				],
				'wp_ajax_dse_queue_import'           => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Import_Item' ] ],
				],
				'wp_ajax_dse_single_publish'         => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Ajax_Publish_Product' ] ],
				],
				'wp_ajax_dse_clear_log'              => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Clear_Log' ] ],
				],
				'wp_ajax_dse_update_visited_product' => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Product_Viewed' ] ],
				],
				'admin_post_dse_update_activation'   => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Settings', 'Update_Activation' ] ],
				],
				'dse_process_queued_cron'            => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Process_Queued_Products' ] ],
				],
				'before_delete_post'                 => [
					[ 'priority' => 10, 'args' => 2, 'callback' => [ 'DSE_Import', 'Remove_After_Delete' ] ],
				],
				'woocommerce_product_data_panels'    => [
					[ 'priority' => 10, 'args' => 0, 'callback' => [ 'DSE_Register', 'WC_Data_Tab_Content' ] ],
				],
				'woocommerce_process_product_meta'   => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'WC_Save_Metabox_Data' ] ],
				],
				'add_meta_boxes'                     => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Register_Metaboxes' ] ],
				],
				'dse_process_order_cron'             => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Shipping', 'Process_Order' ] ],
				],
				'woocommerce_order_status_completed' => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Shipping', 'Add_Order_Cron' ] ],
				],
				'dse_process_autoimport_cron'        => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Process_Autoimport' ] ],
				],
				'dse_process_autopublish_cron'       => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Process_Autopublish' ] ],
				],
				'admin_post_dse_single_import_rules' => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Settings', 'Remove_Single_Rule' ] ],
				],
				'admin_post_dse_save_import_rules'   => [
					[ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Import', 'Add_Import_Rule' ] ],
				],
			];

			// Allow adding extra hooks
			$hooks = apply_filters( 'dse_registered_hooks', $hooks );

			// Add the hooks
			foreach ( $hooks as $hook_name => $hook_array ) {
				foreach ( $hook_array as $hook ) {
					add_action( $hook_name, [ $hook[ 'callback' ][ 0 ], $hook[ 'callback' ][ 1 ] ], $hook[ 'priority' ], $hook[ 'args' ] );
				}
			}

			// Also register uninstall hook
			self::Uninstall_Hook();
		}

		/**
		 * Method to register plugin's uninstall hook
		 *
		 */
		public static function Uninstall_Hook() {
			register_uninstall_hook( DSE_PLUGIN_FILE, [ 'DSE_Core', 'Uninstall' ] );
		}
	}
<?php

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Core
	 *
	 * Class used to initiate the plugin.
	 *
	 */
	class DSE_Core {

		/**
		 * Method that sets up the plugin's dependencies when
		 * plugin is activated, including the database tables.
		 *
		 */
		public static function Activation() {

			// Do not activate is WooCommerce is not installed
			if ( ! DSE_Core::Check_Extensions() ) {
				wp_die(
					esc_html__( 'DropshipExpress requires the WooCommerce plugin to be activated. Please install and activate WooCommerce by clicking the following link.', 'dropshipexpress' ),
					esc_html__( 'Can not activate plugin', 'dropshipexpress' ),
					[
						'back_link' => TRUE,
						'link_text' => esc_html__( 'Download from WordPress.org' ),
						'link_url'  => 'https://wordpress.org/plugins/woocommerce',
					]
				);
			}

			// Migrator
			DSE_Settings::Upgrade_Helper();

			/**
			 * @var $wp_filesystem \WP_Filesystem_Direct
			 */
			global $wp_filesystem;

			// Add an option for the queue list
			$import_list_option = [
				'aliexpress' => [],
				'amazon'     => [],
				'jingdong'   => [],
				'ebay'       => [],
				'vip'        => [],
				'gearbest'   => [],
				'lowes'      => [],
				'costco'     => [],
				'walmart'    => [],
				'homedepot'  => [],
			];

			$published_list_option = [
				'aliexpress' => [],
				'amazon'     => [],
				'jingdong'   => [],
				'ebay'       => [],
				'vip'        => [],
				'gearbest'   => [],
				'lowes'      => [],
				'costco'     => [],
				'walmart'    => [],
				'homedepot'  => [],
			];

			if ( ! get_option( 'dse_imported_list' ) ) {
				add_option( 'dse_imported_list', $import_list_option );
			}

			if ( ! get_option( 'dse_published_list' ) ) {
				add_option( 'dse_published_list', $published_list_option );
			}

			// Create the required files and folders
			if ( WP_Filesystem() ) {
				// Create the log directory
				if ( ! $wp_filesystem->is_dir( DSE_LOG_DIR ) ) {
					$wp_filesystem->mkdir( DSE_LOG_DIR, 0755 );
				}

				// Create the log file
				if ( ! $wp_filesystem->is_file( DSE_LOG_FILE ) ) {

					$log_file = $wp_filesystem->put_contents( DSE_LOG_FILE, '', 0755 );

					// Check the log file
					if ( ! $log_file ) {
						$error_message = esc_html__( "Can not create the log file. Please check your permission on the uploads directory. Without the log file, your will not be notified of warnings and errors regarding this plugin.", 'dropshipexpress' );
						self::Throw_Admin_Notice( $error_message, 'danger' );
						DSE_Core::Log_Error( $error_message );
					}
				}

				// Create the htaccess file
				if ( ! $wp_filesystem->is_file( trailingslashit( DSE_LOG_DIR ) . '.htaccess' ) ) {
					$htaccess_file = $wp_filesystem->put_contents( trailingslashit( DSE_LOG_DIR ) . '.htaccess', 'deny from all', 0755 );

					// Check the htaccess file
					if ( ! $htaccess_file ) {
						$error_message = esc_html__( 'Can not create the .htaccess file for DropshipExpress logs. Without this file, your logs can be accessed by anyone. Please contact your server\'s administrator before using this plugin.', 'dropshipexpress' );
						self::Throw_Admin_Notice( $error_message );
						DSE_Core::Log_Error( $error_message );
					}
				}
			} else {
				self::Throw_Admin_Notice( esc_html__( 'Can not create the required files. The plugin can\'t write the files to the disk.', 'dropshipexpress' ) );
			}


			// Set default settings on first activation
			$settings_array = [ 'general', 'aliexpress' ];

			foreach ( $settings_array as $setting ) {
				if ( FALSE === get_option( "dse_{$setting}" ) ) {
					DSE_Settings::Set_Defaults( $setting );
				}
			}

		}

		/**
		 * Check for the required extensions
		 *
		 * @return bool
		 */
		public static function Check_Extensions() {
			// This plugin relies on WooCommerce to operate. If
			// WooCommerce is not installed, abort.
			if ( ! in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
				return FALSE;
			}

			return TRUE;
		}

		/**
		 * Throw an admin notice about cURL not being loaded
		 *
		 * @param        $message
		 * @param string $class
		 */
		public static function Throw_Admin_Notice( $message, $class = 'notice notice-info' ) {

			add_action(
				'admin_notices',
				function () use ( $message, $class ) {
					printf(
						'<div class="%1$s"><p>%2$s</p></div>',
						esc_attr( $class ),
						$message
					);
				}
			);
		}

		/**
		 * Method to write to the custom log file
		 *
		 * @param        $error_message
		 * @param string $extra_headers
		 */
		public static function Log_Error( $error_message, $extra_headers = '' ) {

			if ( file_exists( DSE_LOG_FILE ) ) {
				// Log the date
				$date     = date( 'd-M-Y H:i:s' );
				$timezone = date_default_timezone_get();

				error_log( "[{$date} {$timezone}] " . $error_message . "\n", 3, DSE_LOG_FILE, $extra_headers );
			} else {
				error_log( $error_message );
			}
		}

		/**
		 * Initialize the plugin and do the necessary checks
		 *
		 */
		public static function Init() {

			// Register activation hook
			self::Register_Activation_Hook();

			// Check if the required extensions are installed
			if ( ! self::Check_Extensions() ) {

				// Notify the admin
				add_action(
					'admin_notices',
					function () {
						printf(
							'<div class="notice notice-error is-dismissible"><p>%1$s</p></div>',
							esc_html__( 'DropshipExpress requires the WooCommerce plugin to operate. Please install and activate WooCommerce first.', 'dropshipexpress' )
						);
					}
				);

				// Deactivate the plugin, if the WooCommerce is not activated
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( DSE_PLUGIN_FILE );

				return;
			}

			// Load the config file
			self::Load_Config();

			// Import the external classes and required files
			self::Load_Classes();

			// Initiate the settings
			new DSE_Settings();

			// Create the menus
			new DSE_Menu();

			// Register the post types, styles, and so
			new DSE_Register();
		}

		/**
		 * Method to register the plugin's activation hook
		 *
		 */
		public static function Register_Activation_Hook() {
			register_activation_hook( DSE_PLUGIN_FILE, [ 'DSE_Core', 'Activation' ] );
		}

		/**
		 * Load the plugin's configuration file
		 */
		public static function Load_Config() {
			require_once( DSE_PLUGIN_FOLDER . "/includes/deps/config.php" );
		}

		/**
		 * Method to import the classes
		 *
		 */
		private static function Load_Classes() {

			// Load composer's dependencies
			//require_once( DSE_PLUGIN_FOLDER . '/includes/deps/vendor/autoload.php' );

			// Load plugin's core classes
			$classes_suffix = [ 'menu', 'settings', 'import', 'register', 'product', 'filters', 'hooks', 'shipping' ];

			foreach ( $classes_suffix as $class ) {
				require_once( DSE_PLUGIN_FOLDER . "/includes/classes/class-dse_{$class}.php" );
			}
		}

		/**
		 * Method to fully uninstall the plugin
		 *
		 */
		public static function Uninstall() {

			// Delete the plugin's options
			$options_array = [
				'dse_general',
				'dse_aliexpress',
				'dse_orders',
				'dse_import_rules',
				'dse_is_activated',
			];

			foreach ( $options_array as $option ) {

				delete_option( $option );

				// For multisite
				delete_site_option( $option );
			}

			// Delete the default terms
			$terms = get_terms(
				[
					'taxonomy'   => 'dse_source',
					'hide_empty' => FALSE,
				]
			);

			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					wp_delete_term( $term->term_id, 'dse_source' );
				}
			}

		}

	}
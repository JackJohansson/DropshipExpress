<?php
	/**
	 * Plugin Name: DropshipExpress
	 * Plugin URI: https://cydbytes.com
	 * Description: A complete package to import, manage and sell products from online retailers such as Amazon or AliExpress on your WooCommerce store.
	 * Version: 1.0.3
	 * Author: CydBytes
	 * Text Domain: dropshipexpress
	 * Domain Path: /languages/
	 * Tested up to: 5.7.0
	 * WC requires at least: 3.0.0
	 * WC tested up to: 4.7.1
	 * Requires at least : 4.6.0
	 * Requires PHP : 7.2
	 *
	 * @package DropshipExpress
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	// Basic path information
	define( 'DSE_PLUGIN_FOLDER', dirname( __FILE__ ) );
	define( 'DSE_PLUGIN_DIRNAME', basename( dirname( __FILE__ ) ) );
	define( 'DSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'DSE_PLUGIN_FILE', __FILE__ );

	// Include the main plugin class file
	require_once( DSE_PLUGIN_FOLDER . '/includes/classes/class-dse_core.php' );

	// Initialize the plugin
	DSE_Core::Init();

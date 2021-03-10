<?php
	/**
	 * Main configuration file
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	// Plugin's log file
	$wp_uploads = wp_get_upload_dir();
	define( 'DSE_LOG_DIR', trailingslashit( $wp_uploads[ 'basedir' ] ) . 'dse-logs' );
	define( 'DSE_LOG_FILE', trailingslashit( DSE_LOG_DIR ) . 'log.txt' );

	// Plugin's API url
	define( 'DSE_API_URL', 'https://api.cydbytes.com' );
<?php
	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Filters
	 *
	 * Contains all the filters used by this
	 * plugin
	 *
	 */
	class DSE_Filters {
		/**
		 * DSE_Filters constructor.
		 *
		 * This method will loop through all the registered
		 * filters and connect them to the proper
		 * method.
		 */
		public function __construct() {

			// Register a list of internal filters and their callbacks
			$filters = [
				'dse_rest_routes'                 => [ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Get_Rest_Routes' ] ],
				'dse_order_apis'                  => [ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Shipping', 'Supported_Apis' ] ],
				'cron_schedules'                  => [ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'Register_Cron_Interval' ] ],
				'woocommerce_product_data_tabs'   => [ 'priority' => 10, 'args' => 1, 'callback' => [ 'DSE_Register', 'WC_Data_Tabs' ] ],
				'wp_get_attachment_image_src'     => [ 'priority' => 10, 'args' => 4, 'callback' => [ 'DSE_Filters', 'wp_get_attachment_image_src' ] ],
				'wp_get_attachment_thumb_url'     => [ 'priority' => 10, 'args' => 2, 'callback' => [ 'DSE_Filters', 'wp_get_attachment_thumb_url' ] ],
				'wp_get_attachment_thumb_file'    => [ 'priority' => 10, 'args' => 2, 'callback' => [ 'DSE_Filters', 'wp_get_attachment_thumb_file' ] ],
				'image_downsize'                  => [ 'priority' => 10, 'args' => 3, 'callback' => [ 'DSE_Filters', 'image_downsize' ] ],
				'wp_get_attachment_url'           => [ 'priority' => 10, 'args' => 2, 'callback' => [ 'DSE_Filters', 'wp_get_attachment_url' ] ],
				'wp_calculate_image_srcset'       => [ 'priority' => 10, 'args' => 5, 'callback' => [ 'DSE_Filters', 'wp_calculate_image_srcset' ] ],
				'wp_generate_attachment_metadata' => [ 'priority' => 10, 'args' => 3, 'callback' => [ 'DSE_Filters', 'wp_generate_attachment_metadata' ] ],
				'wp_update_attachment_metadata'   => [ 'priority' => 10, 'args' => 2, 'callback' => [ 'DSE_Filters', 'wp_update_attachment_metadata' ] ],

			];

			// Allow users to add/remove filters
			$filters = apply_filters( 'dse_registered_filters', $filters );

			// Add the filters
			foreach ( $filters as $filter_name => $filter ) {
				// If the callback is a method
				if ( is_array( $filter[ 'callback' ] ) ) {
					// Check if the class exists and also the method is registered
					if ( class_exists( $filter[ 'callback' ][ 0 ] ) && method_exists( $filter[ 'callback' ][ 0 ], $filter[ 'callback' ][ 1 ] ) ) {
						add_filter(
							$filter_name,
							[ $filter[ 'callback' ][ 0 ], $filter[ 'callback' ][ 1 ] ],
							$filter[ 'priority' ],
							$filter[ 'args' ]
						);
					}
				} else {
					// If the callback is a function
					if ( function_exists( $filter[ 'callback' ] ) ) {
						add_filter( $filter_name, $filter[ 'callback' ], $filter[ 'priority' ], $filter[ 'args' ] );
					}
				}
			}
		}

		/**
		 * Method to skip downsize image function for
		 * external images
		 *
		 * @param $downsize
		 * @param $attachment_id
		 * @param $size
		 *
		 * @return bool
		 */
		public static function image_downsize( $downsize, $attachment_id, $size ) {
			if ( 'yes' == get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				return FALSE;
			}
			return $downsize;
		}

		/**
		 * Method to remove the srcset for external images
		 *
		 * @param $sources
		 * @param $size_array
		 * @param $image_src
		 * @param $image_meta
		 * @param $attachment_id
		 *
		 * @return array
		 */
		public static function wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
			if ( 'yes' == get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				// External images have no srcset
				return [];
			}
			return $sources;
		}

		/**
		 * Method to prevent generating the metadata of external images
		 *
		 * @param $metadata
		 * @param $attachment_id
		 * @param $context
		 *
		 * @return null
		 */
		public static function wp_generate_attachment_metadata( $metadata, $attachment_id, $context ) {
			if ( 'yes' === get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				return NULL;
			}
			return $metadata;
		}

		/**
		 * Method to replace the src for externally imported images
		 *
		 * @param $image
		 * @param $attachment_id
		 * @param $size
		 * @param $icon
		 *
		 * @return mixed
		 */
		public static function wp_get_attachment_image_src( $image, $attachment_id, $size, $icon ) {

			// Check if the image was added by this plugin
			if ( 'yes' == get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				return [
					0 => get_post_meta( $attachment_id, 'dse_attachment_source', TRUE ),
					1 => NULL,
					2 => NULL,
				];
			}

			return $image;
		}

		/**
		 * Method to filter the thumbnail file path for
		 * external images
		 *
		 * @param $thumbfile
		 * @param $attachment_id
		 *
		 * @return mixed
		 */
		public static function wp_get_attachment_thumb_file( $thumbfile, $attachment_id ) {
			// Check if the image was added by this plugin
			if ( 'yes' == get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				$thumbfile = get_post_meta( $attachment_id, 'dse_attachment_source', TRUE );
			}
			return $thumbfile;
		}

		/**
		 * Method to filter the thumbnail URL for
		 * external images
		 *
		 * @param $url
		 * @param $attachment_id
		 *
		 * @return mixed
		 */
		public static function wp_get_attachment_thumb_url( $url, $attachment_id ) {
			// Check if the image was added by this plugin
			if ( 'yes' == get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				$url = get_post_meta( $attachment_id, 'dse_attachment_source', TRUE );
			}
			return $url;
		}

		/**
		 * Method to return the proper attachment URL for
		 * external images
		 *
		 * @param $url
		 * @param $attachment_id
		 *
		 * @return mixed
		 */
		public static function wp_get_attachment_url( $url, $attachment_id ) {
			if ( 'yes' == get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				$url = get_post_meta( $attachment_id, 'dse_attachment_source', TRUE );
			}
			return $url;
		}

		/**
		 *  Method to prevent updating the metadata of external images
		 *
		 * @param $data
		 * @param $attachment_id
		 *
		 * @return null
		 */
		public static function wp_update_attachment_metadata( $data, $attachment_id ) {
			if ( 'yes' === get_post_meta( $attachment_id, 'dse_image_is_external', TRUE ) ) {
				return NULL;
			}
			return $data;
		}

	}
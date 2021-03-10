<?php

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Api_aliexpress
	 *
	 * Class to interact with AliExpress and
	 * retrieve product data
	 *
	 */
	class DSE_Api_aliexpress {

		/**
		 * Method to add a new import rule for AliExpress
		 *
		 */
		public function Add_Import_Rule() {

			// Don't save if both keyword and category fields are empty
			if ( empty( $_POST[ 'dse_import_rules_keyword_text' ] ) ) {
				$invalid = TRUE;
			}

			if ( ! isset( $_POST[ 'dse_import_rules_delay' ] ) || ! in_array( $_POST[ 'dse_import_rules_delay' ], [ 'minute', 'hour', 'day' ] ) ) {
				$invalid = TRUE;
			}

			if ( ! isset( $invalid ) ) {

				// Get the current import rules
				$import_rules = get_option( 'dse_import_rules', [] );

				if ( (int) $_POST[ 'dse_import_rules_timer' ] < 1 || 60 < (int) $_POST[ 'dse_import_rules_timer' ] ) {
					$_POST[ 'dse_import_rules_timer' ] = 30;
				}

				// Get the category ID and Name
				if ( 'dse_all' !== $_POST[ 'dse_import_rules_cat' ] ) {
					$category = explode( '^', sanitize_text_field( $_POST[ 'dse_import_rules_cat' ] ) );
				} else {
					$category = [ 'all', esc_html__( 'All Categories', 'dropshipexpress' ) ];
				}

				$rule = [
					'search_category' => isset( $_POST[ 'dse_import_rules_type' ] ) ? 'yes' : 'no',
					'keyword_text'    => sanitize_text_field( $_POST[ 'dse_import_rules_keyword_text' ] ),
					'category'        => $category[ 1 ],
					'timer'           => absint( $_POST[ 'dse_import_rules_timer' ] ),
					'delay'           => sanitize_text_field( $_POST[ 'dse_import_rules_delay' ] ),
					'get_data'        => [
						'dse_product_search_orderby'        => '',
						'dse_product_search_cat'            => $category[ 0 ],
						'dse_product_search_delivery_delay' => sanitize_text_field( $_POST[ 'dse_import_rules_delivery' ] ),
						'dse_product_search_destination'    => sanitize_text_field( $_POST[ 'dse_import_rules_destination' ] ),
						'dse_product_search_price_from'     => 0 !== intval( $_POST[ 'dse_import_rules_price_from' ] ) ? intval( $_POST[ 'dse_import_rules_price_from' ] ) : 'all',
						'dse_product_search_price_to'       => 0 !== intval( $_POST[ 'dse_import_rules_price_to' ] ) ? intval( $_POST[ 'dse_import_rules_price_to' ] ) : 'all',
					],
					'api'             => 'aliexpress',
					'date'            => date( get_option( 'date_format' ) ),
					'id'              => uniqid(),

				];
				// Push the rule to the array

				$import_rules[ $rule[ 'id' ] ] = $rule;

				// Update the options
				update_option( 'dse_import_rules', $import_rules, TRUE );
			}
		}

		/**
		 * Method to fetch and return a product
		 *
		 * @param $url_or_id
		 * @param $get
		 *
		 * @return array|\WP_Error
		 */
		public function Download_Product( $url_or_id, $get = [] ) {

			// Fetch the product from source
			$product = $this->Fetch_Product( $url_or_id, $get );

			if ( is_wp_error( $product ) ) {
				return $product;
			}

			$product_obj                  = new stdClass();
			$product_obj->product_id      = $product[ 'product_id' ];
			$product_obj->title           = $product[ 'title' ];
			$product_obj->url             = $product[ 'url' ];
			$product_obj->price           = $product[ 'price' ];
			$product_obj->price_formatted = $product[ 'price_formatted' ];
			$product_obj->thumbnail       = $product[ 'images' ][ 0 ];
			$product_obj->currency        = $product[ 'currency' ];
			$product_obj->discount_value  = $product[ 'discounted_value' ];
			$product_obj->discount_curr   = $product[ 'discount_currency' ];
			$product_obj->discount_perc   = $product[ 'discount' ];
			$product_obj->rating          = $product[ 'rating_percentage' ];
			$product_obj->is_variable     = TRUE;
			$product_obj->page            = 1;

			$result                   = [];
			$result[ 'total' ]        = 1;
			$result[ 'errors' ]       = '';
			$result[ 'products' ]     = [ $product_obj ];
			$result[ 'pages' ]        = 1;
			$result[ 'current_page' ] = 1;
			$result[ 'success' ]      = TRUE;

			return $result;
		}

		/**
		 * Download and parse a single product
		 *
		 * @param       $url_or_id
		 * @param array $get
		 *
		 * @return array|\WP_Error
		 */
		public function Fetch_Product( $url_or_id, $get = [] ) {

			// Construct a URL
			$url = $this->Construct_URL( $url_or_id );

			if ( is_wp_error( $url ) ) {
				return $url;
			}

			// Send a get request to fetch the product
			$remote_get_args = [
				'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
				'headers'    => [
					'referer' => 'https://feedback.aliexpress.com/display/productEvaluation.htm',
				],
			];

			$remote_product = wp_remote_get( $url, $remote_get_args );

			// Check if the response is valid
			if ( is_wp_error( $remote_product ) ) {
				return $remote_product;
			}

			if ( ! is_array( $remote_product ) ) {
				return new WP_Error( 'dse_failed_request', esc_html__( 'Could not fetch product from AliExpress. Please try again later.', 'dropshipexpress' ) );
			}

			// Check if the remote page exists
			if ( 200 !== $remote_product[ 'response' ][ 'code' ] ) {
				return new WP_Error( 'dse_not_found', esc_html__( 'The product ID or URL that you have entered does not seem to point to a valid product. Please double check your input.', 'dropshipexpress' ) );
			}

			$content = $remote_product[ 'body' ];

			// Parse the content and find the data needed
			$parser = new DOMDocument( '1.0', 'UTF-8' );

			// Suppress the html entity checking
			@$parser->loadHTML( $content );

			$script_tags = $parser->getElementsByTagName( 'script' );

			$regex = '~data: (\{.*\})~m';

			$matches = [];

			// Find the product info
			foreach ( $script_tags as $script_tag ) {

				// First, find the tag with the window.runParams
				if ( FALSE !== strpos( $script_tag->textContent, 'window.runParams' ) ) {
					// Now extract the data
					$match_found = preg_match( $regex, $script_tag->textContent, $matches );
					// Stop
					if ( $match_found ) {
						break;
					}
				}
			}

			if ( ! isset( $matches[ 1 ] ) || empty( $matches[ 1 ] ) ) {
				return new WP_Error( 'dse_empty_body', esc_html__( 'Unable to extract the product info. Please try again later.', 'dropshipexpress' ) );
			}

			// We have the product's information
			$product_json = json_decode( $matches[ 1 ] );

			// Fetch the product description
			$description = '';

			if ( 'yes' === DSE_Settings::Get_Setting( 'aliexpress', 'import_content_desc' ) ) {

				$remote_desc = wp_remote_get( $this->Json_Parse( $product_json, 'descriptionModule->descriptionUrl' ), $remote_get_args );

				if ( is_array( $remote_desc ) && ! is_wp_error( $remote_desc ) && 200 === $remote_desc[ 'response' ][ 'code' ] ) {
					$description = $remote_desc[ 'body' ];
				}
			}

			// Construct the product data
			$product            = [];
			$product_attributes = [];
			$product_variations = [];
			$product_reviews    = [];
			$product_tags       = [];

			$product[ 'source' ]             = 'aliexpress';
			$product[ 'product_id' ]         = $this->Json_Parse( $product_json, 'commonModule->productId', 'int', 'intval' );
			$product[ 'title' ]              = $this->Json_Parse( $product_json, 'titleModule->subject', 'string', 'sanitize_text_field' );
			$product[ 'description' ]        = wp_kses_post( $description );
			$product[ 'images' ]             = array_map( 'esc_url_raw', $this->Json_Parse( $product_json, 'imageModule->imagePathList', 'array' ) );
			$product[ 'thumbnails' ]         = array_map( 'esc_url_raw', $this->Json_Parse( $product_json, 'imageModule->summImagePathList', 'array' ) );
			$product[ 'url' ]                = $this->Json_Parse( $product_json, 'storeModule->detailPageUrl', 'string', 'esc_url_raw' );
			$product[ 'review_server' ]      = $this->Json_Parse( $product_json, 'feedbackModule->feedbackServer', 'string', 'esc_url_raw' );
			$product[ 'company_id' ]         = $this->Json_Parse( $product_json, 'feedbackModule->companyId', 'int', 'intval' );
			$product[ 'seller_id' ]          = $this->Json_Parse( $product_json, 'feedbackModule->sellerAdminSeq', 'int', 'intval' );
			$product[ 'is_discount' ]        = $this->Json_Parse( $product_json, 'priceModule->activity', 'bool', 'boolval' );
			$product[ 'discount' ]           = $this->Json_Parse( $product_json, 'priceModule->discount', 'int', 'intval' );
			$product[ 'price' ]              = $this->Json_Parse( $product_json, 'priceModule->maxAmount->value', 'float', 'floatval' );
			$product[ 'price_formatted' ]    = $this->Json_Parse( $product_json, 'priceModule->maxAmount->formatedAmount', 'string', 'sanitize_text_field' );
			$product[ 'price_currency' ]     = $this->Json_Parse( $product_json, 'priceModule->maxAmount->currency', 'string', 'sanitize_text_field' );
			$product[ 'discounted_value' ]   = $this->Json_Parse( $product_json, 'priceModule->maxActivityAmount->value', 'float', 'floatval' );
			$product[ 'discount_formatted' ] = $this->Json_Parse( $product_json, 'priceModule->maxActivityAmount->formatedAmount', 'string', 'sanitize_text_field' );
			$product[ 'discount_currency' ]  = $this->Json_Parse( $product_json, 'priceModule->maxActivityAmount->currency', 'string', 'sanitize_text_field' );
			$product[ 'quantity' ]           = $this->Json_Parse( $product_json, 'quantityModule->totalAvailQuantity', 'int', 'intval' );
			$product[ 'currency' ]           = $this->Json_Parse( $product_json, 'commonModule->currencyCode', 'string', 'sanitize_text_field' );
			$product[ 'currency_shipping' ]  = $this->Json_Parse( $product_json, 'shippingModule->currencyCode', 'string', 'sanitize_text_field' );
			$product[ 'category_id' ]        = $this->Json_Parse( $product_json, 'commonModule->categoryId', 'int', 'intval' );
			$product[ 'product_url' ]        = $this->Json_Parse( $product_json, 'storeModule->detailPageUrl', 'string', 'esc_url_raw' );
			$product[ 'rating' ]             = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->averageStar', 'float', 'floatval' );
			$product[ 'rating_percentage' ]  = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->averageStarRage', 'float', 'floatval' );
			$product[ 'rating_1' ]           = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->oneStarNum', 'int', 'intval' );
			$product[ 'rating_2' ]           = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->twoStarNum', 'int', 'intval' );
			$product[ 'rating_3' ]           = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->threeStarNum', 'int', 'intval' );
			$product[ 'rating_4' ]           = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->fourStarNum', 'int', 'intval' );
			$product[ 'rating_5' ]           = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->fiveStarNum', 'int', 'intval' );
			$product[ 'rating_total' ]       = $this->Json_Parse( $product_json, 'titleModule->feedbackRating->totalValidNum', 'int', 'intval' );

			// Product categories
			$product_categories = [];

			foreach ( $this->Json_Parse( $product_json, 'crossLinkModule->breadCrumbPathList' ) as $item ) {
				if ( isset( $item->name ) && ! in_array( $item->name, [ 'Home', 'All Categories' ] ) ) {
					$product_categories[] = sanitize_text_field( $item->name );
				}
			}

			$product[ 'categories' ] = $product_categories;

			// Product specifications
			$product_specs = [];

			foreach ( $this->Json_Parse( $product_json, 'specsModule->props', 'array' ) as $spec ) {
				$product_specs[] = [
					'name'         => sanitize_text_field( $this->Json_Parse( $spec, 'attrName' ) ),
					'value'        => sanitize_text_field( $this->Json_Parse( $spec, 'attrValue' ) ),
					'attribute_id' => sanitize_text_field( $this->Json_Parse( $spec, 'attrNameId' ) ),
					'value_id'     => sanitize_text_field( $this->Json_Parse( $spec, 'attrValueId' ) ),
				];
			}

			$product[ 'specs' ] = $product_specs;

			// Product attributes
			foreach ( $this->Json_Parse( $product_json, 'skuModule->productSKUPropertyList', 'array' ) as $attribute ) {

				$attribute_values = [];

				if ( $this->Json_Parse( $attribute, 'skuPropertyValues' ) ) {
					foreach ( $this->Json_Parse( $attribute, 'skuPropertyValues' ) as $value ) {
						$attribute_values[ $this->Json_Parse( $value, 'propertyValueId', 'int', 'intval' ) ] = [
							'id'        => $this->Json_Parse( $value, 'propertyValueId', 'int', 'intval' ),
							'name'      => preg_replace( '/\s\s+/', ' ', $this->Json_Parse( $value, 'propertyValueDisplayName', 'string', 'sanitize_text_field' ) ),
							'order'     => $this->Json_Parse( $value, 'skuPropertyValueShowOrder', 'int', 'intval' ),
							'color'     => $this->Json_Parse( $value, 'skuColorValue', 'bool', 'sanitize_hex_color' ),
							'image'     => $this->Json_Parse( $value, 'skuPropertyImagePath', 'bool', 'esc_url_raw' ),
							'thumbnail' => $this->Json_Parse( $value, 'skuPropertyImageSummPath', 'bool', 'esc_url_raw' ),
						];
					}
				}

				$product_attributes[ $this->Json_Parse( $attribute, 'skuPropertyId', 'int', 'intval' ) ] = [
					'type'   => $this->Json_Parse( $attribute, 'isShowTypeColor', 'bool' ) ? 'color' : 'list',
					'order'  => $this->Json_Parse( $attribute, 'order', 'int', 'intval' ),
					'name'   => preg_replace( '/\s\s+/', ' ', $this->Json_Parse( $attribute, 'skuPropertyName', 'string', 'sanitize_text_field' ) ),
					'id'     => $this->Json_Parse( $attribute, 'skuPropertyId', 'int', 'intval' ),
					'values' => $attribute_values,
				];
			}

			$product[ 'attributes' ] = $product_attributes;

			// Only import variations if there's more than 1
			$variations_array = $this->Json_Parse( $product_json, 'skuModule->skuPriceList', 'array' );

			if ( count( $variations_array ) > 1 ) {
				// Product variations
				foreach ( $variations_array as $key => $variation ) {

					$combinations = [];

					$variation_attributes = explode( ';', $this->Json_Parse( $variation, 'skuAttr' ) );

					foreach ( $variation_attributes as $variation_attribute ) {
						$variation_combination = explode( ':', $variation_attribute );

						if ( is_array( $variation_combination ) ) {
							$combinations[ sanitize_text_field( $variation_combination[ 0 ] ) ] = strstr( $variation_combination[ 1 ], '#', TRUE ) ?: sanitize_text_field( $variation_combination[ 1 ] );
						}
					}

					// If the variation has SKU, use it as index key
					$variation_sku = $this->Json_Parse( $variation, 'skuId', 'int', 'intval' );
					$variation_key = 0 === $variation_sku ? $key : $variation_sku;

					$product_variations[ $variation_key ] = [
						'combination'        => $combinations,
						'sku'                => $variation_sku,
						'price'              => $this->Json_Parse( $variation, 'skuVal->skuAmount->value', 'float', 'floatval' ),
						'price_currency'     => $this->Json_Parse( $variation, 'skuVal->skuAmount->currency', 'string', 'sanitize_text_field' ),
						'price_formatted'    => $this->Json_Parse( $variation, 'skuVal->skuAmount->formatedAmount', 'string', 'sanitize_text_field' ),
						'quantity'           => $this->Json_Parse( $variation, 'skuVal->availQuantity', 'int', 'intval' ),
						'is_discount'        => $this->Json_Parse( $variation, 'skuVal->isActivity', 'bool', 'boolval' ),
						'discount_currency'  => $this->Json_Parse( $variation, 'skuVal->skuActivityAmount->currency', 'string', 'sanitize_text_field' ),
						'discount_formatted' => $this->Json_Parse( $variation, 'skuVal->skuActivityAmount->formatedAmount', 'string', 'sanitize_text_field' ),
						'discounted_value'   => $this->Json_Parse( $variation, 'skuVal->skuActivityAmount->value', 'float', 'floatval' ),
					];

				}
			} else {
				$product[ 'sku' ] = $this->Json_Parse( $product_json, 'skuModule->skuPriceList->skuId', 'int', 'intval' );
			}

			$product[ 'variations' ] = $product_variations;

			// Product reviews

			if ( 'yes' === DSE_Settings::Get_Setting( 'aliexpress', 'import_reviews' ) ) {

				$reviews_count        = (int) DSE_Settings::Get_Setting( 'aliexpress', 'review_import_count' );
				$reviews_translate    = DSE_Settings::Get_Setting( 'aliexpress', 'import_review_translate' );
				$import_review_images = DSE_Settings::Get_Setting( 'aliexpress', 'import_review_images' ) === 'yes';

				// Request more reviews until the limit is met
				$more_reviews        = TRUE;
				$review_page         = 1;
				$review_current_page = 1;
				$loaded_reviews      = 0;
				$temp_review_dom     = new DOMDocument( '1.0', 'UTF-8' );

				// Fetch a list of review, and if it was not enough, do it again
				if ( $product[ 'rating_total' ] > 0 ) {

					do {

						// Set the required headers and parameters
						$remote_review_args = [
							'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
							'headers'    => [
								'referer' => 'https://feedback.aliexpress.com/display/productEvaluation.htm',
							],
							'body'       => [
								'ownerMemberId'          => $product[ 'seller_id' ],
								'memberType'             => 'seller',
								'productId'              => $product[ 'product_id' ],
								'companyId'              => $product[ 'company_id' ],
								'evaStarFilterValue'     => 'all+Stars',
								'evaSortValue'           => 'sortdefault@feedback',
								'page'                   => $review_page,
								'currentPage'            => $review_current_page,
								'i18n'                   => TRUE,
								'withPictures'           => FALSE,
								'withPersonalInfo'       => FALSE,
								'withAdditionalFeedback' => FALSE,
								'onlyFromMyCountry'      => FALSE,
								'isOpened'               => TRUE,
								'translate'              => $reviews_translate,
								'v'                      => 2,
							],
						];

						// Fetch a list of reviews
						$remote_reviews = wp_remote_post( 'https:' . $product[ 'review_server' ] . '/display/productEvaluation.htm', $remote_review_args );

						// To prevent another nested conditional
						if ( is_wp_error( $remote_reviews ) || 200 !== $remote_reviews[ 'response' ][ 'code' ] ) {
							break;
						}

						if ( ! is_wp_error( $remote_reviews ) && ! empty( $remote_reviews[ 'body' ] ) && 200 === $remote_reviews[ 'response' ][ 'code' ] ) {

							$review_parser = new DOMDocument( '1.0', 'UTF-8' );

							// Convert the encoding
							$review_html = mb_convert_encoding( $remote_reviews[ 'body' ], 'HTML-ENTITIES', 'UTF-8' );

							$review_parser->LoadHTML(
								$review_html,
								LIBXML_NOWARNING | LIBXML_NOERROR
							);

							$review_query = new DOMXPath( $review_parser );

							// Query a list of reviews by class name
							$reviews_per_page = $review_query->query( "//div[contains(@class, 'feedback-item clearfix')]" );

							// Check if there's any review left at the store
							if ( $reviews_per_page->length > 0 ) {

								// Save the review HTML for processing
								foreach ( $reviews_per_page as $key => $review_node ) {
									// Only import if the limit is not reached
									if ( $loaded_reviews <= $reviews_count ) {
										// Raise a flag
										$loaded_reviews++;

										// Parse each review and extract information

										$temp_review_dom->loadHTML( $review_parser->saveHTML( $review_node ), LIBXML_NOWARNING | LIBXML_NOERROR );
										$single_review = new DOMXPath( $temp_review_dom );

										$review_user    = $single_review->query( "//span[contains(@class, 'user-name')]" );
										$review_photos  = $single_review->query( "//dd[contains(@class, 'r-photo-list')]" );
										$review_date    = $single_review->query( "//span[contains(@class, 'r-time-new')]" );
										$review_content = $single_review->query( "//dt[contains(@class, 'buyer-feedback')]" );
										$review_rating  = $single_review->query( "//span[contains(@class, 'star-view')]" );

										$rating_html_node = $review_rating[ 0 ]->ownerDocument;
										$rating_html_frag = $rating_html_node->createDocumentFragment();

										foreach ( $review_rating[ 0 ]->childNodes as $child ) {
											$rating_html_frag->appendChild( $child->cloneNode( TRUE ) );
										}

										// Calculate the rating
										$rating_matches = [];
										preg_match( '/(?<=style="width:)[\d]+(?=%")/', $rating_html_node->saveXML( $rating_html_frag ), $rating_matches );
										if ( isset( $rating_matches[ 0 ] ) && ! empty( $rating_matches[ 0 ] ) && (float) $rating_matches[ 0 ] !== 0 ) {
											$review_rating = (int) ceil( (float) $rating_matches[ 0 ] / 20 );
										} else {
											$review_rating = 3;
										}

										// Push the review to the array
										$product_reviews[] = [
											'key'      => $loaded_reviews,
											'username' => sanitize_text_field( trim( $review_user[ 0 ]->nodeValue ) ),
											'images'   => ( $import_review_images && 0 < count( $review_photos ) ) ? wp_kses_post( $temp_review_dom->saveHTML( $review_photos[ 0 ] ) ) : '',
											'content'  => wp_kses_post( trim( str_replace( $review_date[ 0 ]->nodeValue, '', $review_content[ 0 ]->nodeValue ) ) ),
											'date'     => sanitize_text_field( trim( $review_date[ 0 ]->nodeValue ) ),
											'rating'   => $review_rating,
										];
									}
								}

								// Check if there's still need for more reviews
								if ( $loaded_reviews <= $reviews_count ) {
									$review_page++;
									$review_current_page++;
								} else {
									// No more reviews. Stop the loop
									break;
								}
							} else {
								// No more reviews. Stop the loop
								break;
							}

						}

					} while ( $more_reviews );

				}

			}

			$product[ 'reviews' ] = $product_reviews;

			/**
			 * Import the product's tags
			 *
			 */
			if ( 'yes' === DSE_Settings::Get_Setting( 'aliexpress', 'import_content_tags' ) ) {
				if ( $tags = $this->Json_Parse( $product_json, 'pageModule->keywords' ) ) {
					$product_tags = explode( ',', $tags );
					// Sanitize the tags, and remove empty ones
					$product_tags = array_map( 'trim', $product_tags );
					$product_tags = array_map( 'sanitize_text_field', array_filter( $product_tags ) );
				}
			}
			$product[ 'tags' ] = $product_tags;

			return $product;

		}

		/**
		 * Function to construct a product URL based by user input
		 *
		 * @param $input
		 *
		 * @return string|\WP_Error
		 */
		public function Construct_URL( $input ) {

			// Check if the input is a product URL
			if ( 0 === (int) $input ) {

				$url = $this->URL_To_ID( $input );

				// If URL is valid
				if ( ! is_wp_error( $url ) ) {
					return $url[ 'url' ];
				} else {
					return $url;
				}
			} else {
				// The input is a valid product ID
				return ( "https://www.aliexpress.com/item/" . (int) $input . ".html" );
			}

		}

		/**
		 * Method to parse a json string
		 *
		 * @param object        $object            The Json object
		 * @param string        $path              The path to the value
		 * @param string        $type              Type of the value
		 * @param callable|null $function_to_apply Function to apply on the value before returning
		 *
		 * @return string
		 */
		public function Json_Parse( $object, string $path, $type = 'string', callable $function_to_apply = NULL ) {

			// Set the empty value
			switch ( $type ) {
				case 'float':
				case 'int':
					{
						$empty_val = 0;
						break;
					}
				case 'bool':
					{
						$empty_val = FALSE;
						break;
					}
				case 'array':
					{
						$empty_val = [];
						break;
					}
				case 'string':
				default:
					{
						$empty_val = '';
						break;
					}
			}

			// If entry is not a json object
			if ( ! is_object( $object ) || empty( $path ) ) {
				return $empty_val;
			}

			$path_array = explode( '->', $path );

			// Access the nested object
			foreach ( $path_array as $value ) {
				if ( isset( $object->{$value} ) ) {
					$object = $object->{$value};
				} else return $empty_val;
			}

			// Apply the functions
			if ( NULL !== $function_to_apply && function_exists( $function_to_apply ) ) {
				return $function_to_apply( $object );
			}

			return $object;

		}

		/**
		 * Method to parse a URL and return the product ID
		 *
		 * @param $url
		 *
		 * @return array|\WP_Error
		 */
		public function URL_To_ID( $url ) {

			// Check if the value is already a product id
			if ( 0 !== intval( $url ) ) {
				return [ 'url' => $this->Construct_URL( $url ), 'product_id' => $url ];
			}

			if ( FALSE === strpos( $url, 'https:' ) ) {
				$url = 'https:' . $url;
			}

			if ( FALSE === filter_var( $url, FILTER_VALIDATE_URL ) ) {
				return new WP_Error( 'dse_invalid_url', esc_html__( 'The entered URL is not valid. Please check the URL and try again.', 'dropshipexpress' ) );
			}

			// Reconstruct the URL to remove unnecessary parts
			$scheme = parse_url( $url, PHP_URL_SCHEME );
			$host   = parse_url( $url, PHP_URL_HOST );
			$path   = parse_url( $url, PHP_URL_PATH );

			// Check if the URL belongs to aliexpress
			if ( FALSE === strpos( $host, 'aliexpress.com' ) ) {
				return new WP_Error( 'dse_invalid_domain', esc_html__( 'The entered URL does not belong to AliExpress. The URL must be pointing to aliexpress.com or its subdomains.', 'dropshipexpress' ) );
			}

			$scheme = $scheme ? $scheme . '://' : '';

			$sanitized_url = $scheme . $host . $path;

			// Check if the url is valid
			$regex = "~^(https?://)?(www\.)?(^[a-zA-Z]{2,3}\.)?aliexpress\.com/item/([0-9]+)\.html~i";

			$matches = [];

			$valid = preg_match( $regex, $sanitized_url, $matches );

			if ( $valid && isset( $matches[ 4 ] ) ) {
				return [ 'url' => $sanitized_url, 'product_id' => $matches[ 4 ] ];
			} else {
				return new WP_Error( 'dse_incomplete_url', esc_html__( 'The provided URL does not appear to be a valid product URL. The general form for a product URL is: https://aliexpress.com/item/1234567890.html', 'dropshipexpress' ) );
			}
		}

		/**
		 * Returns an array of category ids alongside their
		 * names
		 *
		 * @return mixed
		 */
		public static function Get_Category_Indexes() {
			return json_decode( include( DSE_PLUGIN_FOLDER . '/templates/admin/import-rules/aliexpress/category-index.php' ), TRUE );
		}

		/**
		 * Method to return a list of supported currencies
		 *
		 *
		 * @return string[]
		 */
		public static function Get_Currency_Indexes() {
			return [ 'AUD', 'BRL', 'CAD', 'EUR', 'GBP', 'IDR', 'INR', 'JPY', 'KRW', 'MXN', 'RUB', 'SEK', 'TRY', 'UAH', 'USD' ];
		}

		/**
		 * Add a downloaded product to the import list to be
		 * imported later.
		 *
		 * @param $url_or_id
		 *
		 * @return array|int|\WP_Error
		 */
		public function Import_Product( $url_or_id ) {

			$product_id = $this->URL_To_ID( $url_or_id );

			// Check if the product is already imported or published
			if ( ! is_wp_error( $product_id ) ) {
				if ( DSE_Import::Is_Imported( $product_id, 'aliexpress' ) ) {
					return new WP_Error( 'dse_already_imported', esc_html__( 'The requested product has already been imported.', 'dropshipexpress' ) );
				} elseif ( DSE_Import::Is_Published( $product_id, 'aliexpress' ) ) {
					return new WP_Error( 'dse_already_published', esc_html__( 'The requested product has already been published on your store.', 'dropshipexpress' ) );
				}
			} else {
				return $product_id;
			}

			// Fetch the product from the source store
			$product = $this->Fetch_Product( $url_or_id );

			if ( is_wp_error( $product ) ) {
				return $product;
			}

			$product = new DSE_Product( $product );

			// Insert a new post into database to be published later
			$product_data = [
				'post_title'   => $product->get_title(),
				'post_content' => $product->get_desc(),
				'post_status'  => 'draft',
				'post_type'    => 'dse_imported',
				'meta_input'   => [
					'dse_source'             => 'aliexpress',
					'dse_title'              => $product->get_title(),
					'dse_description'        => $product->get_desc(),
					'dse_product_id'         => $product->get_id(),
					'dse_review_server'      => $product->get_review_link(),
					'dse_company_id'         => $product->get_company_id(),
					'dse_seller_id'          => $product->get_seller_id(),
					'dse_discount'           => $product->get_discount(),
					'dse_is_discounted'      => $product->on_sale(),
					'dse_price'              => $product->get_price(),
					'dse_price_formatted'    => $product->get_price_formatted(),
					'dse_price_currency'     => $product->get_price_currency(),
					'dse_discounted_value'   => $product->get_discounted_value(),
					'dse_discount_formatted' => $product->get_discount_formatted(),
					'dse_discount_currency'  => $product->get_discount_currency(),
					'dse_quantity'           => $product->get_quantity(),
					'dse_currency'           => $product->get_currency(),
					'dse_currency_shipping'  => $product->get_currency_shipping(),
					'dse_category_id'        => $product->get_category_id(),
					'dse_product_url'        => $product->get_url(),
					'dse_rating'             => $product->get_rating(),
					'dse_rating_percentage'  => $product->get_rating_percentage(),
					'dse_rating_1'           => $product->get_rating_n( 1 ),
					'dse_rating_2'           => $product->get_rating_n( 2 ),
					'dse_rating_3'           => $product->get_rating_n( 3 ),
					'dse_rating_4'           => $product->get_rating_n( 4 ),
					'dse_rating_5'           => $product->get_rating_n( 5 ),
					'dse_is_scheduled'       => 'no',
				],
			];

			$imported_id = wp_insert_post( $product_data, TRUE );

			if ( is_wp_error( $imported_id ) ) {
				return $imported_id;
			}

			// Add product data to post metadata
			$array_metadata_keys = [
				'specs', 'images', 'thumbnails', 'attributes', 'variations', 'tags', 'categories',
			];

			foreach ( $array_metadata_keys as $metadata_key ) {
				add_post_meta( $imported_id, "dse_product_{$metadata_key}", call_user_func( [ $product, "get_{$metadata_key}" ] ) );
			}

			// Set the product's source
			wp_set_post_terms( $imported_id, 'aliexpress', 'dse_source', FALSE );

			// Store the reviews as metadata
			if ( is_array( $product->get_reviews() ) ) {
				foreach ( $product->get_reviews() as $review ) {
					add_post_meta( $imported_id, 'dse_product_reviews', $review, FALSE );
				}
			}

			// Add the product to the import list
			$imported_list = get_option( 'dse_imported_list', [] );

			$imported_list[ 'aliexpress' ][] = $product_id[ 'product_id' ];

			update_option( 'dse_imported_list', $imported_list );

			return $imported_id;
		}

		/**
		 * Method to publish a product on the website
		 *
		 * @param $product_id
		 *
		 * @return \WP_Error|int
		 */
		public function Publish_Product( $product_id ) {

			// Get the product
			$product = get_post( $product_id );

			if ( $product ) {

				// Create an instance of the DSE_Product class
				$local_product = self::Post_To_Product( $product->ID );

				// Get the configurations
				$images_setting         = DSE_Settings::Get_Setting( 'aliexpress', 'import_product_images' );
				$desc_images_setting    = DSE_Settings::Get_Setting( 'aliexpress', 'import_desc_images' );
				$check_duplicate_images = DSE_Settings::Get_Setting( 'aliexpress', 'check_duplicate_images' );
				$product_type_setting   = DSE_Settings::Get_Setting( 'aliexpress', 'default_product_type' );
				$search_replace_setting = DSE_Settings::Get_Setting( 'aliexpress', 'enable_replacements' );
				$import_reviews_setting = DSE_Settings::Get_Setting( 'aliexpress', 'import_reviews' );
				$review_limit_setting   = DSE_Settings::Get_Setting( 'aliexpress', 'review_import_count' );
				$dynamic_cat_enabled    = DSE_Settings::Get_Setting( 'aliexpress', 'dynamic_cat' );

				// Check if anything should be synced
				$sync_options = [];

				$sync_keys = [
					'publish_sync_title',
					'publish_sync_images',
					'publish_sync_desc',
					'publish_sync_price',
					'publish_sync_stock',
					'publish_sync_reviews',
					'publish_sync_variations',
				];

				foreach ( $sync_keys as $sync_key ) {
					$sync_options[ $sync_key ] = DSE_Settings::Get_Setting( 'aliexpress', $sync_key );
				}

				$enabled_syncs = array_keys( $sync_options, 'yes' );

				// Fetch an updated version of remote product
				$product_url     = get_post_meta( $product_id, 'dse_product_url', TRUE );
				$fetched_product = $this->Fetch_Product( $product_url );

				if ( is_wp_error( $fetched_product ) ) {
					return $fetched_product;
				}

				$synced_product = new DSE_Product( $fetched_product );

				// We have some enabled syncs, reload the product from source
				if ( ! empty( $enabled_syncs ) ) {

					// Update the title
					if ( isset( $sync_options[ 'publish_sync_title' ] ) ) {
						$local_product->Set_Value( 'title', $synced_product->get_title() );
					}

					// Update the images
					if ( isset( $sync_options[ 'publish_sync_images' ] ) ) {
						$local_product->Set_Value( 'images', $synced_product->get_images() );
					}

					// Update the description
					if ( isset( $sync_options[ 'publish_sync_desc' ] ) ) {
						$local_product->Set_Value( 'description', $synced_product->get_desc() );
					}

					// Update the price
					if ( isset( $sync_options[ 'publish_sync_price' ] ) ) {
						$local_product->Set_Value( 'price', $synced_product->get_price() );
						$local_product->Set_Value( 'price_formatted', $synced_product->get_price_formatted() );
						$local_product->Set_Value( 'discounted_value', $synced_product->get_discounted_value() );
						$local_product->Set_Value( 'discount_formatted', $synced_product->get_discount_formatted() );
					}

					// Update the stock value
					if ( isset( $sync_options[ 'publish_sync_stock' ] ) ) {
						$local_product->Set_Value( 'quantity', $synced_product->get_quantity() );
					}

					// Update the reviews
					if ( isset( $sync_options[ 'publish_sync_reviews' ] ) ) {
						$local_product->Set_Value( 'reviews', $synced_product->get_reviews() );
					}

					// Update the variations
					if ( isset( $sync_options[ 'publish_sync_variations' ] ) ) {
						$local_product->Set_Value( 'variations', $synced_product->get_variations() );

					}
				}

				// List of product's images
				$product_images = $local_product->get_images();

				// Handle the description images
				switch ( $desc_images_setting ) {
					case 'download':
						{
							// Get a list of images used in the product's description
							$desc_dom  = new DOMDocument();
							$desc_html = "<!DOCTYPE html><html lang='en'><head><title></title></head><body>" . $local_product->get_desc() . "</body></html>";
							$desc_dom->loadHTML( $desc_html );
							$desc_images = $desc_dom->getElementsByTagName( 'img' );

							// Store the image URLs inside an array
							if ( $desc_images ) {
								foreach ( $desc_images as $desc_image ) {
									// Download the image
									$downloaded_desc_image = DSE_Import::Download_Image( $desc_image->getAttribute( 'src' ), $check_duplicate_images );
									if ( $downloaded_desc_image && ! is_wp_error( $downloaded_desc_image ) ) {
										// Update the description
										$local_product->Set_Value(
											'description',
											str_replace(
												$desc_image->getAttribute( 'src' ),
												wp_get_attachment_image_url( $downloaded_desc_image, 'full' ),
												$local_product->get_desc()
											)
										);
										// Keep the image ids to remove later if publishing the product fails
										$downloaded_desc_img_ids[] = $downloaded_desc_image;
									}
								}
							}
							break;
						}
					case 'drop':
						{
							// Strip all the images
							$local_product->Set_Value(
								'description',
								preg_replace( "/<img[^>]+\>/i", "", $local_product->get_desc() )
							);
							break;
						}
				}

				// Run search and replace
				if ( 'yes' === $search_replace_setting ) {
					$search_replace_rules = DSE_Settings::Get_Setting( 'aliexpress', 'replace_rule' );
					if ( ! empty( $search_replace_rules ) ) {
						foreach ( $search_replace_rules as $rule ) {
							// Run on title
							if ( 'yes' === $rule[ 'apply_title' ] ) {
								$local_product->Set_Value( 'title', str_replace( $rule[ 'search' ], $rule[ 'value' ], $local_product->get_title() ) );
							}
							// Run on description
							if ( 'yes' === $rule[ 'apply_desc' ] ) {
								$local_product->Set_Value( 'description', str_replace( $rule[ 'search' ], $rule[ 'value' ], $local_product->get_desc() ) );
							}
							// Run on attributes
							if ( 'yes' === $rule[ 'apply_attr' ] ) {
								$specifications_array = $local_product->get_specs();
								foreach ( $specifications_array as $key => $value ) {
									$specifications_array[ $key ][ 'value' ] = str_replace( $rule[ 'search' ], $rule[ 'value' ], $value[ 'value' ] );
								}
								$local_product->Set_Value( 'specs', $specifications_array );
							}
							// Run on tags
							if ( 'yes' === $rule[ 'apply_tags' ] ) {
								$tags_array = $local_product->get_tags();
								if ( is_array( $tags_array ) ) {
									foreach ( $tags_array as $key => $tag ) {
										$tags_array[ $key ] = str_replace( $rule[ 'search' ], $rule[ 'value' ], $tag );
									}
								}
								$local_product->Set_Value( 'tags', $tags_array );
							}
							// Run on reviews
							if ( 'yes' === $rule[ 'apply_reviews' ] ) {
								$reviews_array = $local_product->get_reviews();
								foreach ( $reviews_array as $key => $review ) {
									$reviews_array[ $key ] = str_replace( $rule[ 'search' ], $rule[ 'value' ], $review[ 'content' ] );
								}
								$local_product->Set_Value( 'reviews', $reviews_array );
							}
						}
					}
				}

				// Update the regular and discounted price
				$local_product->Set_Value( 'price', DSE_Import::Calculate_Price( $local_product->get_price(), 'aliexpress' ) );
				$local_product->Set_Value( 'discounted_value', DSE_Import::Calculate_Price( $local_product->get_discounted_value(), 'source' ) );

				// Update the price for the variations
				$local_variations = $variations_for_price = $local_product->get_variations();

				// Update the variations' prices
				if ( ! empty( $variations_for_price ) ) {
					foreach ( $variations_for_price as $key => $value ) {
						$variations_for_price[ $key ][ 'price' ] = DSE_Import::Calculate_Price( $value[ 'price' ], 'aliexpress' );

						// If product has discount
						if ( FALSE !== $value[ 'is_discount' ] ) {
							$variations_for_price[ $key ][ 'discounted_value' ] = DSE_Import::Calculate_Price( $value[ 'discounted_value' ], 'aliexpress' );
						}
					}
					$local_product->Set_Value( 'variations', $variations_for_price );
				}

				// Create a new WooCommerce product, based on the product's type
				if ( 'external' !== $product_type_setting ) {

					// Select the proper product type
					if ( empty( $local_product->get_variations() ) ) {
						// Create a new WooCommerce product
						$wc_product = new WC_Product();
						$wc_product->set_manage_stock( TRUE );

						// Stock status
						if ( $local_product->get_quantity() > 0 ) {
							$wc_product->set_stock_status( 'instock' );
							$wc_product->set_stock_quantity( $local_product->get_quantity() );
						}
					} else {
						$wc_product = new WC_Product_Variable();

						// Disable stock management for variable products
						$wc_product->set_manage_stock( FALSE );
						$wc_product->set_default_attributes( [] );
						$wc_product->set_stock_quantity( '' );
					}

					// Set the product's data
					if ( $local_product->on_sale() ) {
						$wc_product->set_price( $local_product->get_discounted_value() );
						$wc_product->set_sale_price( $local_product->get_discounted_value() );
					}

				} else {
					$wc_product = new WC_Product_External();
					$wc_product->set_product_url( $local_product->get_url() );
					$wc_product->set_button_text( esc_html__( 'Buy on AliExpress', 'dropshipexpress' ) );
				}

				// Set the product's data
				try {
					$wc_product->set_name( $local_product->get_title() );
					$wc_product->set_status( 'publish' );
					$wc_product->set_catalog_visibility( 'visible' );
					$wc_product->set_description( $local_product->get_desc() );
					//$wc_product->set_price( $local_product->get_price() );
					$wc_product->set_regular_price( $local_product->get_price() );
					$wc_product->set_backorders( 'no' );
					$wc_product->set_sold_individually( FALSE );
					$wc_product->set_virtual( FALSE );

					// Set empty values
					$wc_product->set_cross_sell_ids( [] );
					$wc_product->set_upsell_ids( [] );
					$wc_product->set_date_on_sale_from( NULL );
					$wc_product->set_date_on_sale_to( NULL );
					$wc_product->set_purchase_note( '' );
					$wc_product->set_download_expiry( 0 );
					$wc_product->set_download_limit( 0 );
					$wc_product->set_downloadable( 0 );
					$wc_product->set_downloads( [] );
					$wc_product->set_height( '' );
					$wc_product->set_width( '' );
					$wc_product->set_length( '' );
					$wc_product->set_weight( '' );
					$wc_product->set_low_stock_amount( '' );

					// If it's a simple product
					if ( ! empty( $local_product->get_sku() ) ) {
						$wc_product->set_sku( $local_product->get_sku() );
					}

					// If it's on sale
					if ( $local_product->on_sale() ) {
						$wc_product->set_price( $local_product->get_discount() );
						$wc_product->set_sale_price( $local_product->get_discount() );
					}
				} catch ( Exception $error ) {
					return new WP_Error( 'dse_data_exception', $error->getMessage() );
				}

				// Set the images
				if ( 'drop' !== $images_setting && ! empty( $product_images ) ) {
					// Set the featured image
					$featured_image = DSE_Import::Download_Image( $product_images[ 0 ], $check_duplicate_images, $images_setting );

					if ( ! is_wp_error( $featured_image ) ) {
						$wc_product->set_image_id( $featured_image );
					}

					// Set the gallery if there's more than 1 image available
					if ( count( $product_images ) > 1 ) {
						foreach ( $product_images as $product_image ) {
							$gallery_item = DSE_Import::Download_Image( $product_image, $check_duplicate_images, $images_setting );
							if ( ! is_wp_error( $gallery_item ) ) {
								$product_gallery[] = $gallery_item;
							}
						}
						if ( isset( $product_gallery ) ) {
							$wc_product->set_gallery_image_ids( $product_gallery );
						}
					}
				}

				// Add the product's attributes, if enabled
				if ( ! empty( $local_product->get_specs() ) ) {

					$product_attributes = [];

					foreach ( $local_product->get_specs() as $attribute ) {
						// Create an attribute
						$product_attribute = new WC_Product_Attribute();
						$product_attribute->set_id( 0 );
						$product_attribute->set_options( [ $attribute[ 'value' ] ] );
						$product_attribute->set_name( $attribute[ 'name' ] );
						$product_attribute->set_position( 0 );
						$product_attribute->set_variation( 0 );
						$product_attribute->set_visible( 1 );

						// Add the attribute to the array
						$product_attributes[] = $product_attribute;
					}

					if ( ! empty( $product_attributes ) ) {
						// Set the attributes
						$wc_product->set_attributes( $product_attributes );
					}
				}

				// Set the tags
				if ( 'yes' === DSE_Settings::Get_Setting( 'aliexpress', 'import_content_tags' ) ) {
					// Insert the tags
					if ( is_array( $local_product->get_tags() ) ) {
						$tag_ids = [];
						foreach ( $local_product->get_tags() as $product_tag ) {
							// Check if the tag exists
							if ( $existing_term = term_exists( $product_tag, 'product_tag' ) ) {
								$tag_ids[] = $existing_term[ 'term_id' ];
							} else {
								// Insert a new tag
								$tag_id = wp_insert_term( $product_tag, 'product_tag' );
								if ( ! is_wp_error( $tag_id ) ) {
									$tag_ids[] = $tag_id[ 'term_id' ];
								}
							}
						}
						$wc_product->set_tag_ids( $tag_ids );
					}
				}

				// Try to save the product
				$wc_product_saved = $wc_product->save();

				if ( 0 === $wc_product_saved ) {
					return new WP_Error(
						'dse_publish_fail',
						sprintf(
							wp_kses(
							/* translators: %1$s is replaced with "product's URL" */
								__( 'An error has occurred while trying to save the product. You can visit the original product page <a href="%1$s" target="_blank">here.</a>', 'dropshipexpress' ),
								[
									'a' => [
										'href'   => [],
										'target' => [],
									],
								]
							),
							esc_url( $local_product->get_url() )
						)
					);
				}

				// Handle the product's categories
				if ( 'yes' === $dynamic_cat_enabled && is_array( $category_tree = $local_product->get_categories() ) ) {

					// Create a hierarchy of categories
					if ( is_array( $category_tree ) ) {

						$parent         = 0;
						$categories_ids = [];

						foreach ( $category_tree as $key => $category ) {
							// Check if the category exists. if not, create it
							if ( NULL !== $existing_cat = term_exists( $category, 'product_cat', $parent ) ) {
								$parent = $categories_ids[] = $existing_cat[ 'term_id' ];
							} else {
								$new_category = wp_insert_term( $category, 'product_cat', [ 'parent' => $parent ] );

								if ( ! is_wp_error( $new_category ) ) {
									// Update the parent category
									$parent = $categories_ids[] = $new_category[ 'term_id' ];
								}
							}
						}

						// Set the category ID
						$wc_product->set_category_ids( $categories_ids );
					}
				} else {
					$wc_product->set_category_ids( [ $local_product->get_local_category_id() ] );
				}

				// Add product's variations
				if ( ! empty( $local_variations ) && 'external' !== $product_type_setting ) {

					// Get the attributes associated with the variations
					$variations_attributes_array = $local_product->get_attributes();
					$wc_product_attributes       = $wc_product->get_attributes();

					// Import the attributes to be later used as variation
					foreach ( $variations_attributes_array as $variation_attribute ) {

						$single_variation_attribute = new WC_Product_Attribute();
						$single_variation_attribute->set_id( 0 );
						$single_variation_attribute->set_name( $variation_attribute[ 'name' ] );
						$single_variation_attribute->set_options( array_column( $variation_attribute[ 'values' ], 'name' ) );
						$single_variation_attribute->set_visible( 1 );
						$single_variation_attribute->set_variation( 1 );

						// Push the variations to array
						array_push( $wc_product_attributes, $single_variation_attribute );
					}

					// Set the attributes and save the product
					$wc_product->set_attributes( $wc_product_attributes );
					$wc_product_attributes_added = $wc_product->save();

					// If there's a problem, abort
					if ( 0 === $wc_product_attributes_added ) {
						//wp_delete_post( $wc_product_saved, TRUE );
						return new WP_Error(
							'dse_failed_attributes',
							sprintf(
								wp_kses(
								/* translators: %1$s is replaced with product's URL */
									__( 'Failed to import attributes for the product. You can check the associated product <a href="%1$s" target="_blank">here.</a>' ),
									[
										'a' => [
											'href'   => [],
											'target' => [],
										],
									]
								),
								esc_url( $local_product->get_url() )
							)
						);
					}

					// Refresh the product attributes
					$wc_product_attributes = $wc_product->get_attributes();

					foreach ( $local_variations as $sku_as_key => $variation ) {
						try {
							$variation_attributes = [];

							$wc_variation = new WC_Product_Variation();
							//$wc_variation->set_price( $variation[ 'price' ] );
							$wc_variation->set_regular_price( $variation[ 'price' ] );
							$wc_variation->set_parent_id( $wc_product_saved );
							$wc_variation->set_backorders( 'no' );
							$wc_variation->set_sold_individually( 'no' );
							$wc_variation->set_virtual( 'no' );
							$wc_variation->set_manage_stock( TRUE );
							$wc_variation->set_stock_quantity( $variation[ 'quantity' ] );

							// Set the unavailable values
							$wc_variation->set_download_limit( 0 );
							$wc_variation->set_download_expiry( 0 );
							$wc_variation->set_downloadable( FALSE );
							$wc_variation->set_downloads( [] );
							$wc_variation->set_low_stock_amount( '' );
							$wc_variation->set_date_on_sale_from( NULL );
							$wc_variation->set_date_on_sale_to( NULL );
							$wc_variation->set_upsell_ids( [] );
							$wc_variation->set_cross_sell_ids( [] );
							$wc_variation->set_width( '' );
							$wc_variation->set_height( '' );
							$wc_variation->set_length( '' );
							$wc_variation->set_width( '' );
							$wc_variation->set_gallery_image_ids( [] );
							$wc_variation->set_rating_counts( [] );
							$wc_variation->set_default_attributes( [] );
							$wc_variation->set_attributes( [] );
							$wc_variation->set_purchase_note( '' );
							$wc_variation->set_review_count( 0 );
							$wc_variation->set_average_rating( 0 );
							$wc_variation->set_rating_counts( [] );
							$wc_variation->set_description( '' );
							$wc_variation->set_short_description( '' );

							//$wc_variation->set_purchase_note( );
							$wc_variation->set_stock_status( $variation[ 'quantity' ] > 0 ? 'instock' : 'outofstock' );

							if ( FALSE !== $variation[ 'is_discount' ] ) {
								$wc_variation->set_sale_price( $variation[ 'discounted_value' ] );
							}

							if ( ! empty( $variation[ 'sku' ] ) ) {
								$wc_variation->set_sku( $variation[ 'sku' ] );
							}

							// Set the combination
							foreach ( $variation[ 'combination' ] as $combination_key => $combination ) {
								$variation_attributes[ sanitize_title( $variations_attributes_array[ $combination_key ][ 'name' ] ) ] = $variations_attributes_array[ $combination_key ][ 'values' ][ $combination ][ 'name' ];
							}

							$wc_variation->set_attributes( $variation_attributes );

							// Try to find a thumbnail
							foreach ( $variation[ 'combination' ] as $key => $value ) {
								if ( isset( $variations_attributes_array[ $key ][ 'values' ][ $value ][ 'image' ] ) ) {
									if ( 'drop' !== $images_setting ) {
										// Strip the unnecessary characters from the thumbnail
										$variation_image = preg_replace( '/(?<=\.jpg|png|jpeg|webp)_[0-9]+x[0-9]+\.jpg|png|jpeg|webp$/', '', $variations_attributes_array[ $key ][ 'values' ][ $value ][ 'image' ] );
										$variation_image = DSE_Import::Download_Image( $variation_image, $check_duplicate_images, $images_setting );
										// Set the variation image
										if ( ! is_wp_error( $variation_image ) ) {
											$wc_variation->set_image_id( $variation_image );
										}
									}
									break;
								}
							}

							// If something goes wrong, abort and delete the product
							if ( 0 === ( $variation_id = $wc_variation->save() ) ) {
								//wp_delete_post( $wc_product_saved, TRUE );
								return new WP_Error(
									'dse_variation_failed',
									sprintf(
										wp_kses(
										/* translators: %1$s is replaced with product's URL */
											__( 'An error has occurred while trying to publish the variations of the product. You can visit the original product page <a href="%1$s" target="_blank">here.</a>', 'dropshipexpress' ),
											[
												'a' => [
													'href'   => [],
													'target' => [],
												],
											]
										),
										esc_url( $local_product->get_url() )
									)
								);
							}

							// Store the variation id to the variation array
							$local_variations[ $sku_as_key ][ 'variation_id' ] = $variation_id;

						} catch ( Exception $error ) {
							// If something goes wrong, delete the product
							//wp_delete_post( $wc_product_saved, TRUE );
							return new WP_Error( 'dse_variation_exception', $error->getMessage() );
						}
					}
				}

				// Add the reviews
				if ( 'yes' === $import_reviews_setting ) {

					$imported_reviews = 0;
					$rating_sum       = 0;
					$rating_counts    = [];
					$review_ids       = [];

					foreach ( $local_product->get_reviews() as $review ) {
						// Don't import excessive reviews
						if ( $imported_reviews > $review_limit_setting ) {
							break;
						}
						// Add a single review
						$single_review_id = DSE_Import::Import_Review( $review, $wc_product_saved );

						$rating_sum      += $review[ 'rating' ];
						$rating_counts[] = $review[ 'rating' ];

						$review_ids[] = $single_review_id;
						$imported_reviews++;
					}

					if ( $imported_reviews > 0 ) {

						// Store the comment ids for later
						add_post_meta( $wc_product_saved, 'dse_review_ids', $review_ids, TRUE );

						// Store the ratings
						$wc_product->set_average_rating( $rating_sum / $imported_reviews );
						$wc_product->set_rating_counts( $rating_counts );
						$wc_product->set_review_count( $imported_reviews );

						// Save the product
						$wc_product->save();
					}
				}

				// Add the extra metadata
				add_post_meta( $wc_product_saved, 'dse_product', 'yes', TRUE );
				add_post_meta( $wc_product_saved, 'dse_source', 'aliexpress', TRUE );
				add_post_meta( $wc_product_saved, 'dse_product_id', $local_product->get_id(), TRUE );
				add_post_meta( $wc_product_saved, 'dse_product_url', $local_product->get_url(), TRUE );
				add_post_meta( $wc_product_saved, 'dse_title', $local_product->get_title(), TRUE );
				add_post_meta( $wc_product_saved, 'dse_sku', $local_product->get_sku(), TRUE );
				add_post_meta( $wc_product_saved, 'dse_seller_id', $local_product->get_seller_id(), TRUE );
				add_post_meta( $wc_product_saved, 'dse_company_id', $local_product->get_company_id(), TRUE );
				add_post_meta( $wc_product_saved, 'dse_price', $local_product->get_price(), TRUE );
				add_post_meta( $wc_product_saved, 'dse_discounted_value', $local_product->get_discounted_value(), TRUE );

				// Only for variable products
				if ( $wc_product->is_type( 'variable' ) ) {
					add_post_meta( $wc_product_saved, 'dse_attributes', $local_product->get_attributes(), TRUE );
					add_post_meta( $wc_product_saved, 'dse_variations', $local_variations, TRUE );
				}

				// Delete the product from imported list
				$this->Remove_After_Publish( $product_id );

				// Add the product to the list of published product
				$published_products = get_option( 'dse_published_list', [] );

				if ( ! array_search( $local_product->get_id(), $published_products[ 'aliexpress' ] ) ) {
					$published_products[ 'aliexpress' ][] = $local_product->get_id();
					update_option( 'dse_published_list', $published_products );
				}

				// Return the product ID
				return $wc_product_saved;

			} else {
				return new WP_Error( 'dse_publish_fail', esc_html__( 'Can not publish post. The provided post ID is not valid.', 'dropshipexpress' ) );
			}

		}

		/**
		 * Method to convert a WordPress post to an instance
		 * of the DSE_Product object
		 *
		 * @param        $product_id
		 *
		 * @param string $source
		 *
		 * @return \DSE_Product|\WP_Error
		 */
		public static function Post_To_Product( $product_id, $source = 'aliexpress' ) {

			// Get the imported product
			$product = get_post( $product_id );

			if ( ! $product ) {
				return new WP_Error( 'dse_wrong_product_id', esc_html__( 'Can not get the product from the database. It might have already been removed.', 'dropshipexpress' ) );
			}

			$product_data = [];

			// Array of metadata used to store the product's information
			$meta_keys = [
				'source'             => 'dse_source',
				'title'              => 'dse_title',
				'description'        => 'dse_description',
				'product_id'         => 'dse_product_id',
				'review_server'      => 'dse_review_server',
				'company_id'         => 'dse_company_id',
				'seller_id'          => 'dse_seller_id',
				'discount'           => 'dse_discount',
				'currency'           => 'dse_currency',
				'currency_shipping'  => 'dse_currency_shipping',
				'price'              => 'dse_price',
				'price_formatted'    => 'dse_price_formatted',
				'price_currency'     => 'dse_price_currency',
				'discounted_value'   => 'dse_discounted_value',
				'discount_formatted' => 'dse_discount_formatted',
				'discount_currency'  => 'dse_discount_currency',
				'quantity'           => 'dse_quantity',
				'category_id'        => 'dse_category_id',
				'product_url'        => 'dse_product_url',
				'rating'             => 'dse_rating',
				'rating_percentage'  => 'dse_rating_percentage',
				'rating_1'           => 'dse_rating_1',
				'rating_2'           => 'dse_rating_2',
				'rating_3'           => 'dse_rating_3',
				'rating_4'           => 'dse_rating_4',
				'rating_5'           => 'dse_rating_5',
				'rating_total'       => 'dse_rating_total',
				'specs'              => 'dse_product_specs',
				'images'             => 'dse_product_images',
				'thumbnails'         => 'dse_product_thumbnails',
				'attributes'         => 'dse_product_attributes',
				'variations'         => 'dse_product_variations',
				'tags'               => 'dse_product_tags',
				'categories'         => 'dse_product_categories',
				'is_scheduled'       => 'dse_is_scheduled',
			];

			foreach ( $meta_keys as $class_key => $db_key ) {
				$product_data[ $class_key ] = get_post_meta( $product_id, $db_key, TRUE );
			}

			// Reviews are an array
			$product_data[ 'reviews' ] = get_post_meta( $product_id, 'dse_product_reviews', FALSE );

			return new DSE_Product( $product_data );
		}

		/**
		 * Remove a single imported product from the imported
		 * items after it's published on the website
		 *
		 * @param $post_id
		 *
		 * @return bool|\WP_Error
		 */
		public function Remove_After_Publish( $post_id ) {

			$post_id = intval( $post_id );

			$published_post = get_post( $post_id );

			$product_id = get_post_meta( $published_post->ID, 'dse_product_id', TRUE );

			if ( NULL === $published_post || empty( $product_id ) ) {
				return new WP_Error( 'dse_invalid_postid', esc_html__( 'Can not remove the item. The requested item ID is not valid.', 'dropshipexpress' ) );
			}

			$delete_result = wp_delete_post( $post_id, TRUE );

			if ( ! $delete_result ) {
				return new WP_Error( 'dse_failed_dequeue', esc_html__( 'An error occurred while trying to remove the item. Please try again later.', 'dropshipexpress' ) );
			}

			// Remove the item from the import list
			$published                 = get_option( 'dse_imported_list' );
			$published[ 'aliexpress' ] = array_diff( $published[ 'aliexpress' ], [ $product_id ] );
			update_option( 'dse_imported_list', $published );

			return TRUE;
		}

		/**
		 * Method to save the user changes to a drafted imported
		 * product and queue it to be published by cronjob
		 *
		 * @return \WP_Error|boolean
		 */
		public function Queue_Import() {

			$product_id = (int) $_POST[ 'dse_single_import_id' ];

			// Get the imported product to make sure it's not already expired
			$product = get_post( $product_id );

			if ( ! $product ) {
				return new WP_Error( 'dse_invalid_product', esc_html__( 'The requested product can not be found. It might have been removed or already published.', 'dropshipexpress' ) );
			}

			// Retrieve the data and build the product
			$product_title      = isset( $_POST[ 'dse_imported_product_title' ] ) ? sanitize_text_field( $_POST[ 'dse_imported_product_title' ] ) : $product->post_title;
			$product_desc       = isset( $_POST[ 'dse_single_imported_description_' . $product_id ] ) ? wp_kses_post( $_POST[ 'dse_single_imported_description_' . $product_id ] ) : '';
			$product_cat        = isset( $_POST[ 'dse_single_imported_category' ] ) && 'dse_default' !== $_POST[ 'dse_single_imported_category' ] ? (int) $_POST[ 'dse_single_imported_category' ] : (int) get_option( 'default_product_cat' );
			$product_images     = [];
			$product_variations = [];

			// Form an array of images and remove the deleted images
			if ( isset( $_POST[ 'dse_single_imported_img' ] ) ) {
				foreach ( $_POST[ 'dse_single_imported_img' ] as $image ) {
					if ( $image != '' ) {
						$product_images[] = esc_url_raw( $image );
					}
				}
			}

			$original_images = get_post_meta( $product_id, 'dse_product_images', TRUE );
			$product_images  = array_intersect( $original_images, $product_images );

			// Update the variations
			$original_variations = get_post_meta( $product_id, 'dse_product_variations', TRUE );

			if ( isset( $_POST[ 'dse_single_imported_variation' ] ) ) {
				// Store the updated variations into an array
				foreach ( $_POST[ 'dse_single_imported_variation' ] as $key => $variation ) {
					$product_variations[ sanitize_text_field( $variation[ 'key' ] ) ] = [
						'price'          => sanitize_text_field( $variation[ 'price' ] ),
						'price_discount' => sanitize_text_field( $variation[ 'discounted_price' ] ),
						'quantity'       => sanitize_text_field( $variation[ 'quantity' ] ),
					];
				}
				// Compare these with the original variations and update them
				foreach ( $original_variations as $key => $variation ) {
					if ( isset( $product_variations[ $key ] ) ) {
						// Update a single variation
						$original_variations[ $key ][ 'price' ]            = $product_variations[ $key ][ 'price' ];
						$original_variations[ $key ][ 'discounted_value' ] = $product_variations[ $key ][ 'price_discount' ];
						$original_variations[ $key ][ 'quantity' ]         = $product_variations[ $key ][ 'quantity' ];
					} else {
						// Remove variation if deleted
						unset( $original_variations[ $key ] );
					}
				}
			} else {
				// If all the variations are deleted
				$original_variations = [];
			}


			// Remove the deleted reviews
			if ( isset( $_POST[ 'dse_imported_product_reviews' ] ) ) {
				$original_reviews = get_post_meta( $product_id, 'dse_product_reviews', TRUE );
				foreach ( $original_reviews as $key => $review ) {
					if ( ! in_array( $review[ 'key' ], $_POST[ 'dse_imported_product_reviews' ] ) ) {
						unset( $original_reviews[ $key ] );
					}
				}
			} else {
				$original_reviews = [];
			}

			// Update the post. Change the post type to private to be processed by cronjob
			$results = wp_update_post(
				[
					'ID'           => $product_id,
					'post_title'   => $product_title,
					'post_content' => $product_desc,
					'post_status'  => 'private',
				]
			);

			if ( ! is_wp_error( $results ) ) {
				// Update the metadata
				update_post_meta( $product_id, 'dse_title', $product_title );
				update_post_meta( $product_id, 'dse_description', $product_desc );
				update_post_meta( $product_id, 'dse_imported_product_reviews', $original_reviews );
				update_post_meta( $product_id, 'dse_product_variations', $original_variations );
				update_post_meta( $product_id, 'dse_product_images', $product_images );
				update_post_meta( $product_id, 'dse_product_category', $product_cat );
			}

			return $results;
		}

		/**
		 * Method to dequeue a product from the import queue
		 *
		 * @param $post_id
		 *
		 * @return bool|\WP_Error
		 */
		public function Remove_Queued_Item( $post_id ) {

			$post_id = intval( $post_id );

			$imported_post = get_post( $post_id );

			$product_id = get_post_meta( $imported_post->ID, 'dse_product_id', TRUE );

			if ( NULL === $imported_post || empty( $product_id ) ) {
				return new WP_Error( 'dse_invalid_postid', esc_html__( 'Can not remove the item. The requested item ID is not valid.', 'dropshipexpress' ) );
			}

			$delete_result = wp_delete_post( $post_id, TRUE );

			// Remove the item from the import list
			if ( $delete_result ) {

				$imported_list                 = get_option( 'dse_imported_list' );
				$imported_list[ 'aliexpress' ] = array_diff( $imported_list[ 'aliexpress' ], [ $product_id ] );

				update_option( 'dse_imported_list', $imported_list );

			} else {
				return new WP_Error( 'dse_failed_dequeue', esc_html__( 'An error occurred while trying to remove the item. Please try again later.', 'dropshipexpress' ) );
			}

			return TRUE;
		}

		/**
		 * Method to search the store based
		 * on user input
		 *
		 * @param $input
		 * @param $get
		 *
		 * @return array|WP_Error
		 */
		public function Search( $input, $get = [] ) {

			// Default response
			$results = [
				'errors'       => '',
				'success'      => FALSE,
				'current_page' => 0,
				'total'        => 0,
				'pages'        => 0,
				'products'     => [],
			];

			if ( empty( $input ) ) {
				$results[ 'errors' ] = new WP_Error( 'dse_invalid_search_param', esc_html__( 'Invalid search parameters. Please try again.', 'dropshipexpress' ) );
				return $results;
			}

			// Base query args
			$query_args = [];
			$server     = DSE_API_URL . '/restful/dse/v1/products';

			// Api args
			$query_args[ 'action' ] = 'search';
			$query_args[ 'token' ]  = get_option( 'dse_api_token', '' );
			$query_args[ 'store' ]  = 'aliexpress';

			// Mandatory fields
			$query_args[ 'keyword' ] = urlencode( $input[ 'keyword' ] );
			$query_args[ 'orderby' ] = $get[ 'dse_product_search_orderby' ];

			// Category Id
			if ( 'all' !== $get[ 'dse_product_search_cat' ] ) {
				$query_args[ 'category_id' ] = urlencode( $get[ 'dse_product_search_cat' ] );
			}

			// Delivery delay
			if ( 3 != $get[ 'dse_product_search_delivery_delay' ] ) {
				$query_args[ 'delivery_delay' ] = intval( $get[ 'dse_product_search_delivery_delay' ] );
			}

			// Destination
			if ( 'all' !== $get[ 'dse_product_search_destination' ] ) {
				$query_args[ 'destination' ] = $get[ 'dse_product_search_destination' ];
			}

			// Price values
			if ( 0.0 !== floatval( $get[ 'dse_product_search_price_from' ] ) ) {
				$query_args[ 'price_from' ] = floatval( $get[ 'dse_product_search_price_from' ] ) * 100;
			}

			if ( 0.0 !== floatval( $get[ 'dse_product_search_price_to' ] ) ) {
				$query_args[ 'price_to' ] = floatval( $get[ 'dse_product_search_price_to' ] ) * 100;
			}

			// Page number
			$query_args[ 'page' ] = absint( $input[ 'page' ] );

			// Set the locale
			$locale = DSE_Settings::Get_Setting( 'aliexpress', 'language' );

			if ( 'auto' === $locale ) {
				$query_args[ 'locale' ] = get_locale();
			} else {
				$query_args[ 'locale' ] = $locale;
			}

			// Set the currency
			$currency = DSE_Settings::Get_Setting( 'aliexpress', 'currency' );

			if ( 'auto' === $currency ) {
				$query_args[ 'currency' ] = get_woocommerce_currency();
			} else {
				$query_args[ 'currency' ] = $currency;
			}

			// Use the official API if enabled
			if ( 'yes' === DSE_Settings::Get_Setting( 'aliexpress', 'official_api' ) ) {

				// Query the store
				$response = $this->Search_Official( $query_args );

				// Check for errors
				if ( ! is_wp_error( $response ) ) {
					return $response;
				} else {
					$results[ 'errors' ] = $response;
					return $results;
				}
			}

			// Build the query
			$url = add_query_arg( $query_args, $server );

			// Sent the request
			$remote_query = wp_remote_get( $url, [ 'timeout' => 30 ] );

			// Check if the response is valid
			if ( is_wp_error( $remote_query ) ) {
				$results[ 'errors' ] = $remote_query;
				return $results;
			}

			if ( ! is_array( $remote_query ) ) {
				$results[ 'errors' ] = new WP_Error( 'dse_failed_request', esc_html__( 'Could not fetch product from AliExpress. Please try again later.', 'dropshipexpress' ) );
				return $results;
			}

			// Check if the remote page exists
			if ( 200 !== $remote_query[ 'response' ][ 'code' ] ) {
				/* translators: %1$d is replaced with the error's code */
				$results[ 'errors' ] = new WP_Error( 'dse_not_found', sprintf( esc_html__( 'The server has responded with a %1$d error code. Please try again.', 'dropshipexpress' ), $remote_query[ 'response' ][ 'code' ] ) );
				return $results;
			}

			$remote_content = wp_remote_retrieve_body( $remote_query );

			$remote_decoded = json_decode( $remote_content );

			if ( NULL === $remote_decoded ) {
				$results[ 'errors' ] = new WP_Error( 'dse_remote_error', esc_html__( 'Can not decode the results. Please try again later.', 'dropshipexpress' ) );
				return $results;
			}

			// If no status is returned
			if ( ! isset( $remote_decoded->status ) ) {
				$results[ 'errors' ] = new WP_Error( 'dse_api_error', esc_html__( 'Api has responded with a malformed reply. Please try again later.', 'dropshipexpress' ) );
				return $results;
			}

			// If the status isn't 200
			if ( 200 !== $remote_decoded->status ) {
				$results[ 'errors' ] = new WP_Error( 'dse_remote_error', $remote_decoded->message );
				return $results;
			}

			// If the response is valid but there's no data
			if (
				! isset( $remote_decoded->results ) ||
				! isset( $remote_decoded->current_page ) ||
				! isset( $remote_decoded->total )
			) {
				$results[ 'errors' ] = new WP_Error( 'dse_remote_error', esc_html__( 'The API did respond, but the data was not valid. Please try again later.', 'dropshipexpress' ) );
				return $results;
			}

			$results[ 'products' ]     = $remote_decoded->results;
			$results[ 'current_page' ] = $remote_decoded->current_page;
			$results[ 'total' ]        = $remote_decoded->total;
			$results[ 'success' ]      = TRUE;
			$results[ 'pages' ]        = 60;

			return $results;

		}

		/**
		 * Method to use the official SDK to search the
		 * store
		 *
		 * @param $query
		 *
		 * @return array|\WP_Error
		 */
		public static function Search_Official( $query ) {

			// Get the API credentials
			$api_key    = DSE_Settings::Get_Setting( 'aliexpress', 'api_key' );
			$api_secret = DSE_Settings::Get_Setting( 'aliexpress', 'api_secret' );

			if ( empty( $api_key ) || empty( $api_secret ) ) {
				return new WP_Error( 'dse_empty_api_credentials', esc_html__( 'API key or secret is blank. Please review the settings.', 'dropshipexpress' ) );
			}

			// Load the SDK
			if ( file_exists( DSE_PRO_FOLDER . '/assets/vendors/sdk/aliexpress/TopSdk.php' ) ) {
				require_once( DSE_PRO_FOLDER . '/assets/vendors/sdk/aliexpress/TopSdk.php' );
			} else {
				return new WP_Error( 'dse_missing_file', esc_html__( 'One of the plugin\'s files are missing. Please reinstall the plugin.', 'dropshipexpress' ) );
			}

			$client = new TopClient;

			$client->appkey    = $api_key;
			$client->secretKey = $api_secret;
			$client->format    = 'json';

			$request = new AliexpressAffiliateProductQueryRequest;

			$request->setAppSignature( 'DSE_User_Call' );
			$request->setPlatformProductType( "ALL" );
			$request->setPageSize( 40 );

			// Keyword
			$request->setKeywords( $query[ 'keyword' ] );

			// Category
			if ( isset( $query[ 'category_id' ] ) ) {
				$request->setCategoryIds( $query[ 'category_id' ] );
			}

			// Delivery delay
			if ( isset( $query[ 'delivery_delay' ] ) ) {
				$request->setDeliveryDays( $query[ 'delivery_delay' ] );
			}

			// Destination
			if ( isset( $query[ 'destination' ] ) ) {
				$request->setShipToCountry( $query[ 'destination' ] );
			}

			// Price from
			if ( isset( $query[ 'price_from' ] ) ) {
				$request->setMinSalePrice( $query[ 'price_from' ] );
			}

			// Price to
			if ( isset( $query[ 'price_to' ] ) ) {
				$request->setMaxSalePrice( $query[ 'price_to' ] );
			}

			// Page
			$request->setPageNo( $query[ 'page' ] );

			// Currency
			$request->setTargetCurrency( $query[ 'currency' ] );

			// Generate the proper locale
			if ( 2 < strlen( $query[ 'locale' ] ) ) {
				$query[ 'locale' ] = strtoupper( substr( $query[ 'locale' ], 0, 2 ) );
			}

			$request->setTargetLanguage( $query[ 'locale' ] );

			// Fetch the results
			$response = $client->execute( $request );

			if ( NULL === $response ) {
				return new WP_Error( 'dse_null_response', esc_html__( 'Could not query AliExpress at this moment. Please try again later.', 'dropshipexpress' ) );
			}

			if ( empty( $response ) ) {
				return new WP_Error( 'dse_empty_response', esc_html__( 'Received an empty response from AliExpress. Please try again later.', 'dropshipexpress' ) );
			}

			// If there's a response
			if ( ! isset( $response->resp_result ) ) {
				if ( isset( $response->msg ) ) {
					return new WP_Error( 'dse_remote_error', $response->msg );
				} else {
					return new WP_Error( 'dse_remote_error', esc_html__( 'AliExpress has sent an invalid response. Please check your API credentials and try again.', 'dropshipexpress' ) );
				}
			}

			// If there's an error
			if ( isset( $response->resp_result->resp_code ) && 200 !== $response->resp_result->resp_code ) {
				return new WP_Error( 'dse_remote_error', $response->resp_result->resp_msg );
			}

			// Return the list of found products
			if ( isset( $response->resp_result->result->products->product ) && is_array( $response->resp_result->result->products->product ) ) {
				foreach ( $response->resp_result->result->products->product as $product ) {
					// Create a product object
					$single_product = new stdClass();

					$single_product->product_id     = $product->product_id;
					$single_product->title          = $product->product_title;
					$single_product->url            = esc_url_raw( $product->product_detail_url );
					$single_product->price          = $product->target_original_price;
					$single_product->currency       = $product->target_original_price_currency;
					$single_product->discount_value = isset( $product->target_sale_price ) ? $product->target_sale_price : 0;
					$single_product->discount_curr  = isset( $product->target_sale_price_currency ) ? $product->target_sale_price_currency : '';
					$single_product->discount_perc  = isset( $product->discount ) ? floatval( strtok( $product->discount, '%' ) ) : 0;
					$single_product->thumbnail      = isset( $product->product_main_image_url ) ? $product->product_main_image_url : '';
					$single_product->rating         = isset( $product->evaluate_rate ) ? floatval( strtok( $product->evaluate_rate, '%' ) ) : '50';
					$single_product->is_variable    = TRUE;

					$results[ 'products' ][] = $single_product;
				}

				$results[ 'errors' ]       = '';
				$results[ 'total' ]        = $response->resp_result->result->total_record_count;
				$results[ 'current_page' ] = $response->resp_result->result->current_page_no;
				$results[ 'success' ]      = TRUE;
				$results[ 'pages' ]        = 60;

				return $results;
			}

			return new WP_Error( 'dse_unknown_error', esc_html__( 'The call to AliExpress was successful, but an unknown error happened. Please try again later.', 'dropshipexpress' ) );

		}

		/**
		 * Method to translate the locale codes into iso codes
		 *
		 * @param $locale
		 *
		 * @return string
		 */
		public static function Translate_Locale( $locale ) {
			$map = [
				'AR' => 'ar_AE',
				'DE' => 'de_DE',
				'EN' => 'en_US',
				'ES' => 'es_ES',
				'CL' => 'es_CL',
				'MX' => 'es_MX',
				'HE' => 'he_IL',
				'IW' => 'iw_IL',
				'FR' => 'fr_FR',
				'ID' => 'in_ID',
				'IT' => 'it_IT',
				'JA' => 'ja_JP',
				'KO' => 'ko_KR',
				'NL' => 'nl_NL',
				'PL' => 'pl_PL',
				'PT' => 'pt_PT',
				'RU' => 'ru_RU',
				'TH' => 'th_TH',
				'TR' => 'tr_TR',
				'VI' => 'vi_VN',
			];

			if ( array_key_exists( $locale, $map ) ) {
				return $map[ $locale ];
			}

			return $locale;
		}

	}
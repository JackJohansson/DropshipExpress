<?php

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Class DSE_Product
	 * Create an object of a product to be accessed
	 * by other classes
	 */
	class DSE_Product {

		/**
		 * An array of product attributes that are
		 * used by variations
		 *
		 * @array $attributes
		 */
		private $attributes;

		/**
		 * Array holding the original categories of the
		 * product
		 *
		 * @array $categories
		 */
		private $categories;

		/**
		 * Product's category ID
		 *
		 * @var $category_id
		 */
		private $category_id;

		/**
		 * Product's company ID
		 *
		 * @var $company_id
		 */
		private $company_id;

		/**
		 * Currency of prices
		 *
		 * @var $currency
		 */
		private $currency;

		/**
		 * Currency for shipping's price
		 *
		 * @var $currency_shipping
		 */
		private $currency_shipping;

		/**
		 * Product's description
		 *
		 * @var $description
		 */
		private $description;

		/**
		 * Main discount's value in percentage
		 *
		 * @var $discount
		 */
		private $discount;

		/**
		 * Main discount's currency
		 *
		 * @var $discount_currency
		 */
		private $discount_currency;

		/**
		 * Main discount price, formatted
		 *
		 * @var $discount_formatted
		 */
		private $discount_formatted;

		/**
		 * Main price after discount, unformatted
		 *
		 * @var $discounted_value
		 */
		private $discounted_value;

		/**
		 * A list of product's images
		 *
		 * @array $images
		 */
		private $images;

		/**
		 * Whether the product is on discount or not
		 *
		 * @var $is_discount
		 */
		private $is_discount;

		/**
		 * Product's status on being scheduled for publish
		 *
		 * @var $is_scheduled
		 */
		private $is_scheduled;

		/**
		 * Whether product is valid or not
		 *
		 * @bool $is_valid
		 */
		private $is_valid = FALSE;

		/**
		 * Holds the category id for the local
		 * product
		 *
		 * @var $local_category_id
		 */
		private $local_category_id;

		/**
		 * Main price in number
		 *
		 * @var $price
		 */
		private $price;

		/**
		 * Currency of regular price
		 *
		 * @var $price_currency
		 */
		private $price_currency;

		/**
		 * Main price, formatted
		 *
		 * @var $price_formatted
		 */
		private $price_formatted;

		/**
		 * Product's ID on source website
		 *
		 * @var $product_id
		 */
		private $product_id;

		/**
		 * Product's URL on source store
		 *
		 * @var $product_url
		 */
		private $product_url;

		/**
		 * Total number of available pieces
		 *
		 * @var $quantity
		 */
		private $quantity;

		/**
		 * Product's average rating
		 *
		 * @var $rating
		 */
		private $rating;

		/**
		 * Rating of each value
		 *
		 * @var $rating_1
		 * @var $rating_2
		 * @var $rating_3
		 * @var $rating_4
		 * @var $rating_5
		 */
		private $rating_1;

		private $rating_2;

		private $rating_3;

		private $rating_4;

		private $rating_5;

		/**
		 * Product's rating in percentage
		 *
		 * @var $rating_percentage
		 */
		private $rating_percentage;

		/**
		 * Total number of user ratings
		 *
		 * @var $rating_total
		 */
		private $rating_total;

		/**
		 * Product review URL
		 *
		 * @var $review_server
		 */
		private $review_server;

		/**
		 * An array of product reviews
		 *
		 * @array $reviews
		 */
		private $reviews;

		/**
		 * Product's seller ID on source website
		 *
		 * @var $seller_id
		 */
		private $seller_id;

		/**
		 * Product's main SKU
		 *
		 * @var $sku
		 */
		private $sku;

		/**
		 * Source of the product
		 *
		 * @var $source
		 */
		private $source;

		/**
		 * An array of product's features
		 *
		 * @array $specs
		 */
		private $specs;

		/**
		 * Array holding the product's tags
		 *
		 * @array $tags
		 */
		private $tags;

		/**
		 * A list of product thumbnails
		 *
		 * @array $thumbnails
		 */
		private $thumbnails;

		/**
		 * Product's title
		 *
		 * @var $title
		 */
		private $title;

		/**
		 * An array of product variations
		 *
		 * @array $variations
		 */
		private $variations;

		/**
		 * DSE_Product constructor.
		 *
		 * @param $product
		 */
		public function __construct( $product ) {

			if ( ! is_array( $product ) ) {
				return new WP_Error( 'dse_invalid_product', esc_html__( 'The provided data is not a valid product.', 'dropshipexpress' ) );
			}

			if ( is_wp_error( $product ) ) {
				return $product;
			}

			/**
			 * Setup the product's data
			 *
			 */
			foreach ( $product as $key => $property ) {
				$this->$key = $property;
			}

			return $this->is_valid = TRUE;
		}

		/**
		 * Method to get an internal property
		 *
		 * @param $value
		 *
		 * @return mixed|null
		 */
		public function Get_Value( $value ) {
			if ( property_exists( $this, $value ) ) {
				return $this->$value;
			} else {
				return NULL;
			}
		}

		/**
		 * Method to set an internal property
		 *
		 * @param $key
		 * @param $value
		 *
		 * @return true|null
		 */
		public function Set_Value( $key, $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			} else {
				return NULL;
			}
			return TRUE;
		}

		/**
		 * Method to get an array containing the product attributes
		 *
		 * @return array
		 */
		public function get_attributes() {
			return $this->attributes;
		}

		/**
		 * Method to get an array of original product categories
		 *
		 * @return array
		 */
		public function get_categories() {
			return $this->categories;
		}

		/**
		 * Method to get the product's category ID
		 *
		 * @return mixed
		 */
		public function get_category_id() {
			return $this->category_id;
		}

		/**
		 * Method to get the seller company's ID
		 *
		 * @return mixed
		 */
		public function get_company_id() {
			return $this->company_id;
		}

		/**
		 * Method to get the general currency of the product
		 *
		 * @return string
		 */
		public function get_currency() {
			return $this->currency;
		}

		/**
		 * Method to get the currency used for shipping the product
		 *
		 * @return mixed
		 */
		public function get_currency_shipping() {
			return $this->currency_shipping;
		}

		/**
		 * Method to get the product's description
		 *
		 * @return string
		 */
		public function get_desc() {
			return $this->description;
		}

		/**
		 * Method to get the product's price after discount in percentage
		 *
		 * @return int
		 */
		public function get_discount() {
			return $this->discount;
		}

		/**
		 * Method to get the discount's currency
		 *
		 * @return string
		 */
		public function get_discount_currency() {
			return $this->discount_currency;
		}

		/**
		 * Method to get the formatted and discounted product's price
		 *
		 * @return string
		 */
		public function get_discount_formatted() {
			return $this->discount_formatted;
		}

		/**
		 * Method to get the discount value of the product
		 *
		 * @return float
		 */
		public function get_discounted_value() {
			return $this->discounted_value;
		}

		/**
		 * Method to get the product's numeric id
		 *
		 * @return int
		 */
		public function get_id() {
			return $this->product_id;
		}

		/**
		 * Method to get an array of product's images
		 *
		 * @return array
		 */
		public function get_images() {
			return $this->images;
		}

		/**
		 * Method to return the category id for
		 * the local product
		 *
		 * @return int
		 */
		public function get_local_category_id() {
			// Set the category ID if not set
			if ( NULL === $this->local_category_id ) {

				// If there's a default category selected by user
				$category_id = DSE_Settings::Get_Setting( $this->source, 'default_cat' );

				if ( 0 !== intval( $category_id ) && term_exists( $category_id, 'product_cat' ) ) {
					$this->local_category_id = (int) $category_id;
				} else {
					// Fallback option
					$this->local_category_id = (int) get_option( 'default_product_cat' );
				}
			}

			return $this->local_category_id;
		}

		/**
		 * Method to get the product's unformatted price
		 *
		 * @return float
		 */
		public function get_price() {
			return $this->price;
		}

		/**
		 * Method to get the currency of the product's price
		 *
		 * @return string
		 */
		public function get_price_currency() {
			return $this->price_currency;
		}

		/**
		 * Method to get the product's formatted price
		 *
		 * @return string
		 */
		public function get_price_formatted() {
			return $this->price_formatted;
		}

		/**
		 * Method to get the stock count
		 *
		 * @return int
		 */
		public function get_quantity() {
			return $this->quantity;
		}

		/**
		 * Method to get the average rating on the product
		 *
		 * @return float
		 */
		public function get_rating() {
			return $this->rating;
		}

		/**
		 * Method to get the total number of ratings
		 *
		 * @return int
		 */
		public function get_rating_count() {
			return $this->rating_total;
		}

		/**
		 * Method to get an specific rating based on the
		 * number of the stars
		 *
		 * @param int $stars
		 *
		 * @return int
		 */
		public function get_rating_n( int $stars ) {
			return $this->{"rating_" . $stars};
		}

		/**
		 * Method to get the ratio of the ratings
		 *
		 * @return float
		 */
		public function get_rating_percentage() {
			return $this->rating_percentage;
		}

		/**
		 * Method to get the link to product's feedback server (optional)
		 *
		 * @return string
		 */
		public function get_review_link() {
			return $this->review_server;
		}

		/**
		 * Method to get an array of product reviews
		 *
		 * @return array
		 */
		public function get_reviews() {
			return $this->reviews;
		}

		/**
		 * Method to get the seller's ID
		 *
		 * @return mixed
		 */
		public function get_seller_id() {
			return $this->seller_id;
		}

		/**
		 * Method to get the main SKU of the product
		 *
		 * @return int
		 */
		public function get_sku() {
			return $this->sku;
		}

		/**
		 * Method to get the product's source
		 *
		 * @return string
		 */
		public function get_source() {
			return $this->source;
		}

		/**
		 * Method to get an array of product's specification
		 *
		 * @return array
		 */
		public function get_specs() {
			return $this->specs;
		}

		/**
		 * Method to get an array of product's tags
		 *
		 * @return array
		 */
		public function get_tags() {
			return $this->tags;
		}

		/**
		 * Method to get an array of product's thumbnails
		 *
		 * @return array
		 */
		public function get_thumbnails() {
			return $this->thumbnails;
		}

		/**
		 * Method to get the product's title
		 *
		 * @return string
		 */
		public function get_title() {
			return $this->title;
		}

		/**
		 * Method to get the product's URL
		 *
		 * @return string
		 */
		public function get_url() {
			return $this->product_url;
		}

		/**
		 * Method to get an array containing the product's variations
		 *
		 * @return array
		 */
		public function get_variations() {
			return $this->variations;
		}

		/**
		 * method to return whether the product is
		 * scheduled for publish or not
		 *
		 * @return mixed
		 */
		public function is_scheduled() {
			return $this->is_scheduled;
		}

		/**
		 * Method to check whether a product is on sale or not
		 *
		 * @return bool
		 */
		public function on_sale() {
			return $this->is_discount;
		}
	}
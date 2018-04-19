<?php
/**
 * Plugin Name: WooCommerce Product Type Column
 * Plugin URI:  https://github.com/woocommerce/woocommerce-product-type-column
 * Description: @todo
 * Author:      WooCommerce
 * Author URI:  https://woocommerce.com
 * Version:     1.0.0
 * License:     GPLv3
 * Text Domain: woocommerce-product-type-column
 * Domain Path: /languages
 *
 * Copyright (C) 2018 WooCommerce
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WooCommerce_Product_Type_Column
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Product_Type_Column_Plugin' ) ) {

	define( 'WC_PRODUCT_TYPE_COLUMN_VERSION', '1.0.0' );

	/**
	 * Main plugin class.
	 */
	class WC_Product_Type_Column_Plugin {
		private static $_instance = null;

		protected $column_name = 'product_type';

		/**
		 * Get the single instance.
		 *
		 * @return WC_Product_Type_Column_Plugin
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Prevent cloning.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Not allowed.', 'woocommerce-product-type-column' ), WC_PRODUCT_TYPE_COLUMN_VERSION );
		}

		/**
		 * Prevent unserializing instances.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Not allowed.', 'woocommerce-product-type-column' ), WC_PRODUCT_TYPE_COLUMN_VERSION );
		}

		/**
		 * WC_Product_Type_Column_Plugin constructor.
		 */
		private function __construct() {
			add_action( 'woocommerce_loaded', array( $this, 'init' ) );
		}

		/**
		 * Define plugin's constants.
		 */
		public function define_constants() {
			define( 'WC_PRODUCT_TYPE_COLUMN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'WC_PRODUCT_TYPE_COLUMN_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		}

		/**
		 * Initialize plugin, activate hooks.
		 */
		public function init() {
			$this->define_constants();

			if ( class_exists( 'WooCommerce' ) ) {
				add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

				add_filter( 'manage_product_posts_columns', array( $this, 'add_product_type_column' ), 10 );
				add_action( 'manage_product_posts_custom_column', array( $this, 'add_product_type_column_cont' ), 10, 2 );
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}

		/**
		 * Load text domain for plugin.
		 *
		 * @return bool
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'woocommerce_prodcut_type_column_plugin_locale', get_locale(), 'woocommerce-product-type-column' );

			load_textdomain( 'woocommerce-product-type-column', trailingslashit( WP_LANG_DIR ) . 'woocommerce-product-type-column/woocommerce-product-type-column' . '-' . $locale . '.mo' );

			load_plugin_textdomain( 'woocommerce-product-type-column', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			return true;
		}

		/**
		 * Enqueue admin CSS style for edit Products page.
		 *
		 * @return bool
		 */
		public function enqueue_admin_styles() {
			$current_screen = get_current_screen();

			wp_register_style( 'wc-product-type-column-admin-styles', WC_PRODUCT_TYPE_COLUMN_PLUGIN_URL . '/assets/css/wc-product-type-column-admin-styles.css', null, WC_PRODUCT_TYPE_COLUMN_VERSION );
			if ( 'edit-product' === $current_screen->id ) {
				wp_enqueue_style( 'wc-product-type-column-admin-styles' );
			}

			return true;
		}

		/**
		 * Add column name/header to edit Products admin page.
		 *
		 * @param array $columns Columns already added by other code.
		 * @return array         Columns to display, with Product Type column added.
		 */
		public function add_product_type_column( $columns ) {
			$columns[ $this->column_name ] = '<span class="wc-type parent-tips" data-tip="' . esc_attr__( 'Type', 'woocommerce-product-type-column' ) . '">' . __( 'Type', 'woocommerce-product-type-column' ) . '</span>';

			return $columns;
		}

		/**
		 * Echoes the content of column Product Type based on product id.
		 *
		 * @param string $column_name Name of column to render.
		 * @param int    $post_id     Id of product.
		 */
		public function add_product_type_column_cont( $column_name, $post_id ) {
			if ( $column_name === $this->column_name ) {
				$product = wc_get_product( $post_id );

				if ( $product->is_type( 'grouped' ) ) {
					echo '<span class="product-type tips grouped" data-tip="' . esc_attr__( 'Grouped', 'woocommerce-product-type-column' ) . '"></span>';
				} elseif ( $product->is_type( 'external' ) ) {
					echo '<span class="product-type tips external" data-tip="' . esc_attr__( 'External/Affiliate', 'woocommerce-product-type-column' ) . '"></span>';
				} elseif ( $product->is_type( 'simple' ) ) {

					if ( $product->is_virtual() ) {
						echo '<span class="product-type tips virtual" data-tip="' . esc_attr__( 'Virtual', 'woocommerce-product-type-column' ) . '"></span>';
					} elseif ( $product->is_downloadable() ) {
						echo '<span class="product-type tips downloadable" data-tip="' . esc_attr__( 'Downloadable', 'woocommerce-product-type-column' ) . '"></span>';
					} else {
						echo '<span class="product-type tips simple" data-tip="' . esc_attr__( 'Simple', 'woocommerce-product-type-column' ) . '"></span>';
					}
				} elseif ( $product->is_type( 'variable' ) ) {
					echo '<span class="product-type tips variable" data-tip="' . esc_attr__( 'Variable', 'woocommerce-product-type-column' ) . '"></span>';
				} else {
					// Assuming that we have other types in future.
					echo '<span class="product-type tips ' . esc_attr( sanitize_html_class( $product->get_type() ) ) . '" data-tip="' . esc_attr( ucfirst( $product->get_type() ) ) . '"></span>';
				}
			}
		}

		/**
		 * Notify user in case she attempts to use the plugin without WooCommerce.
		 */
		public function woocommerce_missing_notice() {
			echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Product Type Column requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-square' ), '<a href="https://woocommerce.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';
			return true;
		}

	}

	WC_Product_Type_Column_Plugin::instance();
}

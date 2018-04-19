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

// Define WC_PRODUCT_TYPE_COLUMN_VERSION.
if ( ! defined( 'WC_PRODUCT_TYPE_COLUMN_VERSION' ) ) {
	define( 'WC_PRODUCT_TYPE_COLUMN_VERSION', '1.0.0' );
}

// Define WC_PRODUCT_TYPE_COLUMN_PLUGIN_FILE.
if ( ! defined( 'WC_PRODUCT_TYPE_COLUMN_PLUGIN_FILE' ) ) {
	define( 'WC_PRODUCT_TYPE_COLUMN_PLUGIN_FILE', __FILE__ );
}

// Include the main WooCommerce class.
if ( ! class_exists( 'WC_Product_Type_Column' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-product-type-column.php';
}

add_action( 'plugins_loaded', array( 'WC_Product_Type_Column', 'init' ) );
register_activation_hook( __FILE__, array( 'WC_Product_Type_Column', 'activation_check' ) );

<?php
/**
 * Product type column main class
 *
 * @package WooCommerce_Product_Type_Column
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
final class WC_Product_Type_Column {

	/**
	 * Initialize plugin, activate hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		if ( class_exists( 'WooCommerce' ) && version_compare( WC_VERSION, '3.4', '>=' ) ) {
			include dirname( __FILE__ ) . '/class-wc-product-type-column-admin.php';

			$admin = new WC_Product_Type_Column_Admin();
			$admin->init();
		} else {
			add_action( 'admin_notices', array( __CLASS__, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Load text domain for plugin.
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-product-type-column', false, plugin_basename( dirname( WC_PRODUCT_TYPE_COLUMN_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Notify user in case she attempts to use the plugin without WooCommerce.
	 */
	public static function woocommerce_missing_notice() {
		/* translators: %s: WooCommerce URL */
		echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'WooCommerce Product Type Column requires WooCommerce 3.4+ to be installed and active. You can download %s here.', 'woocommerce-product-type-column' ), '<a href="https://woocommerce.com/woocommerce/" target="_blank">WooCommerce</a>' ) ) . '</p></div>';
	}

}

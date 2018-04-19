<?php
/**
 * Product type column admin class
 *
 * @package WooCommerce_Product_Type_Column\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin admin class.
 */
class WC_Product_Type_Column_Admin {

	/**
	 * Column name
	 *
	 * @var string
	 */
	protected $column_name = 'product_type';

	/**
	 * Init admin.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_filter( 'manage_product_posts_columns', array( $this, 'add_product_type_column' ), 11 );
		add_action( 'manage_product_posts_custom_column', array( $this, 'add_product_type_column_cont' ), 10, 2 );
	}

	/**
	 * Enqueue admin CSS style for edit Products page.
	 */
	public function enqueue_admin_styles() {
		$current_screen = get_current_screen();

		wp_register_style( 'wc-product-type-column-admin-styles', plugins_url( 'assets/css/admin/admin.css', WC_PRODUCT_TYPE_COLUMN_PLUGIN_FILE ), array( 'woocommerce_admin_styles' ), WC_PRODUCT_TYPE_COLUMN_VERSION );
		wp_style_add_data( 'wc-product-type-column-admin-styles', 'rtl', 'replace' );

		if ( 'edit-product' === $current_screen->id ) {
			wp_enqueue_style( 'wc-product-type-column-admin-styles' );
		}
	}

	/**
	 * Add column name/header to edit Products admin page.
	 *
	 * @param array $columns Columns already added by other code.
	 * @return array         Columns to display, with Product Type column added.
	 */
	public function add_product_type_column( $columns ) {
		$new                       = array();
		$new[ $this->column_name ] = '<span class="wc-type parent-tips" data-tip="' . esc_attr__( 'Type', 'woocommerce-product-type-column' ) . '">' . esc_html__( 'Type', 'woocommerce-product-type-column' ) . '</span>';

		// Insert before "date" column.
		$date_index = array_search( 'date', array_keys( $columns ), true );
		if ( 0 < $date_index ) {
			return array_slice( $columns, 0, $date_index, true ) + $new + array_slice( $columns, $date_index, count( $columns ) - 1, true );
		}

		return array_merge( $columns, $new );
	}

	/**
	 * Echoes the content of column Product Type based on product id.
	 *
	 * @param string $column_name Name of column to render.
	 * @param int    $post_id     Id of product.
	 */
	public function add_product_type_column_cont( $column_name, $post_id ) {
		if ( $column_name === $this->column_name ) {
			global $the_product;

			if ( empty( $the_product ) || $the_product->get_id() !== $post_id ) {
				$the_product = wc_get_product( $post_id );
			}

			if ( $the_product->is_type( 'grouped' ) ) {
				echo '<span class="product-type tips grouped" data-tip="' . esc_attr__( 'Grouped', 'woocommerce-product-type-column' ) . '"></span>';
			} elseif ( $the_product->is_type( 'external' ) ) {
				echo '<span class="product-type tips external" data-tip="' . esc_attr__( 'External/Affiliate', 'woocommerce-product-type-column' ) . '"></span>';
			} elseif ( $the_product->is_type( 'simple' ) ) {

				if ( $the_product->is_virtual() ) {
					echo '<span class="product-type tips virtual" data-tip="' . esc_attr__( 'Virtual', 'woocommerce-product-type-column' ) . '"></span>';
				} elseif ( $the_product->is_downloadable() ) {
					echo '<span class="product-type tips downloadable" data-tip="' . esc_attr__( 'Downloadable', 'woocommerce-product-type-column' ) . '"></span>';
				} else {
					echo '<span class="product-type tips simple" data-tip="' . esc_attr__( 'Simple', 'woocommerce-product-type-column' ) . '"></span>';
				}
			} elseif ( $the_product->is_type( 'variable' ) ) {
				echo '<span class="product-type tips variable" data-tip="' . esc_attr__( 'Variable', 'woocommerce-product-type-column' ) . '"></span>';
			} else {
				// Assuming that we have other types in future.
				echo '<span class="product-type tips ' . esc_attr( sanitize_html_class( $the_product->get_type() ) ) . '" data-tip="' . esc_attr( ucfirst( $the_product->get_type() ) ) . '"></span>';
			}
		}
	}
}

<?php
/**
 * Load assets.
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Admin_Assets' ) ) :

/**
 * Vendor_Admin_Assets Class
 */
class Vendor_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_head', array( $this, 'product_taxonomy_styles' ) );
	}

	/**
	 * Enqueue styles
	 */
	public function admin_styles() {
		global $woocommerce, $wp_scripts;

		// Sitewide menu CSS
		wp_enqueue_style( 'woocommerce_admin_menu_styles', Vendor()->plugin_dir_url() . '/css/menu.css' );

		$screen = get_current_screen();

		if ( in_array( $screen->id, wc_get_screen_ids() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			// Admin styles for WC pages only
			wp_enqueue_style( 'woocommerce_admin_styles', Vendor()->plugin_dir_url() . '/css/admin.css' );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( in_array( $screen->id, array( 'dashboard' ) ) ) {
			wp_enqueue_style( 'woocommerce_admin_dashboard_styles', Vendor()->plugin_dir_url() . '/css/dashboard.css' );
		}

		do_action( 'woocommerce_admin_css' );
	}


	/**
	 * Enqueue scripts
	 */
	public function admin_scripts() {
		global $woocommerce, $wp_query, $post;

		$screen       = get_current_screen();
		$wc_screen_id = strtolower( __( 'WooCommerce', VENDOR_UNIQUE_IDENTIFIER ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts
		wp_register_script( 'woocommerce_admin', Vendor()->plugin_dir_url() . '/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), VENDOR_VERSION );

		wp_register_script( 'jquery-blockui', Vendor()->plugin_dir_url() . '/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.60', true );

		wp_register_script( 'jquery-tiptip', Vendor()->plugin_dir_url() . '/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), VENDOR_VERSION, true );

		wp_register_script( 'accounting', Vendor()->plugin_dir_url() . '/js/admin/accounting' . $suffix . '.js', array( 'jquery' ), '1.3.2' );

		wp_register_script( 'round', Vendor()->plugin_dir_url() . '/js/admin/round' . $suffix . '.js', array( 'jquery' ), '1.0.0' );

		wp_register_script( 'ajax-chosen', Vendor()->plugin_dir_url() . '/js/jquery-chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'jquery-chosen'), VENDOR_VERSION );

		wp_register_script( 'jquery-chosen', Vendor()->plugin_dir_url() . '/js/jquery-chosen/chosen.jquery' . $suffix . '.js', array('jquery'), VENDOR_VERSION );

		// Accounting
    	$params = array(
			'mon_decimal_point' => get_option( 'woocommerce_price_decimal_sep' )
    	);

    	wp_localize_script( 'accounting', 'accounting_params', $params );

		// WooCommerce admin pages
	    if ( in_array( $screen->id, wc_get_screen_ids() ) ) {

	    	wp_enqueue_script( 'woocommerce_admin' );
	    	wp_enqueue_script( 'iris' );
	    	wp_enqueue_script( 'ajax-chosen' );
	    	wp_enqueue_script( 'jquery-chosen' );
	    	wp_enqueue_script( 'jquery-ui-sortable' );
	    	wp_enqueue_script( 'jquery-ui-autocomplete' );

	    	$locale  = localeconv();
	    	$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

	    	$params = array(
				'i18n_decimal_error'     => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', VENDOR_UNIQUE_IDENTIFIER ), $decimal ),
				'i18n_mon_decimal_error' => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', VENDOR_UNIQUE_IDENTIFIER ), get_option( 'woocommerce_price_decimal_sep' ) ),
				'decimal_point'          => $decimal,
				'mon_decimal_point'      => get_option( 'woocommerce_price_decimal_sep' )
	    	);

	    	wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
	    }

	    // Edit product category pages
	    if ( in_array( $screen->id, array( 'edit-product_cat' ) ) )
			wp_enqueue_media();

		// Products
		if ( in_array( $screen->id, array( 'edit-product' ) ) )
			wp_enqueue_script( 'woocommerce_quick-edit', Vendor()->plugin_dir_url() . '/js/admin/quick-edit' . $suffix . '.js', array('jquery'), VENDOR_VERSION );

		// Product/Coupon/Orders
		if ( in_array( $screen->id, array( 'shop_coupon', 'shop_order', 'product', 'edit-shop_coupon', 'edit-shop_order', 'edit-product' ) ) ) {

			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_media();
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'jquery-chosen' );
			wp_enqueue_script( 'plupload-all' );

		}

		// Term ordering - only when sorting by term_order
		if ( ( strstr( $screen->id, 'edit-pa_' ) || ( ! empty( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], apply_filters( 'woocommerce_sortable_taxonomies', array( VENDOR_TAXONOMIES_CATEGORY ) ) ) ) ) && ! isset( $_GET['orderby'] ) ) {

			wp_register_script( 'woocommerce_term_ordering', Vendor()->plugin_dir_url() . '/js/admin/term-ordering.js', array('jquery-ui-sortable'), VENDOR_VERSION );
			wp_enqueue_script( 'woocommerce_term_ordering' );

			$taxonomy = isset( $_GET['taxonomy'] ) ? wc_clean( $_GET['taxonomy'] ) : '';

			$woocommerce_term_order_params = array(
				'taxonomy' 			=>  $taxonomy
			 );

			wp_localize_script( 'woocommerce_term_ordering', 'woocommerce_term_ordering_params', $woocommerce_term_order_params );
		}

		// Product sorting - only when sorting by menu order on the products page
		if ( current_user_can('edit_others_pages') && $screen->id == 'edit-product' && isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'menu_order title' ) {

			wp_enqueue_script( 'woocommerce_product_ordering', Vendor()->plugin_dir_url() . '/js/admin/product-ordering.js', array('jquery-ui-sortable'), '1.0', true );

		}

		// Reports Pages
		if ( in_array( $screen->id, apply_filters( 'woocommerce_reports_screen_ids', array( $wc_screen_id . '_page_vendor-reports', 'dashboard' ) ) ) ) {
			wp_enqueue_script( 'vendor-reports', Vendor()->plugin_dir_url() . '/js/admin/reports' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0' );
			wp_enqueue_script( 'flot', Vendor()->plugin_dir_url() . '/js/admin/jquery.flot' . $suffix . '.js', array( 'jquery' ), '1.0' );
			wp_enqueue_script( 'flot-resize', Vendor()->plugin_dir_url() . '/js/admin/jquery.flot.resize' . $suffix . '.js', array('jquery', 'flot'), '1.0' );
			wp_enqueue_script( 'flot-time', Vendor()->plugin_dir_url() . '/js/admin/jquery.flot.time' . $suffix . '.js', array( 'jquery', 'flot' ), '1.0' );
			wp_enqueue_script( 'flot-pie', Vendor()->plugin_dir_url() . '/js/admin/jquery.flot.pie' . $suffix . '.js', array( 'jquery', 'flot' ), '1.0' );
			wp_enqueue_script( 'flot-stack', Vendor()->plugin_dir_url() . '/js/admin/jquery.flot.stack' . $suffix . '.js', array( 'jquery', 'flot' ), '1.0' );
		}

		// Chosen RTL
		if ( is_rtl() ) {
			wp_enqueue_script( 'chosen-rtl', Vendor()->plugin_dir_url() . '/js/jquery-chosen/chosen-rtl' . $suffix . '.js', array( 'jquery' ), VENDOR_VERSION, true );
		}
	}

	/**
	 * Admin Head
	 *
	 * Outputs some styles in the admin <head> to show icons on the woocommerce admin pages
	 *
	 * @access public
	 * @return void
	 */
	public function product_taxonomy_styles() {

		if ( ! current_user_can( 'manage_vendor' ) ) return;
		?>
		<style type="text/css">
			<?php if ( isset($_GET['taxonomy']) && $_GET['taxonomy'] == VENDOR_TAXONOMIES_CATEGORY ) : ?>
				.icon32-posts-product { background-position: -243px -5px !important; }
			<?php elseif ( isset($_GET['taxonomy']) && $_GET['taxonomy'] == VENDOR_TAXONOMIES_TAG ) : ?>
				.icon32-posts-product { background-position: -301px -5px !important; }
			<?php endif; ?>
		</style>
		<?php
	}
}

endif;

return new Vendor_Admin_Assets();

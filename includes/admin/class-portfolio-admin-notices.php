<?php
/**
 * Display notices in admin.
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Admin_Notices' ) ) :

/**
 * Vendor_Admin_Notices Class
 */
class Vendor_Admin_Notices {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
	}

	/**
	 * Add notices + styles if needed.
	 */
	public function add_notices() {
		if ( get_option( '_wc_needs_update' ) == 1 || get_option( '_wc_needs_pages' ) == 1 ) {
			wp_enqueue_style( 'woocommerce-activation', Vendor()->plugin_dir_url() . '/css/activation.css' );
			add_action( 'admin_notices', array( $this, 'install_notice' ) );
		}

		$template = get_option( 'template' );

		if ( ! current_theme_supports( 'woocommerce' ) && ! in_array( $template, array( 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' ) ) ) {

			if ( ! empty( $_GET['hide_woocommerce_theme_support_check'] ) ) {
				update_option( 'woocommerce_theme_support_check', $template );
				return;
			}

			if ( get_option( 'woocommerce_theme_support_check' ) !== $template ) {
				wp_enqueue_style( 'woocommerce-activation', Vendor()->plugin_dir_url() . '/css/activation.css' );
				add_action( 'admin_notices', array( $this, 'theme_check_notice' ) );
			}
		}
	}

	/**
	 * Show the install notices
	 */
	function install_notice() {

		// If we need to update, include a message with the update button
		if ( get_option( '_wc_needs_update' ) == 1 ) {
			include( 'views/html-notice-update.php' );
		}

		// If we have just installed, show a message with the install pages button
		elseif ( get_option( '_wc_needs_pages' ) == 1 ) {
			include( 'views/html-notice-install.php' );
		}
	}

	/**
	 * Show the Theme Check notice
	 */
	function theme_check_notice() {
		include( 'views/html-notice-theme-support.php' );
	}
}

endif;

return new Vendor_Admin_Notices();
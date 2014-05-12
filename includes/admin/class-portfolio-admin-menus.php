<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Admin_Menus' ) ) :

/**
 * Vendor_Admin_Menus Class
 */
class Vendor_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
	}

	/**
	 * Add menu item
	 */
	public function admin_menu() {
		if ( current_user_can( 'manage_vendor' ) ) {
			$settings_page = add_submenu_page( 'options-general.php', __( 'Vendor Settings', VENDOR_UNIQUE_IDENTIFIER ),  __( 'Vendor', VENDOR_UNIQUE_IDENTIFIER ) , 'manage_vendor', 'vendor-settings', array( $this, 'settings_page' ) );
		}
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		include_once( dirname( __FILE__ ) . '/class-vendor-admin-settings.php' );
		Vendor_Admin_Settings::output();
	}
}

endif;

return new Vendor_Admin_Menus();
<?php
/**
 * WooCommerce Admin.
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Admin' ) ) :

class Vendor_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditonal_includes' ) );
		add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
		add_action( 'admin_init', array( $this, 'preview_emails' ) );
		add_action( 'admin_footer', 'wc_print_js', 25 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		// Functions
		include( dirname( __FILE__ ) . '/vendor-admin-functions.php' );
		//include( dirname( __FILE__ ) . '/vendor-meta-box-functions.php' );

		// Classes
		//include( dirname( __FILE__ ) . '/class-vendor-admin-post-types.php' );
		//include( dirname( __FILE__ ) . '/class-vendor-admin-taxonomies.php' );

		// Classes we only need if the ajax is not-ajax
		if ( ! is_ajax() ) {
			include( dirname( __FILE__ ) . '/class-vendor-admin-menus.php' );
			include( dirname( __FILE__ ) . '/class-vendor-admin-welcome.php' );
			include( dirname( __FILE__ ) . '/class-vendor-admin-notices.php' );
			include( dirname( __FILE__ ) . '/class-vendor-admin-assets.php' );
			include( dirname( __FILE__ ) . '/class-vendor-admin-permalink-settings.php' );
			//include( dirname( __FILE__ ) . '/class-vendor-admin-editor.php' );

			// Help
			if ( apply_filters( 'vendor_enable_admin_help_tab', true ) )
				include( dirname( __FILE__ ) . '/class-vendor-admin-help.php' );
		}
	}

	/**
	 * Include admin files conditionally
	 */
	public function conditonal_includes() {
		$screen = get_current_screen();

		switch ( $screen->id ) {
			case 'dashboard' :
				include( dirname( __FILE__ ) . '/class-vendor-admin-dashboard.php' );
			break;
		}
	}

	/**
	 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from accessing admin
	 */
	public function prevent_admin_access() {
		$prevent_access = false;

		if ( 'yes' == get_option( 'vendor_lock_down_admin' ) && ! is_ajax() && ! ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_vendor' ) ) && basename( $_SERVER["SCRIPT_FILENAME"] ) !== 'admin-post.php' ) {
			$prevent_access = true;
		}

		$prevent_access = apply_filters( 'vendor_prevent_admin_access', $prevent_access );

		if ( $prevent_access ) {
			wp_safe_redirect( get_permalink( home_url() ) );
			exit;
		}
	}

	/**
	 * Preview email template
	 * @return [type]
	 */
	public function preview_emails() {
		if ( isset( $_GET['preview_vendor_mail'] ) ) {
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'preview-mail') )
				die( 'Security check' );

			global $email_heading;

			ob_start();

			include( 'views/html-email-template-preview.php' );

			$mailer        = WC()->mailer();
			$message       = ob_get_clean();
			$email_heading = __( 'HTML Email Template', VENDOR_UNIQUE_IDENTIFIER );

			echo $mailer->wrap_message( $email_heading, $message );
			exit;
		}
	}
}

endif;

return new Vendor_Admin();

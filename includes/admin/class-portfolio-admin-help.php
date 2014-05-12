<?php
/**
 * Add some content to the help tab.
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Admin_Help' ) ) :

/**
 * Vendor_Admin_Help Class
 */
class Vendor_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( "current_screen", array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add help tabs
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! in_array( $screen->id, wc_get_screen_ids() ) )
			return;

		$screen->add_help_tab( array(
		    'id'	=> 'woocommerce_docs_tab',
		    'title'	=> __( 'Documentation', VENDOR_UNIQUE_IDENTIFIER ),
		    'content'	=>

		    	'<p>' . __( 'Thank you for using WooCommerce :) Should you need help using or extending WooCommerce please read the documentation.', VENDOR_UNIQUE_IDENTIFIER ) . '</p>' .

		    	'<p><a href="' . 'http://docs.woothemes.com/documentation/plugins/woocommerce/' . '" class="button button-primary">' . __( 'WooCommerce Documentation', VENDOR_UNIQUE_IDENTIFIER ) . '</a> <a href="' . 'http://docs.woothemes.com/wc-apidocs/' . '" class="button">' . __( 'Developer API Docs', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>'

		) );

		$screen->add_help_tab( array(
		    'id'	=> 'woocommerce_support_tab',
		    'title'	=> __( 'Support', VENDOR_UNIQUE_IDENTIFIER ),
		    'content'	=>

		    	'<p>' . sprintf(__( 'After <a href="%s">reading the documentation</a>, for further assistance you can use the <a href="%s">community forum</a>, or if you have access as a WooThemes customer, <a href="%s">our support desk</a>.', VENDOR_UNIQUE_IDENTIFIER ), 'http://docs.woothemes.com/documentation/plugins/woocommerce/', 'http://wordpress.org/support/plugin/woocommerce', 'http://support.woothemes.com' ) . '</p>' .

		    	'<p>' . __( 'Before asking for help we recommend checking the status page to identify any problems with your configuration.', VENDOR_UNIQUE_IDENTIFIER ) . '</p>' .

		    	'<p><a href="' . 'http://wordpress.org/support/plugin/woocommerce' . '" class="button">' . __( 'Community Support', VENDOR_UNIQUE_IDENTIFIER ) . '</a> <a href="' . 'http://support.woothemes.com' . '" class="button">' . __( 'Customer Support', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>'

		) );

		$screen->add_help_tab( array(
		    'id'	=> 'woocommerce_bugs_tab',
		    'title'	=> __( 'Found a bug?', VENDOR_UNIQUE_IDENTIFIER ),
		    'content'	=>

		    	'<p>' . sprintf(__( 'If you find a bug within WooCommerce core you can create a ticket via <a href="%s">Github issues</a>. Ensure you read the <a href="%s">contribution guide</a> prior to submitting your report. Be as descriptive as possible and please include your <a href="%s">system status report</a>.', VENDOR_UNIQUE_IDENTIFIER ), 'https://github.com/woothemes/woocommerce/issues?state=open', 'https://github.com/woothemes/woocommerce/blob/master/CONTRIBUTING.md', admin_url( 'admin.php?page=vendor-status' ) ) . '</p>' .

		    	'<p><a href="https://github.com/woothemes/woocommerce/issues?state=open" class="button button-primary">' . __( 'Report a bug', VENDOR_UNIQUE_IDENTIFIER ) . '</a> <a href="' . admin_url('admin.php?page=vendor-status') . '" class="button">' . __( 'System Status', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>'

		) );

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', VENDOR_UNIQUE_IDENTIFIER ) . '</strong></p>' .
			'<p><a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __( 'About WooCommerce', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>' .
			'<p><a href="http://wordpress.org/extend/plugins/woocommerce/" target="_blank">' . __( 'Project on WordPress.org', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>' .
			'<p><a href="https://github.com/woothemes/woocommerce" target="_blank">' . __( 'Project on Github', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>' .
			'<p><a href="http://www.woothemes.com/product-category/woocommerce-extensions/" target="_blank">' . __( 'Official Extensions', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>' .
			'<p><a href="http://www.woothemes.com/product-category/themes/woocommerce/" target="_blank">' . __( 'Official Themes', VENDOR_UNIQUE_IDENTIFIER ) . '</a></p>'
		);
	}

}

endif;

return new Vendor_Admin_Help();
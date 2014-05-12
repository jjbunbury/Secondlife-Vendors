<?php
/**
 * WooCommerce Admin Functions
 *
 * @author      Dazzle Software
 * @category    Core
 * @package     Vendor/Admin/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get all WooCommerce screen ids
 *
 * @return array
 */
function wc_get_screen_ids() {
	return apply_filters( 'woocommerce_screen_ids', array(
    	'settings_page_vendor-settings',
    	'edit-product',
    	'product',
    	'edit-product_cat',
    	'edit-product_tag'
    ) );
}

/**
 * Create a page and store the ID in an option.
 *
 * @access public
 * @param mixed $slug Slug for the new page
 * @param mixed $option Option name to store the page's ID
 * @param string $page_title (default: '') Title for the new page
 * @param string $page_content (default: '') Content for the new page
 * @param int $post_parent (default: 0) Parent for the new page
 * @return int page ID
 */
function wc_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
    global $wpdb;

    $option_value = get_option( $option );

    if ( $option_value > 0 && get_post( $option_value ) )
        return -1;

    $page_found = null;

    if ( strlen( $page_content ) > 0 ) {
        // Search for an existing page with the specified page content (typically a shortcode)
        $page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
    } else {
        // Search for an existing page with the specified page slug
        $page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_name = %s LIMIT 1;", $slug ) );
    }

    if ( $page_found ) {
        if ( ! $option_value )
            update_option( $option, $page_found );
		
		return $page_found;
    }

    $page_data = array(
        'post_status'       => 'publish',
        'post_type'         => 'page',
        'post_author'       => 1,
        'post_name'         => $slug,
        'post_title'        => $page_title,
        'post_content'      => $page_content,
        'post_parent'       => $post_parent,
        'comment_status'    => 'closed'
    );
    $page_id = wp_insert_post( $page_data );

    if ( $option )
        update_option( $option, $page_id );

    return $page_id;
}

/**
 * Output admin fields.
 *
 * Loops though the woocommerce options array and outputs each field.
 *
 * @param array $options Opens array to output
 */
function woocommerce_admin_fields( $options ) {
    if ( ! class_exists( 'Vendor_Admin_Settings' ) )
        include( dirname( __FILE__ ) . '/class-vendor-admin-settings.php' );

    Vendor_Admin_Settings::output_fields( $options );
}

/**
 * Update all settings which are passed.
 *
 * @access public
 * @param array $options
 * @return void
 */
function woocommerce_update_options( $options ) {
    if ( ! class_exists( 'Vendor_Admin_Settings' ) )
        include( dirname( __FILE__ ) . '/class-vendor-admin-settings.php' );

    Vendor_Admin_Settings::save_fields( $options );
}

/**
 * Get a setting from the settings API.
 *
 * @param mixed $option
 * @return string
 */
function woocommerce_settings_get_option( $option_name, $default = '' ) {
    if ( ! class_exists( 'Vendor_Admin_Settings' ) )
        include( dirname( __FILE__ ) . '/class-vendor-admin-settings.php' );

    return Vendor_Admin_Settings::get_option( $option_name, $default );
}

/**
 * Generate CSS from the less file when changing colours.
 *
 * @access public
 * @return void
 */
function woocommerce_compile_less_styles() {

    $colors         = array_map( 'esc_attr', (array) get_option( 'woocommerce_frontend_css_colors' ) );
    $base_file      = Vendor()->plugin_dir_path() . '/assets/css/vendor-base.less';
    $less_file      = Vendor()->plugin_dir_path() . '/assets/css/vendor.less';
    $css_file       = Vendor()->plugin_dir_path() . '/assets/css/vendor.css';

    // Write less file
    if ( is_writable( $base_file ) && is_writable( $css_file ) ) {

        // Colours changed - recompile less
        if ( ! class_exists( 'lessc' ) )
            include_once( Vendor()->plugin_dir_path() . '/includes/libraries/class-lessc.php' );
        if ( ! class_exists( 'cssmin' ) )
            include_once( Vendor()->plugin_dir_path() . '/includes/libraries/class-cssmin.php' );

        try {
            // Set default if colours not set
            if ( ! $colors['primary'] ) $colors['primary'] = '#ad74a2';
            if ( ! $colors['secondary'] ) $colors['secondary'] = '#f7f6f7';
            if ( ! $colors['highlight'] ) $colors['highlight'] = '#85ad74';
            if ( ! $colors['content_bg'] ) $colors['content_bg'] = '#ffffff';
            if ( ! $colors['subtext'] ) $colors['subtext'] = '#777777';

            // Write new color to base file
            $color_rules = "
@primary:       " . $colors['primary'] . ";
@primarytext:   " . wc_light_or_dark( $colors['primary'], 'desaturate(darken(@primary,50%),18%)', 'desaturate(lighten(@primary,50%),18%)' ) . ";

@secondary:     " . $colors['secondary'] . ";
@secondarytext: " . wc_light_or_dark( $colors['secondary'], 'desaturate(darken(@secondary,60%),18%)', 'desaturate(lighten(@secondary,60%),18%)' ) . ";

@highlight:     " . $colors['highlight'] . ";
@highlightext:  " . wc_light_or_dark( $colors['highlight'], 'desaturate(darken(@highlight,60%),18%)', 'desaturate(lighten(@highlight,60%),18%)' ) . ";

@contentbg:     " . $colors['content_bg'] . ";

@subtext:       " . $colors['subtext'] . ";
            ";

            file_put_contents( $base_file, $color_rules );

            $less           = new lessc( $less_file );
            $compiled_css   = $less->parse();

            $compiled_css = CssMin::minify( $compiled_css );

            if ( $compiled_css )
                file_put_contents( $css_file, $compiled_css );

        } catch ( exception $ex ) {
            wp_die( __( 'Could not compile vendor.less:', VENDOR_UNIQUE_IDENTIFIER ) . ' ' . $ex->getMessage() );
        }
    }
}

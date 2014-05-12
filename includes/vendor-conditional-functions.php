<?php
/**
 * WooCommerce Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @author 		Dazzle Software
 * @category 	Core
 * @package 	Vendor/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * is_vendor - Returns true if on a page which uses WooCommerce templates (cart and checkout are standard pages with shortcodes and thus are not included)
 *
 * @access public
 * @return bool
 */
function is_vendor() {
	return apply_filters( 'is_vendor', ( is_vendor_archive() || is_vendor_taxonomy() || is_vendor_singular() ) ? true : false );
}

if ( ! function_exists( 'is_vendor_archive' ) ) {

	/**
	 * is_vendor_archive - Returns true when viewing the product type archive (shop).
	 *
	 * @access public
	 * @return bool
	 */
	function is_vendor_archive() {
		return ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) ? true : false;
	}
}

if ( ! function_exists( 'is_vendor_taxonomy' ) ) {

	/**
	 * is_vendor_taxonomy - Returns true when viewing a product taxonomy archive.
	 *
	 * @access public
	 * @return bool
	 */
	function is_vendor_taxonomy() {
		return is_tax( get_object_taxonomies( 'product' ) );
	}
}

if ( ! function_exists( 'is_vendor_category' ) ) {

	/**
	 * is_vendor_category - Returns true when viewing a product category.
	 *
	 * @access public
	 * @param string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_vendor_category( $term = '' ) {
		return is_tax( VENDOR_TAXONOMIES_CATEGORY, $term );
	}
}

if ( ! function_exists( 'is_vendor_tag' ) ) {

	/**
	 * is_vendor_tag - Returns true when viewing a product tag.
	 *
	 * @access public
	 * @param string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_vendor_tag( $term = '' ) {
		return is_tax( VENDOR_TAXONOMIES_TAG, $term );
	}
}

if ( ! function_exists( 'is_vendor_singular' ) ) {

	/**
	 * is_vendor_singular - Returns true when viewing a single product.
	 *
	 * @access public
	 * @return bool
	 */
	function is_vendor_singular() {
		return is_singular( array( 'product' ) );
	}
}
/*
	This only be used in the testimonials version of the plugin
*/
if ( ! function_exists( 'is_vendor_submit' ) ) {

	/**
	 * is_vendor_submit - Returns true when viewing the submit page.
	 *
	 * @access public
	 * @return bool
	 */
	function is_vendor_submit() {
		return is_page( wc_get_page_id( 'submit' ) );
	}
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @access public
	 * @return bool
	 */
	function is_ajax() {
		if ( defined('DOING_AJAX') )
			return true;

		return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
	}
}
<?php
/**
 * WooCommerce Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		Dazzle Software
 * @category 	Core
 * @package 	Vendor/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
// Include core functions
include( dirname( __FILE__ ) . '/vendor-cart-functions.php' );
include( dirname( __FILE__ ) . '/vendor-conditional-functions.php' ); // added
include( dirname( __FILE__ ) . '/vendor-coupon-functions.php' );
include( dirname( __FILE__ ) . '/vendor-customer-functions.php' );
include( dirname( __FILE__ ) . '/vendor-deprecated-functions.php' ); //deprecated
include( dirname( __FILE__ ) . '/vendor-formatting-functions.php' );
include( dirname( __FILE__ ) . '/vendor-notice-functions.php' );
include( dirname( __FILE__ ) . '/vendor-order-functions.php' );
include( dirname( __FILE__ ) . '/vendor-page-functions.php' ); // added
include( dirname( __FILE__ ) . '/vendor-product-functions.php' );
include( dirname( __FILE__ ) . '/vendor-term-functions.php' );
include( dirname( __FILE__ ) . '/vendor-attribute-functions.php' );
*/

include( dirname( __FILE__ ) . '/vendor-conditional-functions.php' );
include( dirname( __FILE__ ) . '/vendor-deprecated-functions.php' );
include( dirname( __FILE__ ) . '/vendor-page-functions.php' );
include( dirname( __FILE__ ) . '/vendor-functions.php' );
/**
 * Short Description (excerpt)
 */
add_filter( 'vendor_short_description', 'wptexturize' );
add_filter( 'vendor_short_description', 'convert_smilies' );
add_filter( 'vendor_short_description', 'convert_chars' );
add_filter( 'vendor_short_description', 'wpautop' );
add_filter( 'vendor_short_description', 'shortcode_unautop' );
add_filter( 'vendor_short_description', 'prepend_attachment' );
add_filter( 'vendor_short_description', 'do_shortcode', 11 ); // AFTER wpautop()

/**
 * Get an image size.
 *
 * Variable is filtered by vendor_get_image_size_{image_size}
 *
 * @param string $image_size
 * @return array
 * @todo move to image class maybe?
 */
function vendor_get_image_size( $image_size ) {
	if ( in_array( $image_size, array( VENDOR_IMAGE_SIZE_THUMBNAIL, VENDOR_IMAGE_SIZE_CATALOG, VENDOR_IMAGE_SIZE_SINGLE ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 1;
	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}
	return apply_filters( 'vendor_get_image_size_' . $image_size, $size );
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function wc_enqueue_js( $code ) {
	global $queued_js;

	if ( empty( $queued_js ) )
		$queued_js = "";

	$queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function wc_print_js() {
	global $queued_js;

	if ( ! empty( $queued_js ) ) {

		echo "<!-- WooCommerce JavaScript-->\n<script type=\"text/javascript\">\njQuery(document).ready(function($) {";

		// Sanitize
		$queued_js = wp_check_invalid_utf8( $queued_js );
		$queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $queued_js );
		$queued_js = str_replace( "\r", '', $queued_js );

		echo $queued_js . "});\n</script>\n";

		unset( $queued_js );
	}
}

/**
 * Set a cookie - wrapper for setcookie using WP constants
 *
 * @param  string  $name   Name of the cookie being set
 * @param  string  $value  Value of the cookie
 * @param  integer $expire Expiry of the cookie
 */
function wc_setcookie( $name, $value, $expire = 0 ) {
	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, false );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		trigger_error( "Cookie cannot be set - headers already sent", E_USER_NOTICE );
	}
}
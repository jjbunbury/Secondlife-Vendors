<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author 		Dazzle Software
 * @category 	Core
 * @package 	Vendor/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( defined( 'WC_PLUGIN_FILE' ) ) {
	_deprecated_function( 'WC_PLUGIN_FILE', VENDOR_VERSION, 'VENDOR_PLUGIN_FILE' );
}

if ( defined( 'WC_VERSION' ) ) {
	_deprecated_function( 'WC_VERSION', VENDOR_VERSION, 'VENDOR_VERSION' );
}

if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
	_deprecated_function( 'WOOCOMMERCE_VERSION', VENDOR_VERSION, 'VENDOR_VERSION' );
}

if ( defined( 'WC_TEMPLATE_PATH' ) ) {
	_deprecated_function( 'WC_TEMPLATE_PATH', VENDOR_VERSION, 'Vendor()->plugin_template_path()' );
}

if ( defined( 'WC_ROUNDING_PRECISION' ) ) {
	_deprecated_function( 'WC_ROUNDING_PRECISION', VENDOR_VERSION );
}

if ( defined( 'WC_TAX_ROUNDING_MODE' ) ) {
	_deprecated_function( 'WC_TAX_ROUNDING_MODE', VENDOR_VERSION );
}

if ( defined( 'WC_DELIMITER' ) ) {
	_deprecated_function( 'WC_DELIMITER', VENDOR_VERSION );
}
function WC() {
	_deprecated_function( 'WC', VENDOR_VERSION, 'Vendor' );
    //return Vendor::instance();
}
// Global for backwards compatibility.
//$GLOBALS['woocommerce'] = WC();

if ( get_option( 'woocommerce_db_version' ) !== false ) {
	_deprecated_function( 'woocommerce_db_version', VENDOR_VERSION, 'vendor_db_version' );
}
if ( get_option( 'woocommerce_version' ) !== false ) {
	_deprecated_function( 'woocommerce_version', VENDOR_VERSION, 'vendor_version' );
}
//if (current_user_can( 'manage_woocommerce' )) {
//	_deprecated_function( 'manage_woocommerce', VENDOR_VERSION, 'manage_vendor' );
//}
if ( get_option( '_wc_needs_update' ) !== false ) {
	_deprecated_function( '_wc_needs_update', VENDOR_VERSION, '_vendor_needs_update' );
}

if ( get_option( '_wc_needs_pages' ) !== false ) {
	_deprecated_function( '_wc_needs_pages', VENDOR_VERSION, '_vendor_needs_pages' );
}
if ( get_option( '_wc_activation_redirect' ) !== false ) {
	_deprecated_function( '_wc_activation_redirect', VENDOR_VERSION, '_vendor_activation_redirect' );
}
if ( isset( $_GET['preview_woocommerce_mail'] ) ) {
	_deprecated_function( 'preview_woocommerce_mail', VENDOR_VERSION, 'preview_vendor_mail' );
}

/**
 * Filters on data used in admin and frontend
 */
add_filter( 'woocommerce_coupon_code', 'sanitize_text_field' );
add_filter( 'woocommerce_coupon_code', 'strtolower' ); // Coupons case-insensitive by default
add_filter( 'woocommerce_stock_amount', 'intval' ); // Stock amounts are integers by default

/**
 * Short Description (excerpt)
 */
add_filter( 'woocommerce_short_description', 'wptexturize' );
add_filter( 'woocommerce_short_description', 'convert_smilies' );
add_filter( 'woocommerce_short_description', 'convert_chars' );
add_filter( 'woocommerce_short_description', 'wpautop' );
add_filter( 'woocommerce_short_description', 'shortcode_unautop' );
add_filter( 'woocommerce_short_description', 'prepend_attachment' );
add_filter( 'woocommerce_short_description', 'do_shortcode', 11 ); // AFTER wpautop()

/*
 * includes/wc-core-functions.php
 */
function wc_get_template_part( $slug, $name = '' ) {
	_deprecated_function( 'wc_get_template_part', VENDOR_VERSION, 'Vendor()->template()->get_template_part()' );
}
function wc_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	_deprecated_function( 'wc_get_template', VENDOR_VERSION, 'Vendor()->template()->get_template()' );
	//return Vendor()->template()->get_template( $template_name, $args, $template_path, $default_path );
}
function wc_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	_deprecated_function( 'wc_locate_template', VENDOR_VERSION, 'Vendor()->template()->locate_template()' );
	//return Vendor()->template()->locate_template( $template_name, $template_path, $default_path );
}
function get_woocommerce_currency() {
	_deprecated_function( 'get_woocommerce_currency', VENDOR_VERSION );
}
function get_woocommerce_currencies() {
	_deprecated_function( 'get_woocommerce_currencies', VENDOR_VERSION );
}
function get_woocommerce_currency_symbol( $currency = '' ) {
	_deprecated_function( 'get_woocommerce_currency_symbol', VENDOR_VERSION );
}
function wc_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
	_deprecated_function( 'wc_mail', VENDOR_VERSION );
}
function wc_get_image_size( $image_size ) {
	_deprecated_function( 'wc_get_image_size', VENDOR_VERSION, 'vendor_get_image_size' );
	return vendor_get_image_size( $image_size );
}
function get_woocommerce_api_url( $path ) {
	_deprecated_function( 'get_woocommerce_api_url', VENDOR_VERSION );
}

/*
 * includes/wc-conditional-functions.php
 */
function is_woocommerce() {
	_deprecated_function( 'is_woocommerce', VENDOR_VERSION, 'is_vendor' );
	return is_vendor();
}
function is_shop() {
	_deprecated_function( 'is_shop', VENDOR_VERSION, 'is_vendor_archive' );
	return is_vendor_archive();
}
function is_product_taxonomy() {
	_deprecated_function( 'is_product_taxonomy', VENDOR_VERSION, 'is_vendor_taxonomy' );
	return is_vendor_taxonomy();
}
function is_product_category( $term = '' ) {
	_deprecated_function( 'is_product_category', VENDOR_VERSION, 'is_vendor_category' );
	return is_vendor_category( $term = '' );
}
function is_product_tag( $term = '' ) {
	_deprecated_function( 'is_product_tag', VENDOR_VERSION, 'is_vendor_tag' );
	return is_vendor_tag( $term = '' );
}
function is_product() {
	_deprecated_function( 'is_product', VENDOR_VERSION, 'is_vendor_singular' );
	return is_vendor_singular();
}
function is_cart() {
	_deprecated_function( 'is_cart', VENDOR_VERSION );
}
function is_checkout() {
	_deprecated_function( 'is_checkout', VENDOR_VERSION );
}
function is_checkout_pay_page() {
	_deprecated_function( 'is_checkout_pay_page', VENDOR_VERSION );
}
function is_account_page() {
	_deprecated_function( 'is_account_page', VENDOR_VERSION );
}
function is_order_received_page() {
	_deprecated_function( 'is_order_received_page', VENDOR_VERSION );
}
function is_add_payment_method_page() {
	_deprecated_function( 'is_add_payment_method_page', VENDOR_VERSION );
}
function is_store_notice_showing() {
	_deprecated_function( 'is_store_notice_showing', VENDOR_VERSION );
}
function is_filtered() {
	_deprecated_function( 'is_filtered', VENDOR_VERSION );
}
function taxonomy_is_product_attribute( $name ) {
	_deprecated_function( 'taxonomy_is_product_attribute', VENDOR_VERSION );
}
function meta_is_product_attribute( $name, $value, $product_id ) {
	_deprecated_function( 'meta_is_product_attribute', VENDOR_VERSION );
}

/*
 * includes/class-vendor-frontend-scripts.php
 */
//if ( file_exists ( Vendor()->plugin_dir_url() . '/css/woocommerce.css' ) ) {
//	_deprecated_function( 'woocommerce.css', VENDOR_VERSION, 'vendor.css' );
//}

if (has_image_size( 'shop_thumbnail' )) {
	_deprecated_function( 'shop_thumbnail', VENDOR_VERSION, 'vendor_thumbnail' );
}
if (has_image_size( 'shop_catalog' )) {
	_deprecated_function( 'shop_catalog', VENDOR_VERSION, 'vendor_catalog' );
}
if (has_image_size( 'shop_single' )) {
	_deprecated_function( 'shop_single', VENDOR_VERSION, 'vendor_single' );
}

// Old defines from alpha here to make sure they are not used to issue warnings
if ( defined( 'VENDOR_THEME_SUPPORTS_VENDOR' ) ) {
	_deprecated_function( 'VENDOR_THEME_SUPPORTS_VENDOR', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_IMPORTERS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_IMPORTERS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_POST_TYPES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_POST_TYPES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_REPORTS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_REPORTS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_SETTINGS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_SETTINGS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_VIEWS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_VIEWS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_ASSETS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_ASSETS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_INCLUDES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_INCLUDES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_LANGUAGES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_LANGUAGES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_LOGS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_LOGS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_TEMPLATES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_TEMPLATES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_ADMIN_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_ADMIN_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_LIBRARIES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_LIBRARIES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_SHORTCODES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_SHORTCODES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_UPDATES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_UPDATES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_WALKERS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_WALKERS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_WIDGETS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_WIDGETS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_CSS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_CSS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_IMAGES_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_IMAGES_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_PATH' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_PATH', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_ASSETS_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_ASSETS_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_CSS_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_CSS_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_IMAGES_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_IMAGES_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_ADMIN_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_ADMIN_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_JQUERY_CHOSEN_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_JQUERY_CHOSEN_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_FRONTEND_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_FRONTEND_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_JQUERY_BLOCKUI_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_JQUERY_BLOCKUI_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_JQUERY_TIPTIP_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_JQUERY_TIPTIP_URL', VENDOR_VERSION );
}
if ( defined( 'VENDOR_PLUGIN_JS_JQUERY_PRETTYPHOTO_URL' ) ) {
	_deprecated_function( 'VENDOR_PLUGIN_JS_JQUERY_PRETTYPHOTO_URL', VENDOR_VERSION );
}
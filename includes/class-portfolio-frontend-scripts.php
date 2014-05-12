<?php
/**
 * Vendor_Frontend_Scripts
 */
class Vendor_Frontend_Scripts {

	/**
	 * Constructor
	 */
	public function __construct () {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		//deprecated_scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'deprecated_scripts' ) );
		add_action( 'wp_print_scripts', array( $this, 'check_jquery' ), 25 );
	}

	/**
	 * Get styles for the frontend
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'woocommerce_enqueue_styles', array(
			'vendor-layout' => array(
				'src'     => Vendor()->plugin_dir_url() . '/css/vendor-layout.css',
				'deps'    => '',
				'version' => VENDOR_VERSION,
				'media'   => 'all'
			),
			'vendor-smallscreen' => array(
				'src'     => Vendor()->plugin_dir_url() . '/css/vendor-smallscreen.css',
				'deps'    => 'vendor-layout',
				'version' => VENDOR_VERSION,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')'
			),
			'vendor-general' => array(
				'src'     => Vendor()->plugin_dir_url() . '/css/vendor.css',
				'deps'    => '',
				'version' => VENDOR_VERSION,
				'media'   => 'all'
			),
		) );
	}

	/**
	 * Register/queue frontend scripts.
	 *
	 * @access public
	 * @return void
	 */
	public function load_scripts() {
		global $post, $wp;

		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$lightbox_en          = get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? true : false;

		// Register any scripts for later use, or used as dependencies
		wp_register_script( 'jquery-chosen', Vendor()->plugin_dir_url() . '/js/jquery-chosen/chosen.jquery' . $suffix . '.js', array( 'jquery' ), '0.9.14', true );
		wp_register_script( 'jquery-blockui', Vendor()->plugin_dir_url() . '/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.60', true );

		wp_register_script( 'vendor-single-post', Vendor()->plugin_dir_url() . '/js/frontend/single-post' . $suffix . '.js', array( 'jquery' ), VENDOR_VERSION, true );

		// Queue frontend scripts conditionally

		//@todo maybe remove this if we don't need this on frontend
		//if ( is_checkout() ) {

			if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) {
				wp_enqueue_script( 'vendor-chosen', Vendor()->plugin_dir_url() . '/js/frontend/chosen-frontend' . $suffix . '.js', array( 'jquery-chosen' ), VENDOR_VERSION, true );
				wp_enqueue_style( 'woocommerce_chosen_styles', Vendor()->plugin_dir_url() . '/css/chosen.css' );
			}

		//}

		if ( $lightbox_en && ( is_vendor_singular() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) ) {
			wp_enqueue_script( 'jquery-prettyPhoto', Vendor()->plugin_dir_url() . '/js/jquery-prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
			wp_enqueue_script( 'vendor-pretty-photo', Vendor()->plugin_dir_url() . '/js/jquery-prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery' ), VENDOR_VERSION, true );
			wp_enqueue_style( 'woocommerce_prettyPhoto_css', Vendor()->plugin_dir_url() . '/css/prettyPhoto.css' );
		}

		if ( is_vendor_singular() )
			wp_enqueue_script( 'vendor-single-post' );

		// Global frontend scripts
		wp_enqueue_script( 'vendor', Vendor()->plugin_dir_url() . '/js/frontend/vendor' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), VENDOR_VERSION, true );

		// Variables for JS scripts
		wp_localize_script( 'vendor', 'woocommerce_params', apply_filters( 'woocommerce_params', array(
			'ajax_url'        => Vendor()->ajax_url(),
			'ajax_loader_url' => apply_filters( 'woocommerce_ajax_loader_url', Vendor()->plugin_dir_url() . '/images/ajax-loader@2x.gif' ),
		) ) );

		wp_localize_script( 'vendor-single-post', 'wc_single_product_params', apply_filters( 'wc_single_product_params', array(
			'i18n_required_rating_text' => esc_attr__( 'Please select a rating', VENDOR_UNIQUE_IDENTIFIER ),
			'review_rating_required'    => get_option( 'woocommerce_review_rating_required' ),
		) ) );

		// CSS Styles
		$enqueue_styles = $this->get_styles();

		if ( $enqueue_styles )
			foreach ( $enqueue_styles as $handle => $args )
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
	}

	/**
	 * WC requires jQuery 1.7 since it uses functions like .on() for events.
	 * If, by the time wp_print_scrips is called, jQuery is outdated (i.e not
	 * using the version in core) we need to deregister it and register the
	 * core version of the file.
	 *
	 * @access public
	 * @return void
	 */
	public function check_jquery() {
		global $wp_scripts;

		// Enforce minimum version of jQuery
		if ( ! empty( $wp_scripts->registered['jquery']->ver ) && ! empty( $wp_scripts->registered['jquery']->src ) && 0 >= version_compare( $wp_scripts->registered['jquery']->ver, '1.7' ) ) {
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', array(), '1.7' );
			wp_enqueue_script( 'jquery' );
		}
	}

	public function deprecated_scripts() {
		global $wp_scripts;
/*
		echo "<pre>";
		echo print_r($wp_scripts, true);
		echo "</pre>";
*/
/*
wp_register_script
wp_deregister_script
wp_enqueue_script
*/
/*
//'chosen'
#'jquery-payment'
#'wc-credit-card-form'
#'wc-add-to-cart-variation'
#'wc-single-product'
#'wc-country-select'
#'wc-address-i18n'
#'jquery-cookie'

#'wc-add-to-cart'
#'wc-cart'
#'wc-chosen'
#'wc-checkout'
#'wc-add-payment-method'
#'prettyPhoto'
#'prettyPhoto-init'
#'woocommerce'
#'wc-cart-fragments'
*/
		// chosen
		if ( ! empty( $wp_scripts->registered['chosen']->ver ) && ! empty( $wp_scripts->registered['chosen']->src ) ) {
			_deprecated_function( 'chosen', VENDOR_VERSION, 'jquery-chosen' );
		}
		// jquery-payment
		if ( ! empty( $wp_scripts->registered['jquery-payment']->ver ) && ! empty( $wp_scripts->registered['jquery-payment']->src ) ) {
			_deprecated_function( 'jquery-payment', VENDOR_VERSION );
		}
		// wc-credit-card-form
		if ( ! empty( $wp_scripts->registered['wc-credit-card-form']->ver ) && ! empty( $wp_scripts->registered['wc-credit-card-form']->src ) ) {
			_deprecated_function( 'wc-credit-card-form', VENDOR_VERSION );
		}
		// wc-add-to-cart-variation
		if ( ! empty( $wp_scripts->registered['wc-add-to-cart-variation']->ver ) && ! empty( $wp_scripts->registered['wc-add-to-cart-variation']->src ) ) {
			_deprecated_function( 'wc-add-to-cart-variation', VENDOR_VERSION );
		}
		// wc-single-product
		if ( ! empty( $wp_scripts->registered['wc-single-product']->ver ) && ! empty( $wp_scripts->registered['wc-single-product']->src ) ) {
			_deprecated_function( 'wc-single-product', VENDOR_VERSION, 'vendor-single-post' );
		}
		// wc-country-select
		if ( ! empty( $wp_scripts->registered['wc-country-select']->ver ) && ! empty( $wp_scripts->registered['wc-country-select']->src ) ) {
			_deprecated_function( 'wc-country-select', VENDOR_VERSION );
		}
		// wc-address-i18n
		if ( ! empty( $wp_scripts->registered['wc-address-i18n']->ver ) && ! empty( $wp_scripts->registered['wc-address-i18n']->src ) ) {
			_deprecated_function( 'wc-address-i18n', VENDOR_VERSION );
		}
		// jquery-cookie
		if ( ! empty( $wp_scripts->registered['jquery-cookie']->ver ) && ! empty( $wp_scripts->registered['jquery-cookie']->src ) ) {
			_deprecated_function( 'jquery-cookie', VENDOR_VERSION );
		}
		// wc-add-to-cart
		if ( ! empty( $wp_scripts->registered['wc-add-to-cart']->ver ) && ! empty( $wp_scripts->registered['wc-add-to-cart']->src ) ) {
			_deprecated_function( 'wc-add-to-cart', VENDOR_VERSION );
		}
		// wc-cart
		if ( ! empty( $wp_scripts->registered['wc-cart']->ver ) && ! empty( $wp_scripts->registered['wc-cart']->src ) ) {
			_deprecated_function( 'wc-cart', VENDOR_VERSION );
		}
		// wc-chosen
		if ( ! empty( $wp_scripts->registered['wc-chosen']->ver ) && ! empty( $wp_scripts->registered['wc-chosen']->src ) ) {
			_deprecated_function( 'wc-chosen', VENDOR_VERSION, 'vendor-chosen' );
		}
		// wc-checkout
		if ( ! empty( $wp_scripts->registered['wc-checkout']->ver ) && ! empty( $wp_scripts->registered['wc-checkout']->src ) ) {
			_deprecated_function( 'wc-checkout', VENDOR_VERSION );
		}
		// wc-add-payment-method
		if ( ! empty( $wp_scripts->registered['wc-add-payment-method']->ver ) && ! empty( $wp_scripts->registered['wc-add-payment-method']->src ) ) {
			_deprecated_function( 'wc-add-payment-method', VENDOR_VERSION );
		}
		// prettyPhoto
		if ( ! empty( $wp_scripts->registered['prettyPhoto']->ver ) && ! empty( $wp_scripts->registered['prettyPhoto']->src ) ) {
			_deprecated_function( 'prettyPhoto', VENDOR_VERSION, 'jquery-prettyPhoto' );
		}
		// prettyPhoto-init
		if ( ! empty( $wp_scripts->registered['prettyPhoto-init']->ver ) && ! empty( $wp_scripts->registered['prettyPhoto-init']->src ) ) {
			_deprecated_function( 'prettyPhoto-init', VENDOR_VERSION, 'vendor-pretty-photo' );
		}
		// woocommerce
		if ( ! empty( $wp_scripts->registered['woocommerce']->ver ) && ! empty( $wp_scripts->registered['woocommerce']->src ) ) {
			_deprecated_function( 'woocommerce', VENDOR_VERSION, 'vendor' );
		}
		// wc-cart-fragments
		if ( ! empty( $wp_scripts->registered['wc-cart-fragments']->ver ) && ! empty( $wp_scripts->registered['wc-cart-fragments']->src ) ) {
			_deprecated_function( 'wc-cart-fragments', VENDOR_VERSION );
		}
	}
}

new Vendor_Frontend_Scripts();
<?php
/**
 * Template Loader
 *
 * @class 		WC_Template
 * @version		2.1.0
 * @package		WooCommerce/Classes
 * @category	Class
 * @author 		WooThemes
 */
class WC_Template_Loader {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		add_filter( 'comments_template', array( $this, 'comments_template_loader' ) );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. woocommerce looks for theme
	 * overrides in /theme/woocommerce/ by default
	 *
	 * For beginners, it also looks for a woocommerce.php template first. If the user adds
	 * this to the theme (containing a woocommerce() inside) this will be used for all
	 * woocommerce templates.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {
		$find = array( 'woocommerce.php' );
		$file = '';

		if ( is_single() && get_post_type() == 'product' ) {

			$file 	= 'single-product.php';
			$find[] = $file;
			$find[] = Vendor()->plugin_template_path() . $file;

		} elseif ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {

			$term = get_queried_object();

			$file 		= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= Vendor()->plugin_template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= $file;
			$find[] 	= Vendor()->plugin_template_path() . $file;

		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) {

			$file 	= 'archive-product.php';
			$find[] = $file;
			$find[] = Vendor()->plugin_template_path() . $file;

		}

		if ( $file ) {
			$template       = locate_template( $find );
			if ( ! $template )
				$template = Vendor()->plugin_dir_path() . '/templates/' . $file;
		}

		return $template;
	}

	/**
	 * comments_template_loader function.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public function comments_template_loader( $template ) {
		if ( get_post_type() !== 'product' )
			return $template;

		if ( file_exists( STYLESHEETPATH . '/' . Vendor()->plugin_template_path() . 'single-product-reviews.php' ))
			return STYLESHEETPATH . '/' . Vendor()->plugin_template_path() . 'single-product-reviews.php';
		elseif ( file_exists( TEMPLATEPATH . '/' . Vendor()->plugin_template_path() . 'single-product-reviews.php' ))
			return TEMPLATEPATH . '/' . Vendor()->plugin_template_path() . 'single-product-reviews.php';
		elseif ( file_exists( STYLESHEETPATH . '/' . 'single-product-reviews.php' ))
			return STYLESHEETPATH . '/' . 'single-product-reviews.php';
		elseif ( file_exists( TEMPLATEPATH . '/' . 'single-product-reviews.php' ))
			return TEMPLATEPATH . '/' . 'single-product-reviews.php';
		else
			return Vendor()->plugin_dir_path() . '/templates/single-product-reviews.php';
	}
}

new WC_Template_Loader();
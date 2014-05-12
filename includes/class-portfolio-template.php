<?php
/**
 * Installation related functions and actions.
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Classes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Template' ) ) {

	/**
     * Vendor_Template Class
     */
	class Vendor_Template
	{
		/**
         * Get template part (for templates like the shop-loop).
         *
         * @access public
         * @param mixed $slug
         * @param string $name (default: '')
         * @return void
         */
		function get_template_part( $slug, $name = '' ) {
			$template = '';

			// Look in yourtheme/slug-name.php and yourtheme/woocommerce/slug-name.php
			if ( $name )
				$template = locate_template( array ( "{$slug}-{$name}.php", Vendor()->plugin_template_path() . "{$slug}-{$name}.php" ) );

			// Get default slug-name.php
			if ( !$template && $name && file_exists( Vendor()->plugin_dir_path() . "/templates/{$slug}-{$name}.php" ) )
				$template = Vendor()->plugin_dir_path() . "/templates/{$slug}-{$name}.php";

			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/{$plugin_name}/slug.php
			if ( !$template )
				$template = locate_template( array ( "{$slug}.php", Vendor()->plugin_template_path() . "{$slug}.php" ) );

			if ( $template )
				load_template( $template, false );
		}

		/**
         * Get other templates (e.g. product attributes) passing attributes and including the file.
         *
         * @access public
         * @param mixed $template_name
         * @param array $args (default: array())
         * @param string $template_path (default: '')
         * @param string $default_path (default: '')
         * @return void
         */
		function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
			if ( $args && is_array($args) )
				extract( $args );

			$located = $this->locate_template( $template_name, $template_path, $default_path );

			do_action( 'vendor_before_template_part', $template_name, $template_path, $located, $args );

			include( $located );

			do_action( 'vendor_after_template_part', $template_name, $template_path, $located, $args );
		}

		/**
         * Locate a template and return the path for inclusion.
         *
         * This is the load order:
         *
         *		yourtheme		/	$template_path	/	$template_name
         *		yourtheme		/	$template_name
         *		$default_path	/	$template_name
         *
         * @access public
         * @param mixed $template_name
         * @param string $template_path (default: '')
         * @param string $default_path (default: '')
         * @return string
		 * @todo remove template_path, default_path with defines
         */
		function locate_template( $template_name, $template_path = '', $default_path = '' ) {
			if ( ! $template_path ) $template_path = Vendor()->plugin_template_path();
			if ( ! $default_path ) $default_path = Vendor()->plugin_dir_path() . '/templates/';

			// Look within passed path within the theme - this is priority
			$template = locate_template(
				array(
					trailingslashit( $template_path ) . $template_name,
						$template_name
				)
			);

			// Get default template
			if ( ! $template )
				$template = $default_path . $template_name;

			// Return what we found
			return apply_filters('vendor_locate_template', $template, $template_name, $template_path);
		}
	}
}

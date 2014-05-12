<?php
/**
 * WooCommerce General Settings
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Settings_General' ) ) :

/**
 * Vendor_Admin_Settings_General
 */
class Vendor_Settings_General extends Vendor_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', VENDOR_UNIQUE_IDENTIFIER );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

		if ( ( $styles = Vendor_Frontend_Scripts::get_styles() ) && array_key_exists( 'woocommerce-general', $styles ) )
			add_action( 'woocommerce_admin_field_frontend_styles', array( $this, 'frontend_styles_setting' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'woocommerce_general_settings', array(

			array( 'title' => __( 'General Options', VENDOR_UNIQUE_IDENTIFIER ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),

			array(
				'title' 	=> __( 'Base Location', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> __( 'This is the base location for your business. Tax rates will be based on this country.', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_default_country',
				'css' 		=> 'min-width:350px;',
				'default'	=> 'GB',
				'type' 		=> 'single_select_country',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Selling Location(s)', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> __( 'This option lets you limit which countries you are willing to sell to.', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_allowed_countries',
				'default'	=> 'all',
				'type' 		=> 'select',
				'class'		=> 'chosen_select',
				'css' 		=> 'min-width: 350px;',
				'desc_tip'	=>  true,
				'options' => array(
					'all'      => __( 'Sell to all countries', VENDOR_UNIQUE_IDENTIFIER ),
					'specific' => __( 'Sell to specific countries only', VENDOR_UNIQUE_IDENTIFIER )
				)
			),

			array(
				'title' => __( 'Specific Countries', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> '',
				'id' 		=> 'woocommerce_specific_allowed_countries',
				'css' 		=> 'min-width: 350px;',
				'default'	=> '',
				'type' 		=> 'multi_select_countries'
			),

			array(
				'title' => __( 'Store Notice', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> __( 'Enable site-wide store notice text', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_demo_store',
				'default'	=> 'no',
				'type' 		=> 'checkbox'
			),

			array(
				'title' => __( 'Store Notice Text', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> '',
				'id' 		=> 'woocommerce_demo_store_notice',
				'default'	=> __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', VENDOR_UNIQUE_IDENTIFIER ),
				'type' 		=> 'text',
				'css' 		=> 'min-width:300px;',
				'autoload'  => false
			),

			array(
				'title'   => __( 'API', VENDOR_UNIQUE_IDENTIFIER ),
				'desc'    => __( 'Enable the REST API', VENDOR_UNIQUE_IDENTIFIER ),
				'id'      => 'woocommerce_api_enabled',
				'type'    => 'checkbox',
				'default' => 'yes',
			),

			array( 'type' => 'sectionend', 'id' => 'general_options'),

			array(	'title' => __( 'Currency Options', VENDOR_UNIQUE_IDENTIFIER ), 'type' => 'title', 'desc' => __( 'The following options affect how prices are displayed on the frontend.', VENDOR_UNIQUE_IDENTIFIER ), 'id' => 'pricing_options' ),

			array(
				'title' => __( 'Thousand Separator', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> __( 'This sets the thousand separator of displayed prices.', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_price_thousand_sep',
				'css' 		=> 'width:50px;',
				'default'	=> ',',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Decimal Separator', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> __( 'This sets the decimal separator of displayed prices.', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_price_decimal_sep',
				'css' 		=> 'width:50px;',
				'default'	=> '.',
				'type' 		=> 'text',
				'desc_tip'	=>  true,
			),

			array(
				'title' => __( 'Number of Decimals', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 		=> __( 'This sets the number of decimal points shown in displayed prices.', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_price_num_decimals',
				'css' 		=> 'width:50px;',
				'default'	=> '2',
				'desc_tip'	=>  true,
				'type' 		=> 'number',
				'custom_attributes' => array(
					'min' 	=> 0,
					'step' 	=> 1
				)
			),

			array( 'type' => 'sectionend', 'id' => 'pricing_options' ),

			array(	'title' => __( 'Styles and Scripts', VENDOR_UNIQUE_IDENTIFIER ), 'type' => 'title', 'id' => 'script_styling_options' ),

			array( 'type' 		=> 'frontend_styles' ),

			array(
				'title' => __( 'Scripts', VENDOR_UNIQUE_IDENTIFIER ),
				'desc' 	=> __( 'Enable Lightbox', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_enable_lightbox',
				'default'	=> 'yes',
				'desc_tip'	=> __( 'Include WooCommerce\'s lightbox. Product gallery images will open in a lightbox.', VENDOR_UNIQUE_IDENTIFIER ),
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> 'start'
			),

			array(
				'desc' 		=> __( 'Enable enhanced country select boxes', VENDOR_UNIQUE_IDENTIFIER ),
				'id' 		=> 'woocommerce_enable_chosen',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> 'end',
				'desc_tip'	=> __( 'This will enable a script allowing the country fields to be searchable.', VENDOR_UNIQUE_IDENTIFIER ),
				'autoload'  => false
			),

			array( 'type' => 'sectionend', 'id' => 'script_styling_options' ),

		)); // End general settings
	}

	/**
	 * Output the frontend styles settings.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_styles_setting() {
		?><tr valign="top" class="woocommerce_frontend_css_colors">
			<th scope="row" class="titledesc">
				<?php _e( 'Frontend Styles', VENDOR_UNIQUE_IDENTIFIER ); ?>
			</th>
		    <td class="forminp"><?php

				$base_file		= Vendor()->plugin_dir_path() . '/assets/css/vendor-base.less';
				$css_file		= Vendor()->plugin_dir_path() . '/assets/css/vendor.css';

				if ( is_writable( $base_file ) && is_writable( $css_file ) ) {

					// Get settings
					$colors = array_map( 'esc_attr', (array) get_option( 'woocommerce_frontend_css_colors' ) );

					// Defaults
					if ( empty( $colors['primary'] ) ) $colors['primary'] = '#ad74a2';
					if ( empty( $colors['secondary'] ) ) $colors['secondary'] = '#f7f6f7';
					if ( empty( $colors['highlight'] ) ) $colors['highlight'] = '#85ad74';
					if ( empty( $colors['content_bg'] ) ) $colors['content_bg'] = '#ffffff';
		            if ( empty( $colors['subtext'] ) ) $colors['subtext'] = '#777777';

					// Show inputs
		    		$this->color_picker( __( 'Primary', VENDOR_UNIQUE_IDENTIFIER ), 'woocommerce_frontend_css_primary', $colors['primary'], __( 'Call to action buttons/price slider/layered nav UI', VENDOR_UNIQUE_IDENTIFIER ) );
		    		$this->color_picker( __( 'Secondary', VENDOR_UNIQUE_IDENTIFIER ), 'woocommerce_frontend_css_secondary', $colors['secondary'], __( 'Buttons and tabs', VENDOR_UNIQUE_IDENTIFIER ) );
		    		$this->color_picker( __( 'Highlight', VENDOR_UNIQUE_IDENTIFIER ), 'woocommerce_frontend_css_highlight', $colors['highlight'], __( 'Price labels and Sale Flashes', VENDOR_UNIQUE_IDENTIFIER ) );
		    		$this->color_picker( __( 'Content', VENDOR_UNIQUE_IDENTIFIER ), 'woocommerce_frontend_css_content_bg', $colors['content_bg'], __( 'Your themes page background - used for tab active states', VENDOR_UNIQUE_IDENTIFIER ) );
		    		$this->color_picker( __( 'Subtext', VENDOR_UNIQUE_IDENTIFIER ), 'woocommerce_frontend_css_subtext', $colors['subtext'], __( 'Used for certain text and asides - breadcrumbs, small text etc.', VENDOR_UNIQUE_IDENTIFIER ) );

		    	} else {
		    		echo '<span class="description">' . __( 'To edit colours <code>assets/css/vendor-base.less</code> and <code>vendor.css</code> need to be writable. See <a href="http://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.', VENDOR_UNIQUE_IDENTIFIER ) . '</span>';
		    	}

		    ?></td>
		</tr><?php
	}

	/**
	 * Output a colour picker input box.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $id
	 * @param mixed $value
	 * @param string $desc (default: '')
	 * @return void
	 */
	function color_picker( $name, $id, $value, $desc = '' ) {
		echo '<div class="color_box"><strong><img class="help_tip" data-tip="' . esc_attr( $desc ) . '" src="' . Vendor()->plugin_dir_url() . '/images/help.png" height="16" width="16" /> ' . esc_html( $name ) . '</strong>
	   		<input name="' . esc_attr( $id ). '" id="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $value ) . '" class="colorpick" /> <div id="colorPickerDiv_' . esc_attr( $id ) . '" class="colorpickdiv"></div>
	    </div>';
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();

		Vendor_Admin_Settings::save_fields( $settings );

		if ( isset( $_POST['woocommerce_frontend_css_primary'] ) ) {

			// Save settings
			$primary 		= ( ! empty( $_POST['woocommerce_frontend_css_primary'] ) ) ? wc_format_hex( $_POST['woocommerce_frontend_css_primary'] ) : '';
			$secondary 		= ( ! empty( $_POST['woocommerce_frontend_css_secondary'] ) ) ? wc_format_hex( $_POST['woocommerce_frontend_css_secondary'] ) : '';
			$highlight 		= ( ! empty( $_POST['woocommerce_frontend_css_highlight'] ) ) ? wc_format_hex( $_POST['woocommerce_frontend_css_highlight'] ) : '';
			$content_bg 	= ( ! empty( $_POST['woocommerce_frontend_css_content_bg'] ) ) ? wc_format_hex( $_POST['woocommerce_frontend_css_content_bg'] ) : '';
			$subtext 		= ( ! empty( $_POST['woocommerce_frontend_css_subtext'] ) ) ? wc_format_hex( $_POST['woocommerce_frontend_css_subtext'] ) : '';

			$colors = array(
				'primary' 		=> $primary,
				'secondary' 	=> $secondary,
				'highlight' 	=> $highlight,
				'content_bg' 	=> $content_bg,
				'subtext' 		=> $subtext
			);

			$old_colors = get_option( 'woocommerce_frontend_css_colors' );
			update_option( 'woocommerce_frontend_css_colors', $colors );

			if ( $old_colors != $colors )
				woocommerce_compile_less_styles();
		}
	}

}

endif;

return new Vendor_Settings_General();

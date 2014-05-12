<?php
/**
 * Plugin Name: Dazzle Software Vendor
 * Plugin URI: http://www.dazzlesoftware.org/vendor/
 * Description: An Extendable vendor toolkit that helps you display your projects on your WordPress site.
 * Version: 1.0.1
 * Author: Dazzle Software
 * Author URI: http://dazzlesoftware.org
 * Requires at least: 3.8
 * Tested up to: 3.8
 *
 * Text Domain: vendor
 * Domain Path: /languages/
 *
 * @package Vendor
 * @category Core
 * @author Dazzle Software
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Vendor' ) ) {

    /**
     * Main Vendor Class
     *
     * @class Vendor
     * @version 1.0.0
     */
    /*final */class Vendor
    {
        /**
         * @var string
         */
		public $image_thumbnail = null;

        /**
         * @var string
         */
		public $image_catalog = null;

		/**
         * @var string
         */
		public $image_single = null;

        /**
         * @var string
         */
        public $options = array(
			'custom_fields' => array(
				'featured' => '_featured',//VENDOR_CUSTOM_FIELDS_FEATURED
			),
			'github' => array(
				'changelogs' => 'http://plugins.svn.wordpress.org/woocommerce/trunk/changelog.txt',
				'contributors' => 'https://api.github.com/repos/woothemes/woocommerce/contributors',
				'readme' => 'http://plugins.svn.wordpress.org/woocommerce/trunk/readme.txt',
			),
            'image_size' => array(
                'thumbnail' => 'vendor_thumbnail',
                'catalog' => 'vendor_catalog',
                'single' => 'vendor_single',
            ),
			'taxonomies' => array(
				'category' => 'vendor_category',
				'tag' => 'vendor_tag',
				//'type' => 'vendor-type' // website type for vendor version of this plugin
			),
	        'capabilities'			=> array(
	            'manage_terms' 		=> 'manage_product_terms',
				'edit_terms' 		=> 'edit_product_terms',
				'delete_terms' 		=> 'delete_product_terms',
				'assign_terms' 		=> 'assign_product_terms',
	        ),
            'unique_identifier' => 'vendor'
        );

		public $post_types = array(
			'vendor'
		);

		public $template_path = 'vendor';

		public $theme_supports = array(
			'vendor'
		);
		//@rewrite this
		public $screen_ids = array(
			'edit-vendor',
			'vendor',
			'edit-vendor_category',
			'edit-vendor_tag',
		);

        /**
         * @var string
         */
        public $version = '1.0.1';

        /**
         * @var Vendor The single instance of the class
         * @since 1.0
         */
        protected static $_instance = null;

        /**
         * @var Vendor_Logger The single instance of the logger class
         * @since 1.0
         */
        protected static $_logger = null;

        /**
         * @var Vendor_Mailer The single instance of the mailer class
         * @since 1.0
         */
        protected static $_mailer = null;

        /**
         * @var Vendor_Template The single instance of the template class
         * @since 1.0
         */
        protected static $_template = null;

        /**
         * Cloning is forbidden.
         *
         * @since 1.0.1
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), VENDOR_VERSION );
        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0.1
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), VENDOR_VERSION );
        }

        /**
         * WooCommerce Constructor.
         * @access public
         * @return WooCommerce
         */
        public function __construct() {
            // Auto-load classes on demand
            if ( function_exists( "__autoload" ) ) {
                spl_autoload_register( "__autoload" );
            }

            spl_autoload_register( array( $this, 'autoload' ) );

            // Define constants
            $this->define_constants();

            // Include required files
            $this->includes();

			// Hooks
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
			add_action( 'in_plugin_update_message-' . plugin_basename( __FILE__ ), array( 'Vendor_Install', 'in_plugin_update_message' ) );
			add_action( 'widgets_init', array( $this, 'include_widgets' ) );
			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'init', array( $this, 'include_template_functions' ) );
			//add_action( 'init', array( 'Vendor_Shortcodes', 'init' ) );
            add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
			// Loaded action
			do_action( 'vendor_loaded' );
        }

		/**
	     * Show action links on the plugin screen
	     *
	     * @param mixed $links
	     * @return array
	     */
		public function action_links( $links ) {
			return array_merge( array(
				'<a href="' . admin_url( 'options-general.php?page=vendor-settings' ) . '">' . __( 'Settings', VENDOR_UNIQUE_IDENTIFIER ) . '</a>',
				'<a href="' . esc_url( apply_filters( 'vendor_docs_url', 'http://docs.woothemes.com/documentation/plugins/woocommerce/', VENDOR_UNIQUE_IDENTIFIER ) ) . '">' . __( 'Documentation', VENDOR_UNIQUE_IDENTIFIER ) . '</a>',
				'<a href="' . esc_url( apply_filters( 'vendor_support_url', 'http://support.woothemes.com/' ) ) . '">' . __( 'Support', VENDOR_UNIQUE_IDENTIFIER ) . '</a>',
			), $links );
		}

        /**
         * Auto-load classes on demand to reduce memory consumption.
         *
         * @param mixed $class
         * @return void
         * @todo rewrite includes for defines
         */
        public function autoload( $class ) {

            $class = strtolower( $class );

            if ( strpos( $class, 'vendor_' ) === 0 ) {
				$path = $this->plugin_dir_path() . '/includes/';
                $file = 'class-' . str_replace( '_', '-', $class ) . '.php';
                if ( is_readable( $path . $file ) ) {
                    include_once( $path . $file );
                    return;
                }
            }
			elseif ( strpos( $class, 'vendor_shortcode_' ) === 0 ) {
				$path = $this->plugin_dir_path() . '/includes/shortcodes/';
				$file = 'class-' . str_replace( '_', '-', $class ) . '.php';
				if ( is_readable( $path . $file ) ) {
					include_once( $path . $file );
					return;
				}
			}
        }

        /**
         * Define Constants
         */
        private function define_constants() {

            if ( ! defined( 'VENDOR_UNIQUE_IDENTIFIER' ) ) {
                define( 'VENDOR_UNIQUE_IDENTIFIER', $this->options['unique_identifier'] );
            }

            if ( ! defined( 'VENDOR_PLUGIN_FILE' ) ) {
                define( 'VENDOR_PLUGIN_FILE', __FILE__ );
            }

            if ( ! defined( 'VENDOR_VERSION' ) ) {
                define( 'VENDOR_VERSION', $this->version );
            }

			foreach ( $this->options['custom_fields'] as $key => $value ) {
				if ( ! defined( 'VENDOR_CUSTOM_FIELDS_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ) ) ) {
					define( 'VENDOR_CUSTOM_FIELDS_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ), Vendor_Sanitize::sanitize_text( $value ) );
				}
			}

			foreach ( $this->options['github'] as $key => $value ) {
				if ( ! defined( 'VENDOR_GITHUB_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ) ) ) {
					define( 'VENDOR_GITHUB_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ), Vendor_Sanitize::sanitize_text( $value ) );
				}
			}

			foreach ( $this->options['image_size'] as $key => $value ) {
				if ( ! defined( 'VENDOR_IMAGE_SIZE_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ) ) ) {
					define( 'VENDOR_IMAGE_SIZE_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ), Vendor_Sanitize::sanitize_text( $value ) );
				}
			}

			foreach ( $this->post_types as $post_type ) {
				if ( ! defined( 'VENDOR_POST_TYPE_' . Vendor_Sanitize::sanitize_text_strtoupper( $post_type ) ) ) {
					define( 'VENDOR_POST_TYPE_' . Vendor_Sanitize::sanitize_text_strtoupper( $post_type ), Vendor_Sanitize::sanitize_text( $post_type ) );
				}
			}
			
			foreach ( $this->options['taxonomies'] as $key => $value ) {
				if ( ! defined( 'VENDOR_TAXONOMIES_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ) ) ) {
					define( 'VENDOR_TAXONOMIES_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ), Vendor_Sanitize::sanitize_text( $value ) );
				}
			}

			foreach ( $this->options['capabilities'] as $key => $value ) {
				if ( ! defined( 'VENDOR_CAPABILITIES_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ) ) ) {
					define( 'VENDOR_CAPABILITIES_' . Vendor_Sanitize::sanitize_text_strtoupper( $key ), Vendor_Sanitize::sanitize_text( $value ) );
				}
			}

			foreach ( $this->screen_ids as $screen_id ) {
				if ( ! defined( 'VENDOR_SCREEN_ID_' . Vendor_Sanitize::sanitize_text_strtoupper( $screen_id ) ) ) {
					define( 'VENDOR_SCREEN_ID_' . Vendor_Sanitize::sanitize_text_strtoupper( $screen_id ), Vendor_Sanitize::sanitize_text( $screen_id ) );
				}
			}
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        private function includes() {
			include( $this->plugin_dir_path() . '/includes/vendor-core-functions.php' );
			include( $this->plugin_dir_path() . '/includes/class-vendor-install.php' );
			include( $this->plugin_dir_path() . '/includes/class-vendor-comments.php' );

			if ( is_admin() ) {
				include_once( $this->plugin_dir_path() . '/includes/admin/class-vendor-admin.php' );
			}

			if ( defined('DOING_AJAX') ) {
				$this->ajax_includes();
			}

			if ( ! is_admin() || defined('DOING_AJAX') ) {
				$this->frontend_includes();
			}

			// Query class

			// Post types
			include_once( $this->plugin_dir_path() . '/includes/class-vendor-post-types.php' );						// Registers post types

			// Include abstract classes

			// Classes (used on all pages)
			include_once( $this->plugin_dir_path() . '/includes/class-vendor-post-types-factory.php' );				// Post Types Factory

			// Include template hooks in time for themes to remove/modify them
			include_once( $this->plugin_dir_path() . '/includes/vendor-template-hooks.php' );
        }

        /**
         * Include required ajax files.
         */
        public function ajax_includes() {
			include_once( $this->plugin_dir_path() . '/includes/class-vendor-ajax.php' );					// Ajax functions for admin and the front-end
        }

        /**
         * Include required frontend files.
         */
        public function frontend_includes() {
			include_once( $this->plugin_dir_path() . '/includes/class-vendor-template-loader.php' );			// Template Loader
			include_once( $this->plugin_dir_path() . '/includes/class-vendor-frontend-scripts.php' );		// Frontend Scripts
			include_once( $this->plugin_dir_path() . '/includes/class-vendor-shortcodes.php' );				// Shortcodes class
        }

        /**
         * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
         */
        public function include_template_functions() {
			include_once( $this->plugin_dir_path() . '/includes/vendor-template-functions.php' );
        }

        /**
         * Include core widgets
         */
        public function include_widgets() {
			include_once( $this->plugin_dir_path() . '/includes/class-vendor-widget-factory.php' );
        }

        /**
         * Init WooCommerce when WordPress Initialises.
         */
        public function init() {
			// Before init action
			do_action( 'before_vendor_init' );

			// Set up localisation
            $this->load_plugin_textdomain();

			// Init action
			do_action( 'vendor_init' );
        }

        /**
         * Load Localisation files.
         *
         * Note: the first-loaded translation file overrides any following ones if the same translation is present
         */
        public function load_plugin_textdomain() {
            $locale = apply_filters( 'vendor_plugin_locale', get_locale(), VENDOR_UNIQUE_IDENTIFIER );

            // Admin Locale
            if ( is_admin() ) {
                load_textdomain( VENDOR_UNIQUE_IDENTIFIER, $this->plugin_dir_path() . "/languages/vendor-admin-$locale.mo" );
            }

            // Frontend Locale
            load_textdomain( VENDOR_UNIQUE_IDENTIFIER, $this->plugin_dir_path() . "/languages/vendor-$locale.mo" );

            if ( apply_filters( 'vendor_load_alt_locale', false ) )
                load_plugin_textdomain( VENDOR_UNIQUE_IDENTIFIER, false, $this->plugin_dir_path() . "/languages/alt" );
            else
                load_plugin_textdomain( VENDOR_UNIQUE_IDENTIFIER, false, $this->plugin_dir_path() . "/languages" );
        }

        /**
         * Ensure theme and server variable compatibility and setup image sizes..
         */
        public function setup_environment() {
            // Post thumbnail support
			foreach ( $this->post_types as $post_type ) {
				if ( ! current_theme_supports( 'post-thumbnails', $post_type ) ) {
					add_theme_support( 'post-thumbnails' );
					remove_post_type_support( 'post', 'thumbnail' );
					remove_post_type_support( 'page', 'thumbnail' );
				} else {
					add_post_type_support( $post_type, 'thumbnail' );
				}
			}

            // Add image sizes
            $this->image_thumbnail = vendor_get_image_size( VENDOR_IMAGE_SIZE_THUMBNAIL );
            $this->image_catalog   = vendor_get_image_size( VENDOR_IMAGE_SIZE_CATALOG );
            $this->image_single    = vendor_get_image_size( VENDOR_IMAGE_SIZE_SINGLE );

            add_image_size( VENDOR_IMAGE_SIZE_THUMBNAIL, $this->image_thumbnail['width'], $this->image_thumbnail['height'], $this->image_thumbnail['crop'] );
            add_image_size( VENDOR_IMAGE_SIZE_CATALOG, $this->image_catalog['width'], $this->image_catalog['height'], $this->image_catalog['crop'] );
            add_image_size( VENDOR_IMAGE_SIZE_SINGLE, $this->image_single['width'], $this->image_single['height'], $this->image_single['crop'] );

            // IIS
            if ( ! isset($_SERVER['REQUEST_URI'] ) ) {
                $_SERVER['REQUEST_URI'] = substr( $_SERVER['PHP_SELF'], 1 );
                if ( isset( $_SERVER['QUERY_STRING'] ) ) {
                    $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
                }
            }

            // NGINX Proxy
            if ( ! isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];
            }

            if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_HTTPS'] ) ) {
                $_SERVER['HTTPS'] = $_SERVER['HTTP_HTTPS'];
            }

            // Support for hosts which don't use HTTPS, and use HTTP_X_FORWARDED_PROTO
            if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
                $_SERVER['HTTPS'] = '1';
            }
        }

        /** Helper functions ******************************************************/

		/**
	     * Get Ajax URL.
	     *
	     * @return string
	     */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
	     * Gets the URL (with trailing slash) for the plugin __FILE__ passed in.
	     *
	     * @return string
	     */
		public function plugin_dir_url() {
			return untrailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
	     * Gets the filesystem directory path (with trailing slash) for the file passed in.
	     *
	     * @return string
	     */
		public function plugin_dir_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function plugin_template_path() {
			return apply_filters( 'vendor_template_path', rtrim($this->template_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR );
		}

		/** Load Instances on demand **********************************************/

        /**
         * Main Vendor Instance
         *
         * Ensures only one instance of Vendor is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @see Vendor()
         * @return Vendor - Main instance
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Main Vendor Logger Instance
         *
         * Ensures only one instance is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @return Vendor_Logger
         */
        public static function logger() {
            if ( is_null( self::$_logger ) ) {
                self::$_logger = new Vendor_Logger();
            }
            return self::$_logger;
        }

        /**
         * Email Class.
         *
         * @return WC_Email
         */
		 //_mailer
		public function mailer() {
			//return WC_Emails::instance();
            if ( is_null( self::$_mailer ) ) {
                self::$_mailer = new Vendor_Emails();
            }
            return self::$_mailer;
		}

        /**
         * Main Vendor Template Instance
         *
         * Ensures only one instance is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @return Vendor_Template
         */
        public static function template() {
            if ( is_null( self::$_template ) ) {
                self::$_template = new Vendor_Template();
            }
            return self::$_template;
        }

		/** Deprecated methods *********************************************************/

		public function send_transactional_email() {
			_deprecated_function( 'send_transactional_email', $this->version );
		}
		public function prepare_directory( $directory = null, $mode = true) {
			_deprecated_function( 'prepare_directory', $this->version );
		}
		public function api_request_url( $request, $ssl = null ) {
			_deprecated_function( 'api_request_url', $this->version );
		}
		public function plugin_url() {
			_deprecated_function( 'plugin_url', $this->version, 'Vendor()->plugin_dir_url()' );
		}
		public function plugin_path() {
			_deprecated_function( 'plugin_path', $this->version, 'Vendor()->plugin_dir_path()' );
		}
		
		public function template_path() {
			_deprecated_function( 'template_path', $this->version, 'plugin_template_path()' );
		}
		public function get_image_size( $image_size ) {
			_deprecated_function( 'Vendor()->get_image_size', $this->version, 'vendor_get_image_size()' );
		}
		public function validation() {
			_deprecated_function( 'Vendor()->validation', $this->version );
		}
		public function setup_product_data( $post ) {
			_deprecated_function( 'Vendor()->setup_product_data', $this->version );
		}
		public function force_ssl( $content ) {
			_deprecated_function( 'Vendor()->force_ssl', $this->version );
		}
		public function clear_product_transients( $post_id = 0 ) {
			_deprecated_function( 'Vendor()->clear_product_transients', $this->version );
		}
		public function add_inline_js( $code ) {
			_deprecated_function( 'Vendor()->add_inline_js', $this->version );
		}
		public function nonce_field( $action, $referer = true , $echo = true ) {
			_deprecated_function( 'Vendor()->nonce_field', $this->version );
		}
		public function nonce_url( $action, $url = '' ) {
			_deprecated_function( 'Vendor()->nonce_url', $this->version );
		}
		public function verify_nonce( $action, $method = '_POST', $error_message = false ) {
			_deprecated_function( 'Vendor()->verify_nonce', $this->version );
		}
		//public function shortcode_wrapper( $function, $atts = array(), $wrapper = array( 'class' => 'woocommerce', 'before' => null, 'after' => null ) ) {
		//	_deprecated_function( 'Vendor()->shortcode_wrapper', $this->version );
		//}
		public function get_attribute_taxonomies() {
			_deprecated_function( 'Vendor()->get_attribute_taxonomies', $this->version );
		}
		public function attribute_taxonomy_name( $name ) {
			_deprecated_function( 'Vendor()->attribute_taxonomy_name', $this->version );
		}
		public function attribute_label( $name ) {
			_deprecated_function( 'Vendor()->attribute_label', $this->version );
		}
		public function attribute_orderby( $name ) {
			_deprecated_function( 'Vendor()->attribute_orderby', $this->version );
		}
		public function get_attribute_taxonomy_names() {
			_deprecated_function( 'Vendor()->get_attribute_taxonomy_names', $this->version );
		}
		public function get_coupon_discount_types() {
			_deprecated_function( 'Vendor()->get_coupon_discount_types', $this->version );
		}
		public function get_coupon_discount_type( $type = '' ) {
			_deprecated_function( 'Vendor()->get_coupon_discount_type', $this->version );
		}
		public function add_body_class( $class ) {
			_deprecated_function( 'Vendor()->add_body_class', $this->version );
		}
		public function output_body_class( $classes ) {
			_deprecated_function( 'Vendor()->output_body_class', $this->version );
		}
		public function add_error( $error ) {
			_deprecated_function( 'Vendor()->add_error', $this->version );
		}
		public function add_message( $message ) {
			_deprecated_function( 'Vendor()->add_message', $this->version );
		}
		public function clear_messages() {
			_deprecated_function( 'Vendor()->clear_messages', $this->version );
		}
		public function error_count() {
			_deprecated_function( 'Vendor()->error_count', $this->version );
		}
		public function message_count() {
			_deprecated_function( 'Vendor()->message_count', $this->version );
		}
		public function get_errors() {
			_deprecated_function( 'Vendor()->get_errors', $this->version );
		}
		public function get_messages() {
			_deprecated_function( 'Vendor()->get_messages', $this->version );
		}
		public function show_messages() {
			_deprecated_function( 'Vendor()->show_messages', $this->version );
		}
		public function set_messages() {
			_deprecated_function( 'Vendor()->set_messages', $this->version );
		}
		//public function checkout() {
		//	_deprecated_function( 'checkout', $this->version );
		//}
		public function payment_gateways() {
			_deprecated_function( 'payment_gateways', $this->version );
		}
		public function shipping() {
			_deprecated_function( 'shipping', $this->version );
		}
		//public function mailer() {
		//	_deprecated_function( 'mailer', $this->version );
		//}
    }
}

/**
 * Returns the main instance of Vendor to prevent the need to use globals.
 *
 * @since  1.0
 * @return Vendor
 */
function Vendor() {
    return Vendor::instance();
}

//add_action('init', 'Vendor');

/**
 * Init vendor class
 */
$GLOBALS['vendor'] = Vendor();
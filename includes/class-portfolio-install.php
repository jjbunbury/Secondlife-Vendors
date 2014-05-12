<?php
/**
 * Installation related functions and actions.
 *
 * @author 		Dazzle Software
 * @category 	Admin
 * @package 	Vendor/Classes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Vendor_Install' ) ) {

/**
 * Vendor_Install Class
 */
class Vendor_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'install_actions' ) );
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		register_activation_hook( VENDOR_PLUGIN_FILE , array( $this, 'install' ) );
	}

	/**
	 * check_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'vendor_version' ) != VENDOR_VERSION || get_option( 'vendor_db_version' ) != VENDOR_VERSION ) ) {
			$this->install();
		}
	}

	/**
	 * Install actions such as installing pages when a button is clicked.
	 */
	public function install_actions() {
		// Install - Add pages button
		if ( ! empty( $_GET['install_vendor_pages'] ) ) {

			self::create_pages();

			// We no longer need to install pages
			delete_option( '_vendor_needs_pages' );
			delete_transient( '_vendor_activation_redirect' );

			// What's new redirect
			wp_redirect( admin_url( 'index.php?page=vendor-about&vendor-installed=true' ) );
			exit;

		// Skip button
		} elseif ( ! empty( $_GET['skip_install_vendor_pages'] ) ) {

			// We no longer need to install pages
			delete_option( '_vendor_needs_pages' );
			delete_transient( '_vendor_activation_redirect' );

			// Flush rules after install
			flush_rewrite_rules();

			// What's new redirect
			wp_redirect( admin_url( 'index.php?page=vendor-about' ) );
			exit;

		// Update button
		} elseif ( ! empty( $_GET['do_update_vendor'] ) ) {

			$this->update();

			// Update complete
			delete_option( '_vendor_needs_pages' );
			delete_option( '_vendor_needs_update' );
			delete_transient( '_vendor_activation_redirect' );

			// What's new redirect
			wp_redirect( admin_url( 'index.php?page=vendor-about&vendor-updated=true' ) );
			exit;
		}
	}

	/**
	 * Install WC
	 */
	public function install() {

		$this->create_options();
		$this->create_tables();
		$this->create_roles();

		// Register post types
		$post_types = include( dirname( __FILE__ ) . '/class-vendor-post-types.php' );
		$post_types->register_taxonomies();

		$this->create_terms();
		$this->create_cron_jobs();
		$this->create_files();
		$this->create_css_from_less();

		// Clear transient cache
		wc_delete_product_transients();

		// Queue upgrades
		$current_version = get_option( 'vendor_version', null );
		$current_db_version = get_option( 'vendor_db_version', null );

		if ( version_compare( $current_db_version, '2.1.0', '<' ) && null !== $current_db_version ) {
			update_option( '_vendor_needs_update', 1 );
		} else {
			update_option( 'vendor_db_version', VENDOR_VERSION );
		}

		// Update version
		update_option( 'vendor_version', VENDOR_VERSION );

		// Check if pages are needed
		if ( wc_get_page_id( 'shop' ) < 1 ) {
			update_option( '_vendor_needs_pages', 1 );
		}

		// Flush rewrite rules
		flush_rewrite_rules();

		// Redirect to welcome screen
		set_transient( '_vendor_activation_redirect', 1, 60 * 60 );
	}

	/**
	 * Handle updates
	 */
	public function update() {
		// Do updates
		$current_db_version = get_option( 'vendor_db_version' );

		if ( version_compare( $current_db_version, '2.1.0', '<' ) || VENDOR_VERSION == '2.1-bleeding' ) {
			include( dirname( __FILE__ ) . '/updates/vendor-update-2.1.php' );
			update_option( 'vendor_db_version', '2.1.0' );
		}

		update_option( 'vendor_db_version', VENDOR_VERSION );
	}

	/**
	 * Create cron jobs (clear them first)
	 */
	private function create_cron_jobs() {
		return false;
	}

	/**
	 * Create pages that the plugin relies on, storing page id's in variables.
	 *
	 * @access public
	 * @return void
	 */
	public static function create_pages() {
		$pages = apply_filters( 'vendor_create_pages', array(
			'vendor' => array(
				'name'    => _x( 'shop', 'page_slug', VENDOR_UNIQUE_IDENTIFIER ),
				'title'   => __( 'Shop', VENDOR_UNIQUE_IDENTIFIER ),
				'content' => ''
			)
		) );

		foreach ( $pages as $key => $page ) {
			wc_create_page( esc_sql( $page['name'] ), 'vendor_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : '' );
		}
	}

	/**
	 * Add the default terms for WC taxonomies - product types and order statuses. Modify this at your own risk.
	 *
	 * @access public
	 * @return void
	 */
	private function create_terms() {

		$taxonomies = array(
			'product_type' => array(
				'simple',
				'grouped',
				'variable',
				'external'
			),
			'shop_order_status' => array(
				'pending',
				'failed',
				'on-hold',
				'processing',
				'completed',
				'refunded',
				'cancelled'
			)
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 *
	 * @access public
	 */
	function create_options() {
		// Include settings so that we can run through defaults
		include_once( dirname( __FILE__ ) . '/admin/class-vendor-admin-settings.php' );

		$settings = Vendor_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			foreach ( $section->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}
		}
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *		vendor_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
	 *
	 * @access public
	 * @return void
	 */
	private function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// WooCommerce Tables
		$vendor_tables = "
			CREATE TABLE {$wpdb->prefix}vendor_termmeta (
				meta_id bigint(20) NOT NULL auto_increment,
				vendor_term_id bigint(20) NOT NULL,
				meta_key varchar(255) NULL,
				meta_value longtext NULL,
				PRIMARY KEY  (meta_id),
				KEY vendor_term_id (vendor_term_id),
				KEY meta_key (meta_key)
			) {$collate};
		";
		dbDelta( $vendor_tables );
	}

	/**
	 * Create roles and capabilities
	 */
	public function create_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			// Customer role
			add_role( 'vendor_contributor', __( 'Vendor Contributor', VENDOR_UNIQUE_IDENTIFIER ), array(
				'read' 						=> true,
				'edit_posts' 				=> false,
				'delete_posts' 				=> false
			) );

			// Shop manager role
			add_role( 'vendor_manager', __( 'Vendor Manager', VENDOR_UNIQUE_IDENTIFIER ), array(
				'level_9'                => true,
				'level_8'                => true,
				'level_7'                => true,
				'level_6'                => true,
				'level_5'                => true,
				'level_4'                => true,
				'level_3'                => true,
				'level_2'                => true,
				'level_1'                => true,
				'level_0'                => true,
				'read'                   => true,
				'read_private_pages'     => true,
				'read_private_posts'     => true,
				'edit_users'             => true,
				'edit_posts'             => true,
				'edit_pages'             => true,
				'edit_published_posts'   => true,
				'edit_published_pages'   => true,
				'edit_private_pages'     => true,
				'edit_private_posts'     => true,
				'edit_others_posts'      => true,
				'edit_others_pages'      => true,
				'publish_posts'          => true,
				'publish_pages'          => true,
				'delete_posts'           => true,
				'delete_pages'           => true,
				'delete_private_pages'   => true,
				'delete_private_posts'   => true,
				'delete_published_pages' => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'delete_others_pages'    => true,
				'manage_categories'      => true,
				'manage_links'           => true,
				'moderate_comments'      => true,
				'unfiltered_html'        => true,
				'upload_files'           => true,
				'export'                 => true,
				'import'                 => true,
				'list_users'             => true
			) );

			$capabilities = $this->get_core_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'vendor_manager', $cap );
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
		}
	}

	/**
	 * Get capabilities for WooCommerce - these are assigned to admin/shop manager during installation or reset
	 *
	 * @access public
	 * @return array
	 */
	public function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_vendor'
		);

		$capability_types = array( VENDOR_POST_TYPE_VENDOR );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

	/**
	 * remove roles function.
	 *
	 * @access public
	 * @return void
	 */
	public function remove_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			$capabilities = $this->get_core_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->remove_cap( 'vendor_manager', $cap );
					$wp_roles->remove_cap( 'administrator', $cap );
				}
			}

			remove_role( 'vendor_contributor' );
			remove_role( 'vendor_manager' );
		}
	}

	/**
	 * Create files/directories
	 */
	private function create_files() {
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir =  wp_upload_dir();

		$files = array(
			array(
				'base' 		=> $upload_dir['basedir'] . '/vendor_uploads',
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> $upload_dir['basedir'] . '/vendor_uploads',
				'file' 		=> 'index.html',
				'content' 	=> ''
			),
			array(
				'base' 		=> WP_PLUGIN_DIR . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/logs',
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> WP_PLUGIN_DIR . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/logs',
				'file' 		=> 'index.html',
				'content' 	=> ''
			)
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * Create CSS from LESS file
	 */
	private function create_css_from_less() {
		// Recompile LESS styles if they are custom
		if ( get_option( 'vendor_frontend_css' ) == 'yes' ) {

			$colors = get_option( 'vendor_frontend_css_colors' );

			if ( ( ! empty( $colors['primary'] ) && ! empty( $colors['secondary'] ) && ! empty( $colors['highlight'] ) && ! empty( $colors['content_bg'] ) && ! empty( $colors['subtext'] ) ) && ( $colors['primary'] != '#ad74a2' || $colors['secondary'] != '#f7f6f7' || $colors['highlight'] != '#85ad74' || $colors['content_bg'] != '#ffffff' || $colors['subtext'] != '#777777' ) ) {
				vendor_compile_less_styles();
			}

		}
	}

	/**
	 * Active plugins pre update option filter
	 *
	 * @param string $new_value
	 * @return string
	 */
	function pre_update_option_active_plugins( $new_value ) {
		$old_value = (array) get_option( 'active_plugins' );

		if ( $new_value !== $old_value && in_array( W3TC_FILE, (array) $new_value ) && in_array( W3TC_FILE, (array) $old_value ) ) {
			$this->_config->set( 'notes.plugins_updated', true );
			try {
				$this->_config->save();
			} catch( Exception $ex ) {}
		}

		return $new_value;
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 *
	 * @return void
	 */
	function in_plugin_update_message() {
		$response = wp_remote_get( VENDOR_GITHUB_README );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {

			// Output Upgrade Notice
			$matches = null;
			$regexp = '~==\s*Upgrade Notice\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote( VENDOR_VERSION ) . '\s*=|$)~Uis';

			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$notices = (array) preg_split('~[\r\n]+~', trim( $matches[1] ) );

				echo '<div style="font-weight: normal; background: #cc99c2; color: #fff !important; border: 1px solid #b76ca9; padding: 9px; margin: 9px 0;">';

				foreach ( $notices as $index => $line ) {
					echo '<p style="margin: 0; font-size: 1.1em; color: #fff; text-shadow: 0 1px 1px #b574a8;">' . preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) . '</p>';
				}

				echo '</div>';
			}

			// Output Changelog
			$matches = null;
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*-(.*)=(.*)(=\s*' . preg_quote( VENDOR_VERSION ) . '\s*-(.*)=|$)~Uis';

			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$changelog = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

				echo ' ' . __( 'What\'s new:', VENDOR_UNIQUE_IDENTIFIER ) . '<div style="font-weight: normal;">';

				$ul = false;

				foreach ( $changelog as $index => $line ) {
					if ( preg_match('~^\s*\*\s*~', $line ) ) {
						if ( ! $ul ) {
							echo '<ul style="list-style: disc inside; margin: 9px 0 9px 20px; overflow:hidden; zoom: 1;">';
							$ul = true;
						}
						$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );
						echo '<li style="width: 50%; margin: 0; float: left; ' . ( $index % 2 == 0 ? 'clear: left;' : '' ) . '">' . $line . '</li>';
					} else {
						if ( $ul ) {
							echo '</ul>';
							$ul = false;
						}
						echo '<p style="margin: 9px 0;">' . htmlspecialchars( $line ) . '</p>';
					}
				}

				if ( $ul ) {
					echo '</ul>';
				}

				echo '</div>';
			}
		}
	}
}

}

return new Vendor_Install();

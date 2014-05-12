<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		WC_Post_types
 * @version		2.1.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */
if ( ! class_exists( 'WC_Post_types' ) ) {

class WC_Post_types
{

	private $permalinks;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->permalinks = get_option( 'woocommerce_permalinks' );

		add_action( 'init', array( $this, 'register_taxonomies' ), 5 );
		add_action( 'init', array( $this, 'register_post_types' ), 5 );
	}

	/**
	 * Register WooCommerce taxonomies.
	 *
	 * @access public
	 * @return void
	 */
	public function register_taxonomies() {

		if ( taxonomy_exists( VENDOR_TAXONOMIES_CATEGORY ) )
			return;
			
		do_action( 'vendor_register_taxonomy' );

		register_taxonomy( VENDOR_TAXONOMIES_CATEGORY, array( VENDOR_POST_TYPE_VENDOR ),
			array(
				'hierarchical' 			=> true,
				//'update_count_callback' => '_wc_term_recount',
				'label' 				=> __( 'Vendor Categories', VENDOR_UNIQUE_IDENTIFIER ),
				'labels' => array(
	                'name' 				=> __( 'Vendor Categories', VENDOR_UNIQUE_IDENTIFIER ),
	                'singular_name' 	=> __( 'Vendor Category', VENDOR_UNIQUE_IDENTIFIER ),
					'menu_name'			=> _x( 'Categories', 'Admin menu name', VENDOR_UNIQUE_IDENTIFIER ),
	                'search_items' 		=> __( 'Search Vendor Categories', VENDOR_UNIQUE_IDENTIFIER ),
	                'all_items' 		=> __( 'All Vendor Categories', VENDOR_UNIQUE_IDENTIFIER ),
	                'parent_item' 		=> __( 'Parent Vendor Category', VENDOR_UNIQUE_IDENTIFIER ),
	                'parent_item_colon' => __( 'Parent Vendor Category:', VENDOR_UNIQUE_IDENTIFIER ),
	                'edit_item' 		=> __( 'Edit Vendor Category', VENDOR_UNIQUE_IDENTIFIER ),
	                'update_item' 		=> __( 'Update Vendor Category', VENDOR_UNIQUE_IDENTIFIER ),
	                'add_new_item' 		=> __( 'Add New Vendor Category', VENDOR_UNIQUE_IDENTIFIER ),
	                'new_item_name' 	=> __( 'New Vendor Category Name', VENDOR_UNIQUE_IDENTIFIER )
	            ),
				'show_ui' 				=> true,
				'query_var' 			=> true,
				'capabilities'			=> array(
					'manage_terms' 		=> VENDOR_CAPABILITIES_MANAGE_TERMS,
					'edit_terms' 		=> VENDOR_CAPABILITIES_EDIT_TERMS,
					'delete_terms' 		=> VENDOR_CAPABILITIES_DELETE_TERMS,
					'assign_terms' 		=> VENDOR_CAPABILITIES_ASSIGN_TERMS,
				),
				'rewrite' 				=> array(
					'slug'         => empty( $this->permalinks['category_base'] ) ? _x( 'product-category', 'slug', VENDOR_UNIQUE_IDENTIFIER ) : $this->permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
				),
			) 
	    );
		
	    register_taxonomy( VENDOR_TAXONOMIES_TAG, array( VENDOR_POST_TYPE_VENDOR ),
	        array(
	            'hierarchical' 			=> false,
	            //'update_count_callback' => '_wc_term_recount',
	            'label' 				=> __( 'Product Tags', VENDOR_UNIQUE_IDENTIFIER ),
	            'labels' => array(
	                'name' 				=> __( 'Product Tags', VENDOR_UNIQUE_IDENTIFIER ),
	                'singular_name' 	=> __( 'Product Tag', VENDOR_UNIQUE_IDENTIFIER ),
					'menu_name'			=> _x( 'Tags', 'Admin menu name', VENDOR_UNIQUE_IDENTIFIER ),
	                'search_items' 		=> __( 'Search Product Tags', VENDOR_UNIQUE_IDENTIFIER ),
	                'all_items' 		=> __( 'All Product Tags', VENDOR_UNIQUE_IDENTIFIER ),
	                'parent_item' 		=> __( 'Parent Product Tag', VENDOR_UNIQUE_IDENTIFIER ),
	                'parent_item_colon' => __( 'Parent Product Tag:', VENDOR_UNIQUE_IDENTIFIER ),
	                'edit_item' 		=> __( 'Edit Product Tag', VENDOR_UNIQUE_IDENTIFIER ),
	                'update_item' 		=> __( 'Update Product Tag', VENDOR_UNIQUE_IDENTIFIER ),
	                'add_new_item' 		=> __( 'Add New Product Tag', VENDOR_UNIQUE_IDENTIFIER ),
	                'new_item_name' 	=> __( 'New Product Tag Name', VENDOR_UNIQUE_IDENTIFIER )
	            ),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
				'capabilities'			=> array(
					'manage_terms' 		=> VENDOR_CAPABILITIES_MANAGE_TERMS,
					'edit_terms' 		=> VENDOR_CAPABILITIES_EDIT_TERMS,
					'delete_terms' 		=> VENDOR_CAPABILITIES_DELETE_TERMS,
					'assign_terms' 		=> VENDOR_CAPABILITIES_ASSIGN_TERMS,
				),
	            'rewrite' 				=> array(
					'slug'       => empty( $this->permalinks['tag_base'] ) ? _x( 'product-tag', 'slug', VENDOR_UNIQUE_IDENTIFIER ) : $this->permalinks['tag_base'],
					'with_front' => false
	            ),
	        )
	    );
	}

	/**
	 * Register core post types
	 */
	public function register_post_types() {

		if ( post_type_exists(VENDOR_POST_TYPE_VENDOR) )
			return;

	    /**
		 * Post Types
		 **/
		do_action( 'woocommerce_register_post_type' );

		$vendor_permalink = empty( $this->permalinks['vendor_base'] ) ? _x( 'vendor', 'slug', VENDOR_UNIQUE_IDENTIFIER ) : $this->permalinks['vendor_base'];

		register_post_type( VENDOR_POST_TYPE_VENDOR,
			array(
				'labels' => array(
						'name' 					=> __( 'Vendors', VENDOR_UNIQUE_IDENTIFIER ),
						'singular_name' 		=> __( 'Vendor', VENDOR_UNIQUE_IDENTIFIER ),
						'menu_name'				=> _x( 'Vendors', 'Admin menu name', VENDOR_UNIQUE_IDENTIFIER ),
						'add_new' 				=> __( 'Add Vendor', VENDOR_UNIQUE_IDENTIFIER ),
						'add_new_item' 			=> __( 'Add New Vendor', VENDOR_UNIQUE_IDENTIFIER ),
						'edit' 					=> __( 'Edit', VENDOR_UNIQUE_IDENTIFIER ),
						'edit_item' 			=> __( 'Edit Vendor', VENDOR_UNIQUE_IDENTIFIER ),
						'new_item' 				=> __( 'New Vendor', VENDOR_UNIQUE_IDENTIFIER ),
						'view' 					=> __( 'View Vendor', VENDOR_UNIQUE_IDENTIFIER ),
						'view_item' 			=> __( 'View Vendor', VENDOR_UNIQUE_IDENTIFIER ),
						'search_items' 			=> __( 'Search Vendors', VENDOR_UNIQUE_IDENTIFIER ),
						'not_found' 			=> __( 'No Vendors found', VENDOR_UNIQUE_IDENTIFIER ),
						'not_found_in_trash' 	=> __( 'No Vendors found in trash', VENDOR_UNIQUE_IDENTIFIER ),
						'parent' 				=> __( 'Parent Vendor', VENDOR_UNIQUE_IDENTIFIER )
					),
				'description' 			=> __( 'This is where you can add new vendors to your site.', VENDOR_UNIQUE_IDENTIFIER ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> 'product',
				'map_meta_cap'			=> true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false, // Hierarchical causes memory issues - WP loads all records!
				'rewrite' 				=> $vendor_permalink ? array( 'slug' => untrailingslashit( $vendor_permalink ), 'with_front' => false, 'feeds' => true ) : false,
				'query_var' 			=> true,
				'supports' 				=> array(
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'comments',
					'custom-fields',
					'page-attributes'
				),
				'has_archive' 			=> ( $shop_page_id = wc_get_page_id( 'shop' ) ) && get_page( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop',
				'show_in_nav_menus' 	=> true
			)
		);
	}
}

} // class exists

return new WC_Post_types();
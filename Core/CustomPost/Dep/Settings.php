<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:40
 */

namespace AsStoreLocator\CustomPost\Dep;


/**
 * Class Settings
 * @package AsStoreLocator\CustomPost\Dep
 */
class Settings {

	/**
	 * @var array
	 */
	public $storeSettings;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->storeSettings = array(
			'post_type_name' => 'store',
			'category'       => 'store_categories',
			'admin_columns'  => true,
			'post_type_args' => array(
				'labels'             => array(
					'name'               => __( 'Stores', ASSL__TEXTDOMAIN ),
					'singular_name'      => __( 'Store', ASSL__TEXTDOMAIN ),
					'all_items'          => __( 'All Stores', ASSL__TEXTDOMAIN ),
					'add_new'            => __( 'Add Store', ASSL__TEXTDOMAIN ),
					'add_new_item'       => __( 'Add new Store', ASSL__TEXTDOMAIN ),
					'edit'               => __( 'Edit', ASSL__TEXTDOMAIN ),
					'edit_item'          => __( 'Edit Store', ASSL__TEXTDOMAIN ),
					'new_item'           => __( 'New Store', ASSL__TEXTDOMAIN ),
					'view_item'          => __( 'View Store', ASSL__TEXTDOMAIN ),
					'search_items'       => __( 'Search Store', ASSL__TEXTDOMAIN ),
					'not_found'          => __( 'Nothing in database.', ASSL__TEXTDOMAIN ),
					'not_found_in_trash' => __( 'Nothing in trash', ASSL__TEXTDOMAIN ),
					'parent_item_colon'  => ''
				),
				'description'        => __( 'New Store Custom Post', ASSL__TEXTDOMAIN ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => false,
				'menu_position'      => 5,
				'menu_icon'          => 'dashicons-store',
				'rewrite'            => false,
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => array( 'title', 'thumbnail', 'editor', 'page-attributes' ),
			),
			'category_args'  => array(
				'hierarchical'      => false,
				'show_admin_column' => true,
				'labels'            => array(
					'name'              => __( 'Store Categories', ASSL__TEXTDOMAIN ),
					'singular_name'     => __( 'Store Category', ASSL__TEXTDOMAIN ),
					'search_items'      => __( 'Search Store Category', ASSL__TEXTDOMAIN ),
					'all_items'         => __( 'All Store Categories', ASSL__TEXTDOMAIN ),
					'parent_item'       => __( 'Parent Store Category', ASSL__TEXTDOMAIN ),
					'parent_item_colon' => __( 'Parent Store Category:', ASSL__TEXTDOMAIN ),
					'edit_item'         => __( 'Edit Store Category', ASSL__TEXTDOMAIN ),
					'update_item'       => __( 'Update Store Category', ASSL__TEXTDOMAIN ),
					'add_new_item'      => __( 'Add New Store Category', ASSL__TEXTDOMAIN ),
					'new_item_name'     => __( 'Store Category Name', ASSL__TEXTDOMAIN )
				),
				'show_ui'           => true,
				'query_var'         => true,
				'rewrite'           => false,
				'public'            => false
			),
		);

	}
}
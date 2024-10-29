<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:40
 */

namespace AsStoreLocator\CustomPost;


use AsStoreLocator\CustomPost\Dep\CustomPostLoader as Loader;
use AsStoreLocator\CustomPost\Dep\Settings;

/**
 * Class Store
 * @package AsStoreLocator\CustomPost
 */
class Store extends Loader {

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * @var mixed
	 */
	private $post_type_name;
	/**
	 * @var mixed
	 */
	private $category;

	/**
	 * Store constructor.
	 *
	 * @param Settings   $settings
	 */
	public function __construct( Settings $settings ) {

		$this->settings   = $settings->storeSettings;
		parent::__construct( $this->settings );

		$this->post_type_name = $this->settings['post_type_name'];
		$this->category       = $this->settings['category'];

		add_image_size( 'assl-store-thumb', 75, 75, array( 'center', 'center' ) );

		//Colonna ordinamento menu
		add_action( 'manage_edit-' . $this->post_type_name . '_columns', array( $this, 'assl_add_menu_order_column' ) );
		add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array(
			$this,
			'assl_show_menu_order_column'
		) );
		add_filter( 'manage_edit-' . $this->post_type_name . '_sortable_columns', array(
			$this,
			'assl_order_menu_order_column'
		) );
	}

	/**
	 * @return mixed
	 */
	public function getPostTypeName() {
		return $this->post_type_name;
	}

	/**
	 * @return mixed
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param $store_columns
	 *
	 * @return mixed
	 */
	public function assl_add_menu_order_column( $store_columns ) {
		$store_columns['menu_order'] = __( 'Order', ASSL__TEXTDOMAIN );

		return $store_columns;
	}

	/**
	 * @param $name
	 */
	public function assl_show_menu_order_column( $name ) {
		global $post;

		switch ( $name ) {
			case 'menu_order':
				$order = $post->menu_order;
				echo $order;
				break;
			default:
				break;
		}
	}

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function assl_order_menu_order_column( $columns ) {
		$columns['menu_order'] = 'menu_order';

		return $columns;
	}

}
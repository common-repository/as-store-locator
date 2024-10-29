<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:44
 */

namespace AsStoreLocator\CustomPost\Lib;


/**
 * Class Ajax
 * @package AsStoreLocator\CustomPost\Lib
 */
class Ajax {

	/**
	 * @var Utility
	 */
	private $utility;
	/**
	 * @var mixed
	 */
	private $post_type_name;
	/**
	 * @var mixed
	 */
	private $category;

	/**
	 * Ajax constructor.
	 *
	 * @param Utility $utility
	 */
	public function __construct( Utility $utility ) {
		$this->utility        = $utility;
		$this->post_type_name = $this->utility->getPostTypeName();
		$this->category       = $this->utility->getCategory();
		//Query loop ajax
		add_action( 'wp_ajax_getStores', array( $this, 'getStores' ) );
		add_action( 'wp_ajax_nopriv_getStores', array( $this, 'getStores' ) );
	}

	/**
	 *
	 */
	public function getStores() {
		//Argomenti di default:
		$args = array(
			'post_type'        => $this->post_type_name,
			'posts_per_page'   => - 1,
			'suppress_filters' => 0,
			'post_status'      => 'publish',
			'orderby'          => array( 'menu_order' => 'ASC' )
		);
		//Tassonomia
		if ( isset( $_POST['categories'] ) && is_array( $_POST['categories'] ) ) {
			$categories = $this->utility->sanitizeCategoriesArray($_POST['categories']);
			if(!empty($categories)) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $this->category,
						'field'    => 'id',
						'terms'    => $categories
					),
				);
			}
		}

		//Parola chiave cercata
		if ( isset( $_POST['keyword'] ) ) {
			$keyword = sanitize_text_field( $_POST['keyword'] );
			if ( $keyword != "" ) {
				//Arg default
				$args['post__in'] = $this->utility->getPostIn( $keyword );
			}
		}

		if ( isset( $_POST['usergeo'] ) && ! empty( $_POST['usergeo'] ) ) {
			//Richiamo il loop
			wp_send_json( $this->utility->getStores( $args, $_POST['usergeo'] ) );
		} else {
			//Richiamo il loop
			wp_send_json( $this->utility->getStores( $args ) );
		}
	}
}
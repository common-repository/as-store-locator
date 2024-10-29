<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:46
 */

namespace AsStoreLocator\CustomPost\Dep;


/**
 * Class CustomPostLoader
 * @package AsStoreLocator\CustomPost\Dep
 */
class CustomPostLoader {

	/**
	 * @var
	 */
	private $settings;

	/**
	 * @var
	 */
	private $post_type_name;

	/**
	 * @var
	 */
	private $category;

	/**
	 * @var
	 */
	private $admin_columns;

	/**
	 * @var
	 */
	private $post_type_args;

	/**
	 * @var
	 */
	private $category_args;

	/**
	 * CustomPostLoader constructor.
	 *
	 * @param $settings
	 */
	public function __construct( $settings ) {
		$this->settings       = $settings;
		$this->post_type_name = $this->settings['post_type_name'];
		$this->category       = $this->settings['category'];
		$this->admin_columns  = $this->settings['admin_columns'];
		$this->post_type_args = $this->settings['post_type_args'];
		$this->category_args  = $this->settings['category_args'];

		add_action( 'init', array( $this, 'asslRegisterCustomPost' ) );
		add_action( 'init', array( $this, 'asslRegisterCategory' ) );

		//FILTRI NEL BACKEND
		add_action( 'restrict_manage_posts', array( $this, 'asslFilterListByCategory' ) );
		add_action( 'parse_query', array( $this, 'asslPerformFilteringByCategory' ) );

		if ( $this->admin_columns == true ) {
			//Colonna con miniatura
			add_filter( 'manage_' . $this->post_type_name . '_posts_columns', array( $this, 'asslColumnsHead' ) );
			add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array(
				$this,
				'asslColumnsContent'
			), 10, 2 );
		}

	}

	/**
	 *
	 */
	public function asslRegisterCustomPost() {

		register_post_type(
			$this->post_type_name,
			$this->post_type_args
		);

	}

	/**
	 *
	 */
	public function asslRegisterCategory() {
		if ( isset( $this->category ) ) {
			register_taxonomy( $this->category,
				array( $this->post_type_name ),
				$this->category_args
			);
		}

	}

	/**
	 *
	 */
	public function asslFilterListByCategory() {
		$screen = get_current_screen();
		global $wp_query;
		if ( isset( $this->category ) && $screen->post_type == $this->post_type_name ) {
			wp_dropdown_categories( array(
				'show_option_all' => __( "All Categories", ASSL__TEXTDOMAIN ),
				'taxonomy'        => $this->category,
				'name'            => $this->category,
				'orderby'         => 'name',
				'selected'        => ( isset( $wp_query->query[ $this->category ] ) ? $wp_query->query[ $this->category ] : '' ),
				'hierarchical'    => true,
				'depth'           => 3,
				'show_count'      => false,
				'hide_empty'      => false,
			) );
		}
	}


	/**
	 * @param $query
	 */
	public function asslPerformFilteringByCategory( $query ) {
		$qv = isset( $this->category ) ? $query->query_vars : false;

		if ( $qv && isset( $qv[ $this->category ] ) && is_numeric( $qv[ $this->category ] ) ) {
			$term                  = get_term_by( 'id', $qv[ $this->category ], $this->category );
			$qv[ $this->category ] = isset( $term->slug ) ? $term->slug : '';
		}
	}

	/**
	 * @param $defaults
	 *
	 * @return mixed
	 */
	public function asslColumnsHead( $defaults ) {
		$defaults[ $this->post_type_name . '_featured_image' ] = __( 'Image', ASSL__TEXTDOMAIN );

		return $defaults;
	}

	/**
	 * @param $column_name
	 * @param $post_ID
	 */
	public function asslColumnsContent( $column_name, $post_ID ) {
		switch ( $column_name ) {
			case $this->post_type_name . '_featured_image':
				$post_featured_image = $this->asslGetFeaturedImage( $post_ID );
				if ( $post_featured_image ) {
					echo $post_featured_image;
				}
				break;

		}
	}

	/**
	 * @param $post_ID
	 *
	 * @return string
	 */
	private function asslGetFeaturedImage( $post_ID ) {
		$post_thumbnail_id = get_post_thumbnail_id( $post_ID );
		if ( $post_thumbnail_id ) {
			//https://codex.wordpress.org/it:Riferimento_funzioni/wp_get_attachment_image_src
			$post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id, array( 50, 50 ) );

			return '<img src="' . $post_thumbnail_img[0] . '" width="' . $post_thumbnail_img[1] . '" height="' . $post_thumbnail_img[2] . '" />';
		} else {
			return '<img src="' . ASSL__PLUGIN_URL . 'images/no_images.jpg" width="50" height="50" />';
		}
	}
}
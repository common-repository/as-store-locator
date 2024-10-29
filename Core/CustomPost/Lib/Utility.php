<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:42
 */

namespace AsStoreLocator\CustomPost\Lib;


use AsStoreLocator\Admin\Start as Admin;

/**
 * Class Utility
 * @package AsStoreLocator\CustomPost\Lib
 */
class Utility {

	/**
	 * @var Admin
	 */
	private $admin;

	/**
	 * @var
	 */
	private $post_type_name;

	/**
	 * @var
	 */
	private $category;

	/**
	 * Utility constructor.
	 *
	 * @param Admin $admin
	 * @param       $post_type_name
	 * @param       $category
	 */
	public function __construct( Admin $admin, $post_type_name, $category ) {
		$this->admin          = $admin;
		$this->post_type_name = $post_type_name;
		$this->category       = $category;
	}

	/**
	 * @return Admin
	 */
	public function getAdmin() {
		return $this->admin;
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
	 * @param array $categories
	 *
	 * @return array
	 */
	public function sanitizeCategoriesArray( array $categories ) {
		$newCategories = array();
		if(!empty($categories)) {
			foreach ($categories as $cat) {
				if(intval($cat)) {
					array_push($newCategories, absint($cat));
				}
			}
		}
		return $newCategories;
	}

	/**
	 * @param $keyword
	 *
	 * @return array
	 */
	public function getPostIn( $keyword ) {
		$query_title = get_posts(
			array(
				'post_type'   => $this->post_type_name,
				's'           => $keyword,
				'numberposts' => - 1
			)
		);
		$query_meta  = get_posts(
			array(
				'post_type'   => $this->post_type_name,
				'numberposts' => - 1,
				'meta_query'  => array(
					'relation' => 'OR',
					array(
						'key'     => '_assl_geo_citta',
						'value'   => $keyword,
						'compare' => 'LIKE'
					),
					array(
						'key'     => '_assl_geo_cap',
						'value'   => $keyword,
						'compare' => 'LIKE'
					),
					array(
						'key'     => '_assl_geo_nazione',
						'value'   => $keyword,
						'compare' => 'LIKE'
					),
					array(
						'key'     => '_assl_geo_via',
						'value'   => $keyword,
						'compare' => 'LIKE'
					)
				)
			)
		);
		$merged      = array_merge( $query_title, $query_meta );

		$post_ids = array();
		foreach ( $merged as $item ) {
			$post_ids[] = $item->ID;
		}

		$post__in = array_unique( $post_ids );

		return $post__in;
	}

	/**
	 * @return string
	 */
	public function getStoreCategories() {
		$out   = '';
		$terms = get_terms( $this->category, array( 'parent' => 0 ) );
		if(!empty($terms)) {
			$out .= '<div class="assl-store-categories-intro"><input type="checkbox" id="toggleAllStore" checked="checked"><label for="toggleAllStore">' . __( "Select/Unselect All", ASSL__TEXTDOMAIN ) . '</label><br>';
			$out .= '<button id="getStoreByCat">' . __( "Apply Filters", ASSL__TEXTDOMAIN ) . '</button></div>';
			$out .= '<ul id="assl-store-categories" class="checkbox">';
			foreach ( $terms as $term ) {
				$out .= '<li><input id="cat-' . $term->term_id . '" class="assl-cat" type="checkbox" checked="checked" value="' . $term->term_id . '"><label for="cat-' . $term->term_id . '" class="assl-type-filter">' . ucfirst( $term->name ) . '&nbsp;(' . $term->count . ')' . '</label></li>';
			}
			$out .= '</ul>';
		}
		return $out;
	}

	/**
	 * @param $post_id
	 *
	 * @return array
	 */
	public function getStoreGeolocationData( $post_id ) {
		$id       = $post_id;
		$title    = get_the_title( $post_id );
		$image    = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'assl-store-thumb' );
		$lat      = get_post_meta( $post_id, '_assl_geo_lat', true );
		$lng      = get_post_meta( $post_id, '_assl_geo_lng', true );
		$icon     = $this->getCategoryIcon( $post_id );
		$via      = get_post_meta( $post_id, '_assl_geo_via', true );
		$num      = get_post_meta( $post_id, '_assl_geo_num', true );
		$citta    = get_post_meta( $post_id, '_assl_geo_citta', true );
		$stato    = get_post_meta( $post_id, '_assl_geo_stato', true );
		$cap      = get_post_meta( $post_id, '_assl_geo_cap', true );
		$nazione  = get_post_meta( $post_id, '_assl_geo_nazione', true );
		$telefono = get_post_meta( $post_id, '_assl_geo_tel', true );
		$telefono2= get_post_meta( $post_id, '_assl_geo_tel2', true );
		$fax      = get_post_meta( $post_id, '_assl_geo_fax', true );
		$website  = get_post_meta( $post_id, '_assl_geo_url', true );
		$email    = get_post_meta( $post_id, '_assl_geo_mail', true );
		$email2   = get_post_meta( $post_id, '_assl_geo_mail2', true );
		return array(
			'id'       => $id,
			'title'    => $title,
			'image'    => $image[0],
			'lat'      => floatval( $lat ),
			'lng'      => floatval( $lng ),
			'icon'     => $icon,
			'via'      => $via,
			'num'      => $num,
			'citta'    => $citta,
			'stato'    => $stato,
			'cap'      => $cap,
			'nazione'  => $nazione,
			'telefono' => $telefono,
			'telefono2'=> $telefono2,
			'fax'      => $fax,
			'website'  => $website,
			'email'    => antispambot($email),
			'email2'   => antispambot($email2)
		);
	}

	/**
	 * @param $lat1
	 * @param $lng1
	 * @param $lat2
	 * @param $lng2
	 * @param $unit
	 *
	 * @return float|int
	 */
	public function distanceCalculator( $lat1, $lng1, $lat2, $lng2, $unit ) {

		$theta = $lng1 - $lng2;
		$dist  = sin( deg2rad( $lat1 ) ) * sin( deg2rad( $lat2 ) ) + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * cos( deg2rad( $theta ) );
		$dist  = acos( $dist );
		$dist  = rad2deg( $dist );
		$miles = $dist * 60 * 1.1515;

		switch ( $unit ) {
			case 'km':
				return ( $miles * 1.609344 );
				break;
			case 'mi.':
				return intval( ( $miles * 1.609344 * 1000 ) );
				break;
			default:
				return $miles;
				break;
		}
	}

	/**
	 * @param $post_id
	 *
	 * @return bool|string
	 */
	public function getCategoryIcon( $post_id ) {
		//Marker di default del plugin
		$defaultMarker   = ASSL__PLUGIN_URL . "images/assl_marker.png";
		//Marker di default scelto dall'utente per tutte le categorie
		$defaultMarkerCategory = $this->admin->getOption( 'assl_pin_category_image', 'assl_admin_map' );
		//Imposto il default
		$default = $defaultMarkerCategory && "" != $defaultMarkerCategory ? $defaultMarkerCategory : $defaultMarker;
		//Prendo la prima categoria
		$categoria = $this->getFirstCategory( $post_id );
		if ( $categoria ) {
			$term = get_term_meta( $categoria->term_id, 'assl_store_image_category', true );
			//Compatibilità con la vecchia versione che salvava un'array
			$id = is_array($term) ? $term['id'] : $term;
			//Se è un id valido prendo l'url altrimenti false
			$image  = $id && intval($id) ? wp_get_attachment_image_src($id, 'full') : false;

			//Se trovo l'immagine la usa altrimenti metto quella di default
			$icon = $image && isset($image[0]) ? $image[0] : $default;
		} else {

			$icon = $default;
		}
		return $icon;
	}

	/**
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function getFirstCategory( $post_id ) {
		$post_terms = get_the_terms( $post_id, $this->category );
		if ( ! empty( $post_terms ) ) {
			return $post_terms[0];
		} else {
			return false;
		}
	}

	/**
	 * @param       $post_id
	 * @param       $item
	 * @param array $distanza
	 *
	 * @return string
	 */
	private function getStoreLoop( $post_id, $item, array $distanza ) {
		$lista = '<li>';
		$lista .= '<div class="assl-img assl-list-title" data-marker="' . $item . '">';
		if ( has_post_thumbnail( $post_id ) ) :
			$lista .= get_the_post_thumbnail( $post_id, 'assl-store-thumb' );
			$lista .= '<hr>';
		endif;
		$lista .= '<img src="' . $this->getCategoryIcon( $post_id ) . '" width="32" height="32"/>';

		$lista .= '</div>';
		$lista .= '<div class="assl-text">';
		$lista .= '<h2 class="assl-list-title" data-marker="' . $item . '">' . get_the_title( $post_id ) . '</h2>';
		$poi_cat = $this->getFirstCategory( $post_id );
		if ( $poi_cat ) {
			$lista .= '<h4>' . $poi_cat->name . '</h4>';
		}
        $lista .= apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );
		$lista .= '<address>';
		$via  = get_post_meta( $post_id, '_assl_geo_via', true );
		$num  = get_post_meta( $post_id, '_assl_geo_num', true );
		$cap  = get_post_meta( $post_id, '_assl_geo_cap', true );
		$city = get_post_meta( $post_id, '_assl_geo_citta', true );
		$tel  = get_post_meta( $post_id, '_assl_geo_tel', true );
		$tel2 = get_post_meta( $post_id, '_assl_geo_tel2', true );
		$fax  = get_post_meta( $post_id, '_assl_geo_fax', true );
		$mail = get_post_meta( $post_id, '_assl_geo_mail', true );
		$mail2= get_post_meta( $post_id, '_assl_geo_mail2', true );
		$url  = get_post_meta( $post_id, '_assl_geo_url', true );
		$lista .= $via;
		if ( ! empty( $via ) ) :
			$lista .= ! empty( $num ) ? ', ' . $num . '<br>' : '<br>';
		endif;
		$lista .= ! empty( $cap ) ? $cap : '';
		$lista .= ( ! empty( $cap ) && ! empty( $city ) ) ? ' - ' : '';
		$lista .= ! empty( $city ) ? $city . '<br>' : '<br>';
		$lista .= ! empty( $tel ) ? '<i class="i-phone"></i> ' . $tel . '<br>' : '';
		$lista .= ! empty( $tel2 ) ? '<i class="i-phone"></i> ' . $tel2 . '<br>' : '';
		$lista .= ! empty( $fax ) ? 'Fax: ' . $fax . '<br>' : '';
		if ( ! empty( $mail ) ):
			$lista .= '<i class="i-mail"></i> <a href="mailto:' . antispambot($mail) . '" title="E-mail">'.antispambot($mail).'</a><br>';
		endif;
		if ( ! empty( $mail2 ) ):
			$lista .= '<i class="i-mail"></i> <a href="mailto:' . antispambot($mail2) . '" title="E-mail 2">'.antispambot($mail2).'</a><br>';
		endif;
		if ( ! empty( $url ) ):
			$lista .= '<i class="i-website"></i> <a href="' . esc_url( $url ) . '" target="_blank" title="' . __( 'Website', ASSL__TEXTDOMAIN ) . '"> ' . preg_replace('#^https?://#', '', $url) . '</a>';
		endif;
		$lista .= '</address>';
		if ( ! empty( $distanza ) && isset( $distanza['distanza'] ) && isset( $distanza['unit'] ) ) {
			$km = intval( $distanza['distanza'] );
			$lista .= '<p>' . __( 'Far: ', ASSL__TEXTDOMAIN ) . ' ' . $km . ' ' . $distanza['unit'] . '</p>';
		}
		$lista .= '</div>';
		$lista .= '<div class="clear"></div>';
		$lista .= '</li>';

		return $lista;
	}

	/**
	 * @param      $args
	 * @param bool $usergeo
	 *
	 * @return object
	 */
	public function getStores( $args, $usergeo = false ) {
		//Preparo un'array per la mappa
		$poi_maps = array();

		//Dati per Lista html
		$lista = '';

		$q = new \WP_Query( $args );
		if ( $q->have_posts() ) :
			$i = 0;
			while ( $q->have_posts() ):
				$q->the_post();

				$post_id = get_the_ID();

				//Loop articoli con geolocalizzazione
				if ( $usergeo ) :
					$maxDistance = intval( $usergeo[0] );
					$lat1        = floatval( $usergeo[1] );
					$lng1        = floatval( $usergeo[2] );

					//Latitudine e longitudine del post
					$lat2      = floatval( get_post_meta( $post_id, '_assl_geo_lat', true ) );
					$lng2      = floatval( get_post_meta( $post_id, '_assl_geo_lng', true ) );
					$unit_type = $this->admin->getOption( 'assl_km_mi', 'assl_admin_map' );
					$unit      = isset( $unit_type ) ? $unit_type : "km";

					if ( ( isset( $lat1 ) && isset( $lng1 ) ) && ( isset( $lat2 ) && isset( $lng2 ) ) ) {
						$distanza = floatval( $this->distanceCalculator( $lat1, $lng1, $lat2, $lng2, $unit ) );
					} else {
						$distanza = false;
					}
					if ( $distanza && ( $distanza < floatval( $maxDistance ) ) ) :

						//Inserisco i poi nell'array per la mappa
						$poi_maps[] = $this->getStoreGeolocationData( $post_id );

						//Continuo con l'html
						$lista .= $this->getStoreLoop( $post_id, $i, array(
							'distanza' => $distanza,
							'unit'     => $unit
						) );

						$i ++;

					endif;

				else :
					//Inserisco i poi nell'array per la mappa
					$poi_maps[] = $this->getStoreGeolocationData( $post_id );

					//Continuo con l'html
					$lista .= $this->getStoreLoop( $post_id, $i, array() );
					$i ++;
				endif;
			endwhile;

			wp_reset_query();
			wp_reset_postdata();

		else:
			//Non trovo nulla
		endif;

		//Ritorno i dati
		$results = array(
			'lista'     => $lista,
			'mappa'     => $poi_maps,
			'totstores' => count( $poi_maps )
		);

		return (object) $results;
	}

	/**
	 * @param $filter
	 *
	 * @return object
	 *
	 */
	public function getFilters( $filter ) {

		$obj = array();
		switch ( $filter ) {
			case 'type':
				$labelOption   = $this->admin->getOption( 'assl_map_filter_labelName_type', 'assl_admin_advanced' );
				$labelName     = ( isset( $labelOption ) && $labelOption != '' ) ? $labelOption : __( 'Type', ASSL__TEXTDOMAIN );
				$obj['button'] = '<li><a class="assl-buttons" data-button="type"><i class="i-type"></i><span>' . $labelName . '</span></a></li>';
				$obj['html']   = '<div id="assl-type" class="assl-filters">' . $this->getStoreCategories() . '</div>';
				break;
			case 'radius':
				$unit_type = $this->admin->getOption( 'assl_km_mi', 'assl_admin_map' );
				$unit      = $unit_type == 'km' ? 'METRIC' : 'IMPERIAL';
				//cerchio raggio
				$strokeColor    = $this->admin->getOption( 'assl_map_radius_stroke_color', 'assl_admin_map' );
				$strokeOpacity  = $this->admin->getOption( 'assl_map_radius_stroke_opacity', 'assl_admin_map' );
				$strokeWeight   = $this->admin->getOption( 'assl_map_radius_stroke_weight', 'assl_admin_map' );
				$fillColor      = $this->admin->getOption( 'assl_map_radius_fill_color', 'assl_admin_map' );
				$fillOpacity    = $this->admin->getOption( 'assl_map_radius_fill_opacity', 'assl_admin_map' );
				$radiusSettings = array(
					'strokecolor'   => $strokeColor ? $strokeColor : '#000',
					'strokeopacity' => $strokeOpacity ? $strokeOpacity : 0.5,
					'strokeweight'  => $strokeWeight ? $strokeWeight : 1,
					'fillcolor'     => $fillColor ? $fillColor : '#000',
					'fillopacity'   => $fillOpacity ? $fillOpacity : 0.1
				);
				$labelOption    = $this->admin->getOption( 'assl_map_filter_labelName_radius', 'assl_admin_advanced' );
				$labelName      = ( isset( $labelOption ) && $labelOption != '' ) ? $labelOption : __( 'Radius', ASSL__TEXTDOMAIN );
				$obj['button']  = '<li><a class="assl-buttons" data-button="radius"><i class="i-radius"></i><span>' . $labelName . '</span></a></li>';

				$radius = '';
				$radius .= "<div id='assl-radius' class='assl-filters' data-unit='$unit' data-radiussettings='" . json_encode( $radiusSettings ) . "' data-radius='' data-userlat='' data-userlng=''>";
				$radius .= '<fieldset id="radius-input">';
				$radius .= '<ul class="radio">';
				$radius .= '<li><input type="radio" value="1.0" id="unit1" name="radius-value" class="radius" /><label for="unit1">1 ' . ucfirst( $unit_type ) . '</label></li>';
				$radius .= '<li><input type="radio" value="2.0" id="unit2" name="radius-value" class="radius" /><label for="unit2">2 ' . ucfirst( $unit_type ) . '</label></li>';
				$radius .= '<li><input type="radio" value="5.0" id="unit5" name="radius-value" class="radius" /><label for="unit5">5 ' . ucfirst( $unit_type ) . '</label></li>';
				$radius .= '<li><input type="radio" value="10.0" id="unit10" name="radius-value" class="radius" /><label for="unit10">10 ' . ucfirst( $unit_type ) . '</label></li>';
				$radius .= '<li><input type="radio" value="15.0" id="unit15" name="radius-value" class="radius" /><label for="unit15">15 ' . ucfirst( $unit_type ) . '</label></li>';
				$radius .= '<li><button id="assl-clear-radius">' . __( 'Remove Radius Filter', ASSL__TEXTDOMAIN ) . '</button></li>';
				$radius .= '</ul>';
				$radius .= '</fieldset>';
				$radius .= '</div>';

				$obj['html'] = $radius;
				break;
			case 'list':
				$labelOption   = $this->admin->getOption( 'assl_map_filter_labelName_list', 'assl_admin_advanced' );
				$labelName     = ( isset( $labelOption ) && $labelOption != '' ) ? $labelOption : __( 'List', ASSL__TEXTDOMAIN );
				$obj['button'] = '<li><a class="assl-buttons" data-button="list"><i class="i-list"></i><span>' . $labelName . '</span></a></li>';
				$obj['html']   = '<div id="assl-list" class="assl-filters"></div>';
				break;
			case 'search':
				$labelOption   = $this->admin->getOption( 'assl_map_filter_labelName_search', 'assl_admin_advanced' );
				$labelName     = ( isset( $labelOption ) && $labelOption != '' ) ? $labelOption : __( 'Search', ASSL__TEXTDOMAIN );
				$obj['button'] = '<li><a class="assl-buttons" data-button="search"><i class="i-search"></i><span>' . $labelName . '</span></a></li>';
				$obj['html']   = '<div id="assl-search" class="assl-filters"><label for="assl-search-store">' . __( 'Search by: Title, Street, City, State or Zip Code', ASSL__TEXTDOMAIN ) . '</label><input id="assl-search-store" type="text" placeholder="' . __( 'Type Search...', ASSL__TEXTDOMAIN ) . '" /><button id="searchStore">' . __( 'Search', ASSL__TEXTDOMAIN ) . '</button></div>';
				break;
		}

		return (object) $obj;

	}

}
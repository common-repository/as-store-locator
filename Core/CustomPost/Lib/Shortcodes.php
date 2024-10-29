<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:44
 */

namespace AsStoreLocator\CustomPost\Lib;


class Shortcodes {
	/**
	 * @var Utility
	 */
	private $utility;

	private $admin;

	private $post_type_name;

	private $category;


	public function __construct( Utility $utility ) {
		$this->utility        = $utility;
		$this->admin          = $this->utility->getAdmin();
		$this->post_type_name = $this->utility->getPostTypeName();
		$this->category       = $this->utility->getCategory();
		add_shortcode( 'store_locator', array( $this, 'showMap' ) );
	}

	/**
	 * @return string
	 */
	public function showMap() {
		/*
		 * OPZIONI BACKEND
		 */
		//Coordinate utente fallback
		$centerLat = $this->admin->getOption( 'assl_center_lat', 'assl_admin_map' );
		$centerLng = $this->admin->getOption( 'assl_center_lng', 'assl_admin_map' );
		//Linea calcolo percorso
		$dirstrokeColor   = $this->admin->getOption( 'assl_map_directions_stroke_color', 'assl_admin_map' );
		$dirstrokeOpacity = $this->admin->getOption( 'assl_map_directions_stroke_opacity', 'assl_admin_map' );
		$dirstrokeWeight  = $this->admin->getOption( 'assl_map_directions_stroke_weight', 'assl_admin_map' );
		//testo e immagini cluster
		$cluster_activation = $this->admin->getOption( 'assl_cluster_activation', 'assl_admin_map' );
		$cluster_text_color = $this->admin->getOption( 'assl_cluster_icon_color', 'assl_admin_map' );
		$cluster_img_small  = $this->admin->getOption( 'assl_cluster_icon_small', 'assl_admin_map' );
		$c_img_small        = $cluster_img_small && "" != $cluster_img_small ? $cluster_img_small : ASSL__PLUGIN_URL . "images/cluster_small.png";
		$cluster_img_medium = $this->admin->getOption( 'assl_cluster_icon_medium', 'assl_admin_map' );
		$c_img_medium       = $cluster_img_medium && "" != $cluster_img_medium ? $cluster_img_medium : ASSL__PLUGIN_URL . "images/cluster_medium.png";
		$cluster_img_large  = $this->admin->getOption( 'assl_cluster_icon_large', 'assl_admin_map' );
		$c_img_large        = $cluster_img_large && "" != $cluster_img_large ? $cluster_img_large : ASSL__PLUGIN_URL . "images/cluster_large.png";
		//Stile Mappa
		$mapStyle = $this->admin->getOption( 'assl_map_style', 'assl_admin_map' );
		//immagine pin
		$pinImage   = $this->admin->getOption( 'assl_pin_image', 'assl_admin_map' );
		$pinWidth   = $this->admin->getOption( 'assl_pin_size_width', 'assl_admin_map' );
		$pinHeight  = $this->admin->getOption( 'assl_pin_size_height', 'assl_admin_map' );

		$usermarker = $pinImage && "" != $pinImage ? $pinImage : ASSL__PLUGIN_URL . "images/assl_user_marker.png";
		//Map Controls
		$disableUI           = $this->admin->getOption( 'assl_map_controls_disableui', 'assl_admin_map' );
		$mapTypeControl      = $this->admin->getOption( 'assl_map_controls_maptype', 'assl_admin_map' );
		$streetView          = $this->admin->getOption( 'assl_map_controls_streetview', 'assl_admin_map' );
		$scrollWheel         = $this->admin->getOption( 'assl_map_controls_scrollwheel', 'assl_admin_map' );
		$storeSettingsOption = array(
			'ishttps'           => (bool) $this->admin->checkHttpsServer(),
			'distance'          => __( 'Distance', ASSL__TEXTDOMAIN ),
			'duration'          => __( 'Duration', ASSL__TEXTDOMAIN ),
			'calctitle'         => __( 'Get directions', ASSL__TEXTDOMAIN ),
			'yourpos'           => __( 'You are here', ASSL__TEXTDOMAIN ),
			'defaultposition'   => __( 'Actual Position', ASSL__TEXTDOMAIN ),
			'geoprobl'          => __( 'There is a problem with Geolocation. Marker set to', ASSL__TEXTDOMAIN ),
			'geobrowser'        => __( 'Geolocation not supported or You have decided not to share your location. Marker set to', ASSL__TEXTDOMAIN ),
			'centerlat'         => $centerLat ? $centerLat : 37.513182,
			'centerlng'         => $centerLng ? $centerLng : 15.062063,
			'dirstrokecolor'    => $dirstrokeColor ? $dirstrokeColor : '#61b5e3',
			'dirstrokeopacity'  => $dirstrokeOpacity ? $dirstrokeOpacity : 0.7,
			'dirstrokeweight'   => $dirstrokeWeight ? $dirstrokeWeight : 4,
			'clusteractivation' => $cluster_activation ? $cluster_activation : 'si',
			'clustertext'       => $cluster_text_color ? $cluster_text_color : '#fff',
			'clusterimgsmall'   => $c_img_small,
			'clusterimgmedium'  => $c_img_medium,
			'clusterimglarge'   => $c_img_large,
			'pinimage'          => $usermarker,
			'pinimagewidth'     => isset($pinWidth) && '' != $pinWidth && intval($pinWidth) ? $pinWidth : 32,
			'pinimageheight'    => isset($pinHeight) && '' != $pinHeight && intval($pinHeight) ? $pinHeight : 32,
			'disableui'         => $disableUI ? (bool)$disableUI : false,
			'maptype'           => $mapTypeControl ? (bool)$mapTypeControl : false,
			'streetview'        => $streetView ? (bool)$streetView : false,
			'scrollwheel'       => $scrollWheel ? (bool)$scrollWheel : false
		);
		//Stores Found
		$storeFound = $this->admin->getOption( 'assl_map_tot_founds', 'assl_admin_advanced' );
		//filtri
		$filters = $this->admin->getOption( 'assl_map_filters', 'assl_admin_advanced' );

		$html = '';
		$html .= "<div id='assl-store-locator-container'>";
		//Store found
		if ( isset( $storeFound ) && $storeFound == 'si' ) {
			$html .= '<div class="assl-top-bar-store-results">';
			$html .= '<span class="totStoreFound"></span> ' . __( "Stores Found", ASSL__TEXTDOMAIN );
			$html .= '</div>';
		}
		//Top Bar
		$html .= '<div class="assl-top-bar">';
		if ( ! empty( $filters ) ) {
			$html .= '<ul class="list-inline">';
			foreach ( $filters as $filter ) {
				$html .= $this->utility->getFilters( $filter )->button;
			}
			$html .= '</ul>';
		} else {
			$html .= '<div id="topbar-intro">Store Locator</div>';
		}
		$html .= '</div>';
		//Mappa
		$html .= '<div id="assl-gmap-container">';
		$html .= '<div id="assl-loading"><div class="assl-loading-container"><img src="' . ASSL__PLUGIN_URL . 'images/ajax-loader.gif" /></div></div>';
		$html .= '<div id="assl-no-results"><div class="assl-no-results-container"><p>' . __( 'No stores found.', ASSL__TEXTDOMAIN ) . '<a class="assl-reset-filter">Reset</a></p></div></div>';
		if ( ! empty( $filters ) ) {
			foreach ( $filters as $filter ) {
				$html .= $this->utility->getFilters( $filter )->html;
			}
		}
		//Mappa
		$html .= '<div id="assl-gmap"></div>';
		//Pannello direzione
		$direction_panel = $this->admin->getOption( 'assl_map_directions_panel', 'assl_admin_map' );
		if ( $direction_panel == 'si' ) {
			$html .= '<a id="direction-close"><i class="i-close"></i></a><div id="direction-panel"><div id="direction-content"></div></div>';
		}
		$html .= '</div>';

		$googleScriptEnabled = $this->admin->getOption( 'assl_google_maps', 'assl_admin_advanced' );
		if ( $googleScriptEnabled == "si" ) {
			//Bottom Bar
			$html .= '<div class="assl-bottom-bar">';
			$html .= '<div class="assl-bottom-bar-results">';
			$html .= '</div>';
			$html .= '<div class="assl-bottom-bar-set-user-marker">';
			$html .= '<input type="text" id="set-new-user-marker" placeholder="' . __( 'Type a new start point', ASSL__TEXTDOMAIN ) . '"/>';
			$html .= '<label for="set-new-user-marker">' . __( 'Type a new start point', ASSL__TEXTDOMAIN ) . '</label>';
			$html .= '</div>';
		}

		$footerTextOnOff = $this->admin->getOption( 'assl_footer_onoff', 'assl_admin_layout' );
		if ( $footerTextOnOff ) {
			$html .= '<div class="assl-bottom-bar-copyright">';
			$footerText = $this->admin->getOption( 'assl_footer_text', 'assl_admin_layout' );
			$html .= $footerText ? $footerText : get_bloginfo('name');
			$html .= '</div>';
		}
		//credits
		$html .= '<div class="assl-credits">';
		$html .= '<a href="http://alfiosalanitri.it" target="_blank">powered by alfiosalanitri.it</a>';
		$html .= '</div>';

		$html .= '</div>';
		$html .= '</div>';
		$style = $mapStyle != '' ? $mapStyle : '[{"stylers": [{ "saturation": -100 }]}, {"featureType": "poi", "stylers": [{ "visibility": "off" }]}]';
		$html .= "<div id='assl-store-default-var' data-mapstyle='$style' data-storesettings='" . json_encode( $storeSettingsOption ) . "'></div>";
		return $html;
	}
}
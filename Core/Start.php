<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 10:40
 */

namespace AsStoreLocator;

use AsStoreLocator\Admin\Dep\Api;
use AsStoreLocator\Admin\Dep\Settings as AdminSettings;
use AsStoreLocator\Admin\Metabox\Geolocation;
use AsStoreLocator\Admin\Start as Admin;
use AsStoreLocator\CustomPost\Dep\Settings as StoreSettings;
use AsStoreLocator\CustomPost\Dep\TermMeta;
use AsStoreLocator\CustomPost\Lib\Ajax;
use AsStoreLocator\CustomPost\Lib\Shortcodes;
use AsStoreLocator\CustomPost\Lib\Utility;
use AsStoreLocator\CustomPost\Store;

/**
 * Class Start
 * @package AsStoreLocator
 */
class Start {

	/**
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @var
	 */
	private $wpVersion;

	/**
	 * @var Admin
	 */
	private $admin;
	/**
	 * @var Store
	 */
	private $customPost;
	/**
	 * @var Utility
	 */
	private $utility;
	/**
	 * @var Ajax
	 */
	private $ajax;
	/**
	 * @var Shortcodes
	 */
	private $shortcodes;

	/**
	 * Start constructor.
	 */
	public function __construct() {
		$this->wpVersion  = get_bloginfo('version');
		$this->admin      = new Admin( new Api, new AdminSettings );
		$this->customPost = new Store( new StoreSettings );
		$postTypeName     = $this->customPost->getPostTypeName();
		$postTypeCategory = $this->customPost->getCategory();
		if( version_compare($this->wpVersion, '4.4', '>=') ) {
			TermMeta::getInstance( $this->admin, $postTypeCategory );
		}
		$this->loadMetabox();
		$this->utility    = new Utility( $this->admin, $postTypeName, $postTypeCategory );
		$this->ajax       = new Ajax( $this->utility );
		$this->shortcodes = new Shortcodes( $this->utility );

		add_action( 'init', array( $this, 'loadTextdomain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScriptAndStyle' ) );
		if ( version_compare($this->wpVersion, '4.5', '>=') ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueueCustomStyle' ) );
		} else {
			add_action( 'wp_head', array( $this, 'printCustomStyle' ) );
		}
	}

	/**
	 * @return null
	 */
	public static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *
	 */
	public function loadTextdomain() {
		load_plugin_textdomain( ASSL__TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 *
	 */
	public function loadMetabox() {
		new Geolocation( $this->admin );
	}

	/**
	 *
	 */
	public function enqueueScriptAndStyle() {
		//Google Maps
		$googleScriptEnabled = $this->admin->getOption( 'assl_google_maps', 'assl_admin_advanced' );
		$lang                = $this->admin->getOption( 'assl_google_maps_language', 'assl_admin_advanced' );
		$googleApy           = $this->admin->getOption( 'assl_google_maps_apikey', 'assl_admin_advanced' );
		$key                 = ( isset( $googleApy ) && ! empty( $googleApy ) ) ? 'key=' . $googleApy . '&' : '';
		$enabled             = isset( $googleScriptEnabled ) ? $googleScriptEnabled : "si";
		if ( $enabled == "si" ) {
			$lang_code = isset( $lang ) ? $lang : 'it';
			wp_enqueue_script( 'assl-google-maps', 'https://maps.googleapis.com/maps/api/js?' . $key . 'libraries=places&language=' . $lang_code, array( 'jquery' ), ASSL__VERSION, true );
		}

		//Cluster
		$cluster_activation = $this->admin->getOption( 'assl_cluster_activation', 'assl_admin_map' );
		$clusterer          = $cluster_activation ? $cluster_activation : "si";
		if ( $clusterer == "si" ) {
			wp_enqueue_script( 'assl-markerclusterer', ASSL__PLUGIN_URL . 'assets/frontend/js/markerclusterer.min.js', array( 'jquery' ), '', true );
		}
		//Script Principale
		wp_enqueue_script( 'assl-script', ASSL__PLUGIN_URL . 'assets/frontend/js/assl-script.min.js', array( 'jquery' ), ASSL__VERSION, true );
		//localizzo script per chiamata ajax
		wp_localize_script(
			'assl-script',
			'assl_script_ajax',
			array(
				'ajax_url'              => site_url( 'wp-admin/admin-ajax.php' ),
				'main_script_enabled'   => $enabled == "si" ? 'enabled' : 'disabled'
			)
		);
		// Style
		wp_enqueue_style( 'assl-style', ASSL__PLUGIN_URL . 'assets/frontend/css/assl-style.css', array(), ASSL__VERSION, 'all' );
	}

	/**
	 * @return string
	 */
	private function getCustomStyle() {
		$top_bar_bg_color    = $this->admin->getOption( 'assl_top_bar_bg_color', 'assl_admin_layout' );
		$top_bar_color       = $this->admin->getOption( 'assl_top_bar_color', 'assl_admin_layout' );
		$top_bar_hover_color = $this->admin->getOption( 'assl_top_bar_hover_color', 'assl_admin_layout' );
		$title_color         = $this->admin->getOption( 'assl_title_color', 'assl_admin_layout' );
		$text_color          = $this->admin->getOption( 'assl_text_color', 'assl_admin_layout' );
		$bottom_bar_bg_color = $this->admin->getOption( 'assl_bottom_bar_bg_color', 'assl_admin_layout' );
		$bottom_bar_color    = $this->admin->getOption( 'assl_bottom_bar_color', 'assl_admin_layout' );

		$style = "";
		$style .= "#assl-store-locator-container .assl-top-bar, #assl-clear-radius, #getStoreByCat, #searchStore, #assl-store-locator-container label:before, #assl-list::-webkit-scrollbar-thumb, .assl-filters::-webkit-scrollbar-thumb, #direction-panel::-webkit-scrollbar-thumb {background-color: $top_bar_bg_color;}";
		$style .= "#assl-store-locator-container .assl-top-bar .list-inline li a, #assl-store-locator-container input[type=radio]:checked + label:before, #assl-store-locator-container input[type=checkbox]:checked + label:before, #assl-clear-radius, #getStoreByCat, #searchStore, #assl-store-locator-container #topbar-intro {color: $top_bar_color;}";
		$style .= "#assl-store-locator-container #assl-gmap-container #assl-list ul .assl-img .wp-post-image {border-color: $top_bar_bg_color;}";
		$style .= "#assl-store-locator-container .assl-top-bar .list-inline li a:hover, #assl-store-locator-container #assl-gmap-container .assl-type-filter:hover, .assl-calc-directions a:hover, .assl-calc-directions a.active, .direction-close :hover{color: $top_bar_hover_color;}";
		$style .= "#assl-clear-radius:hover, #getStoreByCat:hover, #searchStore:hover {background-color: $top_bar_hover_color;}";
		$style .= ".assl-map-box .markerTitle, #assl-store-locator-container #assl-gmap-container #assl-list ul .assl-text h2, .assl-calc-directions a, .direction-close  {color: $title_color;}";
		$style .= "#assl-store-locator-container #assl-gmap-container #assl-list ul .assl-text address, #assl-store-locator-container #assl-gmap-container #assl-list ul .assl-text h4, #assl-store-locator-container #assl-gmap-container #assl-list ul .assl-text a, #assl-store-locator-container #assl-gmap-container #assl-search-store, .assl-map-box .markerInfo a, .assl-map-box {color: $text_color;}";
		$style .= "#assl-store-locator-container .assl-bottom-bar, .assl-top-bar-store-results, #direction-close, #assl-store-locator-container #assl-gmap-container #direction-panel .adp-placemark {background-color: $bottom_bar_bg_color !important;color: $bottom_bar_color !important;}";
		$style .= ".assl-credits a {color: $bottom_bar_color !important;}";
		$style .= ".assl-credits {border-color: $bottom_bar_color !important;}";
		return $style;
	}

	/**
	 *
	 */
	function printCustomStyle() {
		echo "<style>";
		echo $this->getCustomStyle();
		echo "</style>";
	}

	/**
	 *
	 */
	function enqueueCustomStyle() {
		wp_enqueue_style( 'assl-custom-style', ASSL__PLUGIN_URL . 'assets/frontend/css/assl-custom-style.css' );
		$style = $this->getCustomStyle();
		wp_add_inline_style( 'assl-custom-style', $style );
	}
}
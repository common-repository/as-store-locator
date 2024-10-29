<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:04
 */

namespace AsStoreLocator\Admin\Dep;


/**
 * Class Settings
 * @package AsStoreLocator\Admin\Dep
 */
class Settings {

	/**
	 * @var array
	 */
	public $sections;

	/**
	 * @var array
	 */
	public $settings_fields;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->sections = array(
			array(
				'id'    => 'assl_admin_layout',
				'title' => __( 'Layout Settings', ASSL__TEXTDOMAIN )
			),
			array(
				'id'    => 'assl_admin_map',
				'title' => __( 'Map Settings', ASSL__TEXTDOMAIN )
			),
			array(
				'id'    => 'assl_admin_advanced',
				'title' => __( 'Advanced Settings', ASSL__TEXTDOMAIN )
			),
		);

		$this->settings_fields = array(
			'assl_admin_layout'   => array(
				array(
					'name'  => 'assl_color',
					'label' => '',
					'desc'  => __( 'Colors', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_top_bar_bg_color',
					'label'   => __( 'Top Bar Background Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#61b5e3',
				),
				array(
					'name'    => 'assl_top_bar_color',
					'label'   => __( 'Icons Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#ffffff',
				),
				array(
					'name'    => 'assl_top_bar_hover_color',
					'label'   => __( 'Icons Hover Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#2789aa',
				),
				array(
					'name'    => 'assl_title_color',
					'label'   => __( 'Title Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#3098d1',
				),
				array(
					'name'    => 'assl_text_color',
					'label'   => __( 'Text Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#828890',
				),
				array(
					'name'    => 'assl_bottom_bar_bg_color',
					'label'   => __( 'Footer Bar Background Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#3098d1',
				),
				array(
					'name'    => 'assl_bottom_bar_color',
					'label'   => __( 'Footer Bar Text Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#ffffff',
				),
				array(
					'name'  => 'assl_footer_title',
					'label' => '',
					'desc'  => __( 'Footer', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_footer_onoff',
					'label'   => __( 'Enable / Disable Footer Text', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'select',
					'default' => '1',
					'options' => array(
						'1' => 'On',
						'0' => 'Off'
					)
				),
				array(
					'name'    => 'assl_footer_text',
					'label'   => __( 'Footer Text', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Custom footer text (default: blog title)', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '',
				),
			),
			'assl_admin_map'      => array(
				array(
					'name'    => 'assl_km_mi',
					'label'   => __( 'Unit of Measurement', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Choose unit', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => 'km',
					'options' => array(
						'km'  => 'Km',
						'mi.' => 'Miles'
					)
				),
				array(
					'name'  => 'assl_lat_lng',
					'label' => '',
					'desc'  => __( 'Fallback GeoLocation', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_center_lat',
					'label'   => __( 'Latitude', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'If the visitors browser doesn\'t support the Geolocation.', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '37.513182',
				),
				array(
					'name'    => 'assl_center_lng',
					'label'   => __( 'Longitude', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'If the visitors browser doesn\'t support the Geolocation.', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '15.062063',
				),
				array(
					'name'  => 'assl_pin',
					'label' => '',
					'desc'  => __( 'Marker Image', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_pin_size_width',
					'label'   => __( 'PIN WIDTH SIZE', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Set the width to half the size of your image file.', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '32',
				),
				array(
					'name'    => 'assl_pin_size_height',
					'label'   => __( 'PIN HEIGHT SIZE', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Set the height to half the size of your image file.', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '32',
				),
				array(
					'name'  => 'assl_pin_image',
					'label' => __( 'User Marker Icon', ASSL__TEXTDOMAIN ),
					'desc'  => __( 'Retina Support: This image will need to be twice as large as the size of "PIN WIDTH SIZE and PIN HEIGHT SIZE" option.', ASSL__TEXTDOMAIN ),
					'type'  => 'file',
				),
				array(
					'name'  => 'assl_pin_category_image',
					'label' => __( 'Category Marker Icon ', ASSL__TEXTDOMAIN ),
					'desc'  => __( 'Retina Support: This image will need to be twice as large as the size of "PIN WIDTH SIZE and PIN HEIGHT SIZE" option.', ASSL__TEXTDOMAIN ),
					'type'  => 'file',
				),
				array(
					'name'  => 'assl_map_controls_title',
					'label' => '',
					'desc'  => __( 'Map Controls', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_map_controls_disableui',
					'label'   => __( 'Default UI', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'enables/disables the map controls', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => '0',
					'options' => array(
						'1' => 'Off',
						'0' => 'On'
					)
				),
				array(
					'name'    => 'assl_map_controls_maptype',
					'label'   => __( 'Map Type Control', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'enables/disables the Map Type control that lets the user toggle between map types (such as Map and Satellite)', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => '0',
					'options' => array(
						'1' => 'On',
						'0' => 'Off'
					)
				),
				array(
					'name'    => 'assl_map_controls_streetview',
					'label'   => __( 'Street View', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'enables/disables the Pegman control that lets the user activate a Street View panorama', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => '0',
					'options' => array(
						'1' => 'On',
						'0' => 'Off'
					)
				),
				array(
					'name'    => 'assl_map_controls_scrollwheel',
					'label'   => __( 'Scroll Wheel', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'enables/disables the mouse scroll whell zoom', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => '0',
					'options' => array(
						'1' => 'On',
						'0' => 'Off'
					)
				),
				array(
					'name'  => 'assl_map_style_title',
					'label' => '',
					'desc'  => __( 'Map Style', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_map_style',
					'label'   => __( 'Style', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Choose your style from  <a href="https://snazzymaps.com/" target="_blank">snazzymaps.com</a>', ASSL__TEXTDOMAIN ),
					'type'    => 'textarea',
					'default' => '[{"featureType":"landscape","stylers":[{"hue":"#FFBB00"},{"saturation":43.400000000000006},{"lightness":37.599999999999994},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#FFC200"},{"saturation":-61.8},{"lightness":45.599999999999994},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":51.19999999999999},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":52},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#0078FF"},{"saturation":-13.200000000000003},{"lightness":2.4000000000000057},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#00FF6A"},{"saturation":-1.0989010989011234},{"lightness":11.200000000000017},{"gamma":1}]}]'
				),
				array(
					'name'  => 'assl_map_radius_title',
					'label' => '',
					'desc'  => __( 'Radius', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_map_radius_stroke_color',
					'label'   => __( 'Stroke Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#000',
				),
				array(
					'name'    => 'assl_map_radius_stroke_opacity',
					'label'   => __( 'Stroke Opacity', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'text',
					'default' => '0.5',
				),
				array(
					'name'    => 'assl_map_radius_stroke_weight',
					'label'   => __( 'Stroke Weight', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'text',
					'default' => '1',
				),
				array(
					'name'    => 'assl_map_radius_fill_color',
					'label'   => __( 'Fill Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#000',
				),
				array(
					'name'    => 'assl_map_radius_fill_opacity',
					'label'   => __( 'Fill Opacity', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'text',
					'default' => '0.1',
				),
				array(
					'name'  => 'assl_map_directions_title',
					'label' => '',
					'desc'  => __( 'Directions Setting', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_map_directions_stroke_color',
					'label'   => __( 'Stroke Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#61b5e3',
				),
				array(
					'name'    => 'assl_map_directions_stroke_opacity',
					'label'   => __( 'Stroke Opacity', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'text',
					'default' => '0.7',
				),
				array(
					'name'    => 'assl_map_directions_stroke_weight',
					'label'   => __( 'Stroke Weight', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'text',
					'default' => '4',
				),
				array(
					'name'    => 'assl_map_directions_panel',
					'label'   => __( 'Directions Panel', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Show the directions render inside the directions panel to the right of the map.', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => 'si',
					'options' => array(
						'si' => __( 'Yes', ASSL__TEXTDOMAIN ),
						'no' => 'No'
					)
				),
				array(
					'name'  => 'assl_cluster_icons',
					'label' => '',
					'desc'  => __( 'Cluster Icons', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_cluster_activation',
					'label'   => __( 'Cluster Option', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Enable or Disable Cluster Function', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => 'si',
					'options' => array(
						'si' => __( 'Yes', ASSL__TEXTDOMAIN ),
						'no' => 'No'
					)
				),
				array(
					'name'    => 'assl_cluster_icon_color',
					'label'   => __( 'Text Color', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'color',
					'default' => '#fff',
				),
				array(
					'name'  => 'assl_cluster_icon_small',
					'label' => __( 'Small Icon', ASSL__TEXTDOMAIN ),
					'desc'  => 'PNG 30 x 30 px',
					'type'  => 'file',
				),
				array(
					'name'  => 'assl_cluster_icon_medium',
					'label' => __( 'Medium Icon', ASSL__TEXTDOMAIN ),
					'desc'  => 'PNG 40 x 40 px',
					'type'  => 'file',
				),
				array(
					'name'  => 'assl_cluster_icon_large',
					'label' => __( 'Large Icon', ASSL__TEXTDOMAIN ),
					'desc'  => 'PNG 50 x 50 px',
					'type'  => 'file',
				),
			),
			'assl_admin_advanced' => array(
				array(
					'name'  => 'assl_map_filter_title',
					'label' => '',
					'desc'  => __( 'Top Bar Filters', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_map_tot_founds',
					'label'   => __( 'Show / Hide Tot Stores Found', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Show / Hide Tot Stores Found on top bar', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => 'si',
					'options' => array(
						'si' => __( 'Yes', ASSL__TEXTDOMAIN ),
						'no' => 'No'
					)
				),
				array(
					'name'    => 'assl_map_filters',
					'label'   => __( 'Filters', ASSL__TEXTDOMAIN ),
					'desc'    => '',
					'type'    => 'multicheck',
					'options' => array(
						'type'   => __( 'Type', ASSL__TEXTDOMAIN ),
						'radius' => __( 'Radius', ASSL__TEXTDOMAIN ),
						'list'   => __( 'List', ASSL__TEXTDOMAIN ),
						'search' => __( 'Search', ASSL__TEXTDOMAIN ),
					),
				),
				array(
					'name'  => 'assl_map_filter_labelName_title',
					'label' => '',
					'desc'  => __( 'Filters Label Name', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_map_filter_labelName_type',
					'label'   => __( 'Type', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Choose a name for Type Label (default: Type)', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name'    => 'assl_map_filter_labelName_radius',
					'label'   => __( 'Radius', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Choose a name for Radius Label (default: Radius)', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name'    => 'assl_map_filter_labelName_list',
					'label'   => __( 'List', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Choose a name for List Label (default: List)', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name'    => 'assl_map_filter_labelName_search',
					'label'   => __( 'Type', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Choose a name for Search Label (default: Search)', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name'  => 'assl_map_script_title',
					'label' => '',
					'desc'  => __( 'Google Map Script', ASSL__TEXTDOMAIN ),
					'type'  => 'title',
				),
				array(
					'name'    => 'assl_google_maps_language',
					'label'   => __( 'Custom Language Code', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Choose your language code <a href="https://developers.google.com/maps/faq#languagesupport" target="_blank">Here</a>', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => 'it',
				),
				array(
					'name'    => 'assl_google_maps_apikey',
					'label'   => __( 'Google Maps API KEY', ASSL__TEXTDOMAIN ),
					'desc'    => __( '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank">Get an API key </a>', ASSL__TEXTDOMAIN ),
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name'    => 'assl_google_maps',
					'label'   => __( 'Main Script', ASSL__TEXTDOMAIN ),
					'desc'    => __( 'Select No to avoid conflicts with your theme or another plugin that includes google maps script. IMPORTANT: If disabled, the places search will be disabled.', ASSL__TEXTDOMAIN ),
					'type'    => 'select',
					'default' => 'si',
					'options' => array(
						'si' => __( 'Yes', ASSL__TEXTDOMAIN ),
						'no' => 'No'
					)
				),
			),
		);
	}
}
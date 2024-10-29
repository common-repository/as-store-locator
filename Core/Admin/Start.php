<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 10:51
 */

namespace AsStoreLocator\Admin;

use AsStoreLocator\Admin\Dep\Api;
use AsStoreLocator\Admin\Dep\Settings;

/**
 * Class Start
 * @package AsStoreLocator\Admin
 */
class Start {

	/**
	 * @var Api
	 */
	private $api;

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var array
	 */
	private $sections;

	/**
	 * @var array
	 */
	private $settings_fields;

	/**
	 * Start constructor.
	 *
	 * @param Api      $api
	 * @param Settings $settings
	 */
	public function __construct(Api $api, Settings $settings) {
		$this->api = $api;
		$this->settings = $settings;
		$this->sections = $this->settings->sections;
		$this->settings_fields = $this->settings->settings_fields;
		add_action( 'admin_enqueue_scripts', array($this, 'asslAdminStyle') );

		//Avviso admin
		add_action('admin_notices', array($this, 'noHttpsWarning') );
		add_action('admin_init', array($this, 'noHttpsWarningReaded') );
		add_action('admin_notices', array($this, 'notSavedTab') );
		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
	}

	/**
	 *
	 */
	public function asslAdminStyle()
	{
		wp_enqueue_style( 'assl-admin-style', ASSL__PLUGIN_URL . 'assets/backend/css/assl-admin-style.css' );
	}

	/**
	 * @return bool
	 */
	public function checkHttpsServer()
	{
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 */
	public function noHttpsWarning()
	{
		if(!$this->checkHttpsServer()) {
			global $current_user;
			$user_id = $current_user->ID;
			if ( ! get_user_meta($user_id, 'no_https_hide') ) {
				$html = '';
				$html .= '<div id="assl-admin-message" class="notice notice-warning is-dismissible">';
				$html .= '<p><strong>As Store Locator Plugin</strong> - ' .__("User Geolocation features only be accessible on \"secure origins\" (such as HTTPS). To use this feature, you should consider switching your application to a secure origin, such as HTTPS. See <a href='https://goo.gl/rStTGz' target='_blank'>https://goo.gl/rStTGz</a> for more details.", ASSL__TEXTDOMAIN) . '</p>';
				$html .= sprintf(__('<p><a href="%1$s">Ok, dismiss this notice.</a></p>', ASSL__TEXTDOMAIN), '?no_https_hide=0');
				$html .= '<br></div>';
				echo $html;
			}
		}
	}

	/**
	 *
	 */
	public function notSavedTab() {
		$html = '';

		$layout = get_option('assl_admin_layout');
		$map = get_option('assl_admin_map');
		$advanced = get_option('assl_admin_advanced');
		if(
			empty($layout)
			||
			empty($map)
			||
			empty($advanced)
		) {
			$html .= '<div id="assl-admin-message-tab" class="notice notice-error">';
			$html .= '<p><strong>As Store Locator Plugin</strong> - ' .__("Save all 3 settings tabs.", ASSL__TEXTDOMAIN) . '</p>';
			$html .= '<ol>';
			$html .= empty($layout) ? '<li>' . __("Layout Settings not saved;", ASSL__TEXTDOMAIN) . '</li>' : '';
			$html .= empty($map) ? '<li>' . __("Map Settings not saved;", ASSL__TEXTDOMAIN) . '</li>' : '';
			$html .= empty($advanced) ? '<li>' . __("Advanced Settings not saved;", ASSL__TEXTDOMAIN) . '</li>' : '';
			$html .= '</ol>';
			$html .= sprintf(__('<p><a href="%1$s">Go to settings and save <b>all tabs</b>.</a></p>', ASSL__TEXTDOMAIN), menu_page_url('assl_settings', false));
			$html .= '</div>';
		}
		echo $html;
	}

	/**
	 *
	 */
	public function noHttpsWarningReaded()
	{
		if(!$this->checkHttpsServer()) {
			global $current_user;
			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset($_GET['no_https_hide']) && '0' == $_GET['no_https_hide'] ) {
				add_user_meta($user_id, 'no_https_hide', 'true', true);
			}
		}
	}

	/**
	 *
	 */
	function admin_init()
	{

		//set the settings
		$this->api->set_sections( $this->get_settings_sections() );
		$this->api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->api->admin_init();
	}

	/**
	 *
	 */
	function admin_menu()
	{
		add_submenu_page('edit.php?post_type=store', __('Settings', ASSL__TEXTDOMAIN), __('Settings', ASSL__TEXTDOMAIN), 'manage_options', 'assl_settings', array($this, 'plugin_page'));
	}


	/**
	 * @return array
	 */
	function get_settings_sections()
	{
		$sections = $this->sections;
		return $sections;
	}

	/**
	 * @return array
	 */
	function get_settings_fields() {

		$settings_fields = $this->settings_fields;

		return $settings_fields;
	}

	/**
	 *
	 */
	function plugin_page() {

		$this->api->show_intro();

		echo '<div class="wrap">';

		$this->api->show_navigation();

		$this->api->show_forms();

		echo '</div>';

		echo '<hr>';
		echo __('Developed by', ASSL__TEXTDOMAIN).' <a href="http://alfiosalanitri.it" target="_blank">alfiosalanitri.it</a> | <a href="mailto:dev@alfiosalanitri.it">'.__('Support', ASSL__TEXTDOMAIN).'</a> | <a href="http://wordpress.plugin.alfiosalanitri.it" target="_blank">'.__('Plugin Homepage', ASSL__TEXTDOMAIN).'</a>';

	}

	/**
	 * @param        $option
	 * @param        $section
	 * @param string $default
	 *
	 * @return string
	 */
	public function getOption( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}
		return $default;
	}
}
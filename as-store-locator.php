<?php
/*
Plugin Name: As Store Locator
Plugin URI: http://wordpress.plugin.alfiosalanitri.it
Description: Simple Store Locator with Custom post type, Custom Taxonomy, Flat Design and Settings Page.
Author: Alfio Salanitri
Author URI: http://alfiosalanitri.it
Version: 1.5.6
Requires at least: 4.4
Tested up to: 4.9
Textdomain: as-store-locator
*/

if ( ! defined( 'WPINC' ) ) 
{
	die;
}
define( 'ASSL__PLUGIN_URL', plugin_dir_url( __FILE__ ) ); //url con slash finale
define( 'ASSL__PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); //path con slash finale
define( 'ASSL__TEXTDOMAIN', 'as-store-locator' );
define( 'ASSL__VERSION', '1.5.6' );
//composer autoload class
require (dirname(__FILE__) . '/vendor/autoload.php');
//on activation copy language files and rename option name
if ( class_exists( '\AsStoreLocator\ActivationHook' ) ) {
	register_activation_hook( __FILE__, array( '\AsStoreLocator\ActivationHook', 'copyLanguagesFiles' ) );
	register_activation_hook( __FILE__, array( '\AsStoreLocator\ActivationHook', 'renameSettingOptionName' ) );
}
//Plugin Init
add_action('plugins_loaded', array('\AsStoreLocator\Start', 'getInstance') );
<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 12:14
 */

namespace AsStoreLocator;


/**
 * Class ActivationHook
 * @package AsStoreLocator
 */
class ActivationHook {

	/**
	 * Copio i file con le traduzioni nella cartella languages
	 */
	public static function copyLanguagesFiles() {
		$locale = get_locale();

		$srcfile_po = ASSL__PLUGIN_DIR . 'languages/' . 'as-store-locator' . '-' . $locale . '.po';
		$dstfile_po = WP_LANG_DIR . '/plugins/' . 'as-store-locator' . '-' . $locale . '.po';
		copy( $srcfile_po, $dstfile_po );

		$srcfile_mo = ASSL__PLUGIN_DIR . 'languages/' . 'as-store-locator' . '-' . $locale . '.mo';
		$dstfile_mo = WP_LANG_DIR . '/plugins/' . 'as-store-locator' . '-' . $locale . '.mo';
		copy( $srcfile_mo, $dstfile_mo );
	}

	/**
	 * Se questa è la versione 1.5.X rinomino il nome opzione nel database
	 */
	public static function renameSettingOptionName() {
		if ( version_compare( ASSL__VERSION, '1.5', '>=') ) {
			global $wpdb;
			$optionTable = $wpdb->prefix . 'options';
			$check1       = $wpdb->get_row( "SELECT * FROM $optionTable WHERE option_name = 'AsslAdminSettings'" );
			if ( ! empty( $check1 ) ) {
				$wpdb->update(
					$optionTable,
					array( 'option_value' => $check1->option_value ),
					array( 'option_name'  => 'assl_admin_map' ),
					array( '%s' ),
					array( '%s' )
				);
				$wpdb->delete(
					$optionTable,
					array( 'option_name'  => 'AsslAdminSettings' ),
					array( '%s' )
				);
			}
		}
	}
}
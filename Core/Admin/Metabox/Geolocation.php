<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 12:09
 */

namespace AsStoreLocator\Admin\Metabox;

use AsStoreLocator\Admin\Start as Admin;

/**
 * Class Geolocation
 * @package AsStoreLocator\Admin\Metabox
 */
class Geolocation {

	/**
	 * @var Admin
	 */
	private $admin;

	private $googleScriptEnabled;

	/**
	 * Geolocation constructor.
	 *
	 * @param Admin $admin
	 */
	public function __construct( Admin $admin ) {
		$this->admin = $admin;
		$enabled = $this->admin->getOption( 'assl_google_maps', 'assl_admin_advanced' );
		$this->googleScriptEnabled = isset( $enabled ) ? $enabled : "si";

		//Aggiungo stile
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'enqueueScriptAndStyle' ), 11 );
		add_action( 'admin_print_scripts-post.php', array( $this, 'enqueueScriptAndStyle' ), 11 );

		//Aggiungo il metabox
		add_action( 'add_meta_boxes', array( $this, 'AsslMetaboxGeolocationAdd' ) );
		add_action( 'save_post', array( $this, 'AsslMetaboxGeolocationSave' ) );
	}

	/**
	 *
	 */
	public function enqueueScriptAndStyle() {

		$assl_language_code      = $this->admin->getOption( 'assl_google_maps_language', 'assl_admin_advanced' );
		$assl_google_maps_apikey = $this->admin->getOption( 'assl_google_maps_apikey', 'assl_admin_advanced' );

		$lang_code = isset( $assl_language_code ) ? $assl_language_code : 'it';
		$key       = ( isset( $assl_google_maps_apikey ) && ! empty( $assl_google_maps_apikey ) ) ? 'key=' . $assl_google_maps_apikey . '&' : '';

		global $post_type;
		if ( 'store' == $post_type ) {
			wp_enqueue_script( 'assl-metabox-maps', 'https://maps.googleapis.com/maps/api/js?' . $key . 'libraries=places&language=' . $lang_code, array( 'jquery' ) );
			wp_enqueue_script( 'assl-metabox-script', ASSL__PLUGIN_URL . 'assets/backend/js/assl-metabox-script.min.js', array( 'jquery' ) );
			wp_localize_script(
				'assl-metabox-script',
				'assl_metabox_script',
				array(
					'tag_lat_lng_required'           => __( "Category Tag, Latitude and Longitude are Required.", ASSL__TEXTDOMAIN ),
					'tag_lat_required'           => __( "Category Tag and Latitude are Required.", ASSL__TEXTDOMAIN ),
					'tag_lng_required'           => __( "Category Tag and Longitude are Required.", ASSL__TEXTDOMAIN ),
					'lat_lng_required'           => __( "Latitude and Longitude are Required.", ASSL__TEXTDOMAIN ),
					'tag_required'               => __( "Category Tag is Required.", ASSL__TEXTDOMAIN ),
					'lat_required'               => __( "Latitude is Required.", ASSL__TEXTDOMAIN ),
					'lng_required'               => __( "Longitude is Required.", ASSL__TEXTDOMAIN ),
					'main_script_enabled'   => $this->googleScriptEnabled == "si" ? 'enabled' : 'disabled'
				)
			);
		}
	}

	/**
	 *
	 */
	public function AsslMetaboxGeolocationAdd() {
		add_meta_box( 'assl_metabox_geo',
			__( 'Store Setting', ASSL__TEXTDOMAIN ),
			array( $this, 'AsslMetaboxGeolocationView' ),
			'store',
			'normal',
			'high'
		);
	}

	/**
	 * @param $post
	 */
	public function AsslMetaboxGeolocationView( $post ) {

		wp_nonce_field( 'assl_metabox', 'assl_metabox_nonce' );

		$assl_geo_lat     = get_post_meta( $post->ID, '_assl_geo_lat', true );
		$assl_geo_lng     = get_post_meta( $post->ID, '_assl_geo_lng', true );
		$assl_geo_via     = get_post_meta( $post->ID, '_assl_geo_via', true );
		$assl_geo_num     = get_post_meta( $post->ID, '_assl_geo_num', true );
		$assl_geo_citta   = get_post_meta( $post->ID, '_assl_geo_citta', true );
		$assl_geo_stato   = get_post_meta( $post->ID, '_assl_geo_stato', true );
		$assl_geo_cap     = get_post_meta( $post->ID, '_assl_geo_cap', true );
		$assl_geo_nazione = get_post_meta( $post->ID, '_assl_geo_nazione', true );
		$assl_geo_tel     = get_post_meta( $post->ID, '_assl_geo_tel', true );
		$assl_geo_tel2    = get_post_meta( $post->ID, '_assl_geo_tel2', true );
		$assl_geo_fax     = get_post_meta( $post->ID, '_assl_geo_fax', true );
		$assl_geo_url     = get_post_meta( $post->ID, '_assl_geo_url', true );
		$assl_geo_mail    = get_post_meta( $post->ID, '_assl_geo_mail', true );
		$assl_geo_mail2   = get_post_meta( $post->ID, '_assl_geo_mail2', true );
		?>
		<div id="assl_metabox_geo_container">
			<div id="assl_metabox_geolocalizzazione_text">
				<table id="tableaddress" width="100%">
					<?php if($this->googleScriptEnabled == 'si'): ?>
					<tr>
						<td colspan="2">
							<div class="type_address">
								<label
									for="assl_metabox_cerca_indirizzo"><?php _e( 'Search', ASSL__TEXTDOMAIN ); ?></label>
								<input type="text" id="assl_metabox_cerca_indirizzo" name="assl_metabox_cerca_indirizzo"
								       value=""
								       placeholder="<?php _e( 'Type an address...', ASSL__TEXTDOMAIN ); ?>"
								       style="width:100%; margin-bottom:10px;"/>
							</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td colspan="2">
							<h4 class="assl_title_geo"><?php _e( "Address", ASSL__TEXTDOMAIN ); ?></h4>
						</td>
					</tr>
					<tr>
						<td>
							<input class="assl_geo_via field" type="text" id="route" name="assl_metabox_geo_via"
							       value="<?php echo $assl_geo_via; ?>"
							       placeholder="<?php _e( 'Street', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="route"><?php _e( 'Street', ASSL__TEXTDOMAIN ); ?></label>
						</td>
						<td>
							<input class="assl_geo_num field" type="text" id="street_number" name="assl_metabox_geo_num"
							       value="<?php echo $assl_geo_num; ?>"
							       placeholder="<?php _e( 'Number', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="street_number">N.</label>
						</td>
					</tr>
					<tr>
						<td>
							<input class="assl_geo_citta field" type="text" id="locality" name="assl_metabox_geo_citta"
							       value="<?php echo $assl_geo_citta; ?>"
							       placeholder="<?php _e( 'City', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="locality"><?php _e( 'City', ASSL__TEXTDOMAIN ); ?></label>
						</td>
						<td>
							<input class="assl_geo_stato field" type="text" id="administrative_area_level_2"
							       name="assl_metabox_geo_stato" value="<?php echo $assl_geo_stato; ?>"
							       placeholder="<?php _e( 'State / Province', ASSL__TEXTDOMAIN ); ?>"/>
							<label
								for="administrative_area_level_2"><?php _e( 'State / Province', ASSL__TEXTDOMAIN ); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<input class="assl_geo_cap field" type="text" id="postal_code" name="assl_metabox_geo_cap"
							       value="<?php echo $assl_geo_cap; ?>"
							       placeholder="<?php _e( 'Zip Code', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="postal_code"><?php _e( 'Zip Code', ASSL__TEXTDOMAIN ); ?></label>
						</td>
						<td>
							<input class="assl_metabox_geo_nazione field" type="text" id="country"
							       name="assl_metabox_geo_nazione" value="<?php echo $assl_geo_nazione; ?>"
							       placeholder="<?php _e( 'Country', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="country"><?php _e( 'Country', ASSL__TEXTDOMAIN ); ?></label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<h4 class="assl_title_geo"><?php _e( "Coordinates", ASSL__TEXTDOMAIN ); ?></h4>
						</td>
					</tr>
					<tr>
						<td>
							<input class="assl_metabox_geo_lat field" type="text" id="assl_metabox_geo_lat"
							       name="assl_metabox_geo_lat" value="<?php echo $assl_geo_lat; ?>"
							       placeholder="<?php _e( 'Latitude', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="assl_metabox_geo_lat"><?php _e( 'Latitude', ASSL__TEXTDOMAIN ); ?></label>
						</td>
						<td>
							<input class="assl_metabox_geo_lng field" type="text" id="assl_metabox_geo_lng"
							       name="assl_metabox_geo_lng" value="<?php echo $assl_geo_lng; ?>"
							       placeholder="<?php _e( 'Longitude', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="assl_metabox_geo_lng"><?php _e( 'Longitude', ASSL__TEXTDOMAIN ); ?></label>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<h4 class="assl_title_geo"><?php _e( "Extra Info", ASSL__TEXTDOMAIN ); ?></h4>
						</td>
					</tr>
					<tr>
						<td>
							<input class="assl_metabox_geo_tel field" type="text" id="assl_metabox_geo_tel"
							       name="assl_metabox_geo_tel" value="<?php echo $assl_geo_tel; ?>"
							       placeholder="<?php _e( 'Phone', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="assl_metabox_geo_tel"><?php _e( 'Phone', ASSL__TEXTDOMAIN ); ?></label>
						</td>
						<td>
							<input class="assl_metabox_geo_tel2 field" type="text" id="assl_metabox_geo_tel2"
							       name="assl_metabox_geo_tel2" value="<?php echo $assl_geo_tel2; ?>"
							       placeholder="<?php _e( 'Phone 2', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="assl_metabox_geo_tel2"><?php _e( 'Phone 2', ASSL__TEXTDOMAIN ); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<input class="assl_metabox_geo_mail field" type="text" id="assl_metabox_geo_mail"
							       name="assl_metabox_geo_mail" value="<?php echo $assl_geo_mail; ?>"
							       placeholder="E-mail"/>
							<label for="assl_metabox_geo_mail">E-mail</label>
						</td>
						<td>
							<input class="assl_metabox_geo_mail2 field" type="text" id="assl_metabox_geo_mail2"
							       name="assl_metabox_geo_mail2" value="<?php echo $assl_geo_mail2; ?>"
							       placeholder="E-mail 2"/>
							<label for="assl_metabox_geo_mail2">E-mail 2</label>
						</td>
					</tr>
					<tr>
						<td>
							<input class="assl_metabox_geo_fax field" type="text" id="assl_metabox_geo_fax"
							       name="assl_metabox_geo_fax" value="<?php echo $assl_geo_fax; ?>"
							       placeholder="<?php _e( 'Fax', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="assl_metabox_geo_fax"><?php _e( 'Fax', ASSL__TEXTDOMAIN ); ?></label>
						</td>
						<td>
							<input class="assl_metabox_geo_url field" type="text" id="assl_metabox_geo_url"
							       name="assl_metabox_geo_url" value="<?php echo $assl_geo_url; ?>"
							       placeholder="<?php _e( 'Website', ASSL__TEXTDOMAIN ); ?>"/>
							<label for="assl_metabox_geo_url"><?php _e( 'Website', ASSL__TEXTDOMAIN ); ?></label>
						</td>
					</tr>
				</table>
				<?php if($this->googleScriptEnabled == 'si'): ?>
				<hr>
				<div id="map-canvas" style="height:400px; margin:0; padding:0;"></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function AsslMetaboxGeolocationSave( $post_id ) {
		global $post;

		// Check if our nonce is set.
		if ( ! isset( $_POST['assl_metabox_nonce'] ) ) {
			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['assl_metabox_nonce'], 'assl_metabox' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		/*
		*	GEOLOCALIZZAZIONE META KEY
		*
		*/
		if ( isset( $_POST['assl_metabox_geo_via'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_via', sanitize_text_field( $_POST['assl_metabox_geo_via'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_num'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_num', sanitize_text_field( $_POST['assl_metabox_geo_num'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_citta'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_citta', sanitize_text_field( $_POST['assl_metabox_geo_citta'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_stato'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_stato', sanitize_text_field( $_POST['assl_metabox_geo_stato'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_cap'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_cap', sanitize_text_field( $_POST['assl_metabox_geo_cap'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_nazione'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_nazione', sanitize_text_field( $_POST['assl_metabox_geo_nazione'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_lat'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_lat', sanitize_text_field( $_POST['assl_metabox_geo_lat'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_lng'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_lng', sanitize_text_field( $_POST['assl_metabox_geo_lng'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_tel'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_tel', sanitize_text_field( $_POST['assl_metabox_geo_tel'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_tel2'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_tel2', sanitize_text_field( $_POST['assl_metabox_geo_tel2'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_fax'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_fax', sanitize_text_field( $_POST['assl_metabox_geo_fax'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_url'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_url', esc_url_raw( $_POST['assl_metabox_geo_url'] ) );
		}

		if ( isset( $_POST['assl_metabox_geo_mail'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_mail', sanitize_email( $_POST['assl_metabox_geo_mail'] ) );
		}
		if ( isset( $_POST['assl_metabox_geo_mail2'] ) ) {
			update_post_meta( $post->ID, '_assl_geo_mail2', sanitize_email( $_POST['assl_metabox_geo_mail2'] ) );
		}
	}
}
<?php
/**
 * Project: AsStoreLocator
 * Developer: alfiosalanitri.it
 * Support: dev@alfiosalanitri.it
 * Date: 26/02/17
 * Time: 11:55
 */

namespace AsStoreLocator\CustomPost\Dep;

use AsStoreLocator\Admin\Start as Admin;

/**
 * Class TermMeta
 * @package AsStoreLocator\CustomPost\Dep
 */
class TermMeta {
	/**
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @var Admin
	 */
	private $admin;

	/**
	 * @var
	 */
	private $taxonomy;

	/**
	 * @var string
	 */
	private $termMetaKey = 'assl_store_image_category';

	/**
	 * TermMeta constructor.
	 *
	 * @param Admin $admin
	 * @param       $taxonomy
	 */
	public function __construct(Admin $admin, $taxonomy) {
		$this->admin = $admin;
		$this->taxonomy = $taxonomy;
		add_action( $this->taxonomy.'_add_form_fields', array ( $this, 'addImage' ), 10, 2 );
		add_action( 'created_'.$this->taxonomy, array ( $this, 'saveImage' ), 10, 2 );
		add_action( $this->taxonomy.'_edit_form_fields', array ( $this, 'updateImage' ), 10, 2 );
		add_action( 'edited_'.$this->taxonomy, array ( $this, 'updatedImage' ), 10, 2 );
	}

	/**
	 * @param Admin $admin
	 * @param       $taxonomy
	 *
	 * @return TermMeta|null
	 */
	public static function getInstance(Admin $admin, $taxonomy) {
		if(is_null(self::$instance)) {
			self::$instance = new self($admin, $taxonomy);
		}
		return self::$instance;
	}

	/**
	 * @param $taxonomy
	 */
	public function addImage ( $taxonomy ) {
		$pinWidth   = $this->admin->getOption( 'assl_pin_size_width', 'assl_admin_map' );
		$pinHeight  = $this->admin->getOption( 'assl_pin_size_height', 'assl_admin_map' );
		?>
		<div class="form-field term-group">
			<label for="assl-store-category-image-id"><?php _e('Image Category', ASSL__TEXTDOMAIN); ?></label>
			<input type="hidden" id="assl-store-category-image-id" name="<?php echo $this->termMetaKey; ?>" class="custom_media_url" value="">
			<p>PNG <?php _e("Size", ASSL__TEXTDOMAIN);?> (<?php echo isset($pinWidth) && intval($pinWidth) ? $pinWidth : 64; ?> x <?php echo isset($pinHeight) && intval($pinHeight) ? $pinHeight : 64; ?> px)</p>
			<div id="assl-store-category-image-wrapper"></div>
			<p>
				<input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', ASSL__TEXTDOMAIN ); ?>" />
				<input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', ASSL__TEXTDOMAIN ); ?>" />
			</p>
		</div>
		<?php
		$this->addScript();
	}

	/**
	 * @param $term_id
	 * @param $tt_id
	 */
	public function saveImage ( $term_id, $tt_id ) {
		if( isset( $_POST[$this->termMetaKey] ) && '' !== $_POST[$this->termMetaKey] ){
			$image = $_POST[$this->termMetaKey];
			add_term_meta( $term_id, $this->termMetaKey, $image, true );
		}
	}

	/**
	 * @param $term
	 * @param $taxonomy
	 */
	public function updateImage ( $term, $taxonomy ) {
		$pinWidth   = $this->admin->getOption( 'assl_pin_size_width', 'assl_admin_map' );
		$pinHeight  = $this->admin->getOption( 'assl_pin_size_height', 'assl_admin_map' );
		?>
		<tr class="form-field term-group-wrap">
			<th scope="row">
				<label for="assl-store-category-image-id"><?php _e( 'Image Category', ASSL__TEXTDOMAIN ); ?></label>
			</th>
			<td>
				<?php
				$image = get_term_meta( $term->term_id, $this->termMetaKey, true );
				//Compatibilità con il vecchio plugin
				if(is_array($image)) {
					$image_id = $image['id'];
				} else {
					$image_id = $image;
				}
				?>
				<input type="hidden" id="assl-store-category-image-id" name="<?php echo $this->termMetaKey; ?>" value="<?php echo $image_id; ?>">
				<p>PNG <?php _e("Size", ASSL__TEXTDOMAIN);?> (<?php echo isset($pinWidth) && intval($pinWidth) ? $pinWidth : 64; ?> x <?php echo isset($pinHeight) && intval($pinHeight) ? $pinHeight : 64; ?> px)</p>
				<div id="assl-store-category-image-wrapper">
					<?php if ( $image_id ) { ?>
						<?php echo wp_get_attachment_image ( $image_id, 'full' ); ?>
					<?php } ?>
				</div>
				<p>
					<input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', ASSL__TEXTDOMAIN ); ?>" />
					<input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', ASSL__TEXTDOMAIN ); ?>" />
				</p>
			</td>
		</tr>
		<?php
		$this->addScript();
	}

	/**
	 * @param $term_id
	 * @param $tt_id
	 */
	public function updatedImage ( $term_id, $tt_id ) {
		if( isset( $_POST[$this->termMetaKey] ) && '' !== $_POST[$this->termMetaKey] ){
			$image = $_POST[$this->termMetaKey];
			update_term_meta( $term_id, $this->termMetaKey, $image );
		} else {
			update_term_meta( $term_id, $this->termMetaKey, '' );
		}
	}

	/**
	 *
	 */
	public function addScript() { ?>
		<script>
			jQuery(document).ready( function($) {
				function ct_media_upload(button_class) {
					$('body').on('click', button_class, function(e) {
						e.preventDefault();
						var file_frame;
						if (file_frame) file_frame.close();

						file_frame = wp.media.frames.file_frame = wp.media({
							title: jQuery(this).data('uploader-title'),
							button: {
								text: jQuery(this).data('uploader-button-text')
							},
							multiple: false
						});

						file_frame.on( 'select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();

							$('#assl-store-category-image-id').val(attachment.id);
							$('#assl-store-category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
							$('#assl-store-category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
						});

						file_frame.open();
					});
				}
				ct_media_upload('.ct_tax_media_button.button');
				$('body').on('click','.ct_tax_media_remove',function(){
					$('#assl-store-category-image-id').val('');
					$('#assl-store-category-image-wrapper').empty().html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
				});
				// Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
				$(document).ajaxComplete(function(event, xhr, settings) {
					var queryStringArr = settings.data.split('&');
					if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
						var xml = xhr.responseXML;
						$response = $(xml).find('term_id').text();
						if($response!=""){
							// Clear the thumb image
							$('#assl-store-category-image-wrapper').html('');
						}
					}
				});
			});
		</script>
	<?php }

}
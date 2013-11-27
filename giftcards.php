<?php
/*
Plugin Name: Gift Cards for WooCommerce
Plugin URI: http://ryanpletcher.com
Description: Gift Cards for WooCommerce allows you to offer gift cards to your customer and allow them to place orders using them.
Version: 1.0
Author: Ryan Pletcher
Author URI: http://ryanpletcher.com
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'RPWCGC_CORE_TEXT_DOMAIN', 'rpgiftcards' );
define( 'RPWCGC_PATH', plugin_dir_path( __FILE__ ) );
define( 'RPWCGC_VERSION', '1.0' );
define( 'RPWCGC_FILE', plugin_basename( __FILE__ ) );
define( 'RPWCGC_URL', plugins_url( 'woocommerce-gift-cards', 'giftcards.php' ) );


add_action('plugins_loaded', 'rpgc_woocommerce', 0);

function rpgc_woocommerce() {

	if (!class_exists('woocommerce'))
		return;

	add_action( 'init', 'rpgc_create_post_type' );
	/**
	 * Create the Giftcard Post Type and what you can do with it.
	 *
	 */
	function rpgc_create_post_type() {
		$show_in_menu = current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true;

		register_post_type( 'rp_shop_giftcard',
			array(
				'labels' => array(
					'name' 					=> __( 'Gift Cards', RPWCGC_CORE_TEXT_DOMAIN ),
					'singular_name' 		=> __( 'Gift Card', RPWCGC_CORE_TEXT_DOMAIN ),
					'menu_name'				=> _x( 'Gift Cards', 'Admin menu name', RPWCGC_CORE_TEXT_DOMAIN ),
					'add_new' 				=> __( 'Add Gift Card', RPWCGC_CORE_TEXT_DOMAIN ),
					'add_new_item' 			=> __( 'Add New Gift Card', RPWCGC_CORE_TEXT_DOMAIN ),
					'edit' 					=> __( 'Edit', RPWCGC_CORE_TEXT_DOMAIN ),
					'edit_item' 			=> __( 'Edit Gift Card', RPWCGC_CORE_TEXT_DOMAIN ),
					'new_item' 				=> __( 'New Gift Card', RPWCGC_CORE_TEXT_DOMAIN ),
					'view' 					=> __( 'View Gift Cards', RPWCGC_CORE_TEXT_DOMAIN ),
					'view_item' 			=> __( 'View Gift Card', RPWCGC_CORE_TEXT_DOMAIN ),
					'search_items' 			=> __( 'Search Gift Cards', RPWCGC_CORE_TEXT_DOMAIN ),
					'not_found' 			=> __( 'No Gift Cards found', RPWCGC_CORE_TEXT_DOMAIN ),
					'not_found_in_trash' 	=> __( 'No Gift Cards found in trash', RPWCGC_CORE_TEXT_DOMAIN ),
					'parent' 				=> __( 'Parent Gift Card', RPWCGC_CORE_TEXT_DOMAIN )
				),
			'public'		=> true,
			'has_archive'	=> true,
			'show_in_menu' 	=> $show_in_menu,
			'hierarchical'	=> false,
			'supports' 		=> array( 'title', 'comments' )
			)
		);
	}

	add_action( 'wp_enqueue_scripts', 'rpgc_enqueue' );
	/**
	 * Add the required scripts to the plugin.
	 *
	 */
	function rpgc_enqueue() {
		$rpgc_url = plugins_url() . '/woocommerce-gift-cards';
		wp_enqueue_style( 'rpgc_style', RPWCGC_URL . '/style/style.css' );
	}
	
	
	add_action( 'admin_enqueue_scripts', 'rpgc_admin_enqueue' );
	/**
	 * Add the required scripts to the plugin.
	 *
	 */
	function rpgc_admin_enqueue() {
		global $woocommerce, $typenow, $post, $wp_scripts;
		
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
			$post = get_post( $_GET['post'] );
			$typenow = $post->post_type;
		}

		if ( $typenow == 'rp_shop_giftcard' ) {

			wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
			
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
			wp_enqueue_style( 'jquery-ui-style', RPWCGC_URL . '/style/jquery-ui.css' );
			wp_enqueue_style( 'font-awesome_styles', RPWCGC_URL . '/style/font-awesome/css/font-awesome.min.css' ); // Adds the custom icon style

			wp_enqueue_script( 'woocommerce_writepanel' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'chosen' );
			wp_enqueue_script( 'plupload-all' );

			$woocommerce_witepanel_params = array(
				'remove_item_notice' 			=> __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', RPWCGC_CORE_TEXT_DOMAIN ),
				'i18n_select_items'				=> __( 'Please select some items.', RPWCGC_CORE_TEXT_DOMAIN ),
				'remove_item_meta'				=> __( 'Remove this item meta?', RPWCGC_CORE_TEXT_DOMAIN ),
				'remove_attribute'				=> __( 'Remove this attribute?', RPWCGC_CORE_TEXT_DOMAIN ),
				'name_label'					=> __( 'Name', RPWCGC_CORE_TEXT_DOMAIN ),
				'remove_label'					=> __( 'Remove', RPWCGC_CORE_TEXT_DOMAIN ),
				'click_to_toggle'				=> __( 'Click to toggle', RPWCGC_CORE_TEXT_DOMAIN ),
				'values_label'					=> __( 'Value(s)', RPWCGC_CORE_TEXT_DOMAIN ),
				'text_attribute_tip'			=> __( 'Enter some text, or some attributes by pipe (|) separating values.', RPWCGC_CORE_TEXT_DOMAIN ),
				'visible_label'					=> __( 'Visible on the product page', RPWCGC_CORE_TEXT_DOMAIN ),
				'used_for_variations_label'		=> __( 'Used for variations', RPWCGC_CORE_TEXT_DOMAIN ),
				'new_attribute_prompt'			=> __( 'Enter a name for the new attribute term:', RPWCGC_CORE_TEXT_DOMAIN ),
				'calc_totals' 					=> __( 'Calculate totals based on order items, discounts, and shipping?', RPWCGC_CORE_TEXT_DOMAIN ),
				'calc_line_taxes' 				=> __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', RPWCGC_CORE_TEXT_DOMAIN ),
				'copy_billing' 					=> __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', RPWCGC_CORE_TEXT_DOMAIN ),
				'load_billing' 					=> __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', RPWCGC_CORE_TEXT_DOMAIN ),
				'load_shipping' 				=> __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', RPWCGC_CORE_TEXT_DOMAIN ),
				'featured_label'				=> __( 'Featured', RPWCGC_CORE_TEXT_DOMAIN ),
				'prices_include_tax' 			=> esc_attr( get_option('woocommerce_prices_include_tax') ),
				'round_at_subtotal'				=> esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
				'no_customer_selected'			=> __( 'No customer selected', RPWCGC_CORE_TEXT_DOMAIN ),
				'plugin_url' 					=> $woocommerce->plugin_url(),
				'ajax_url' 						=> admin_url('admin-ajax.php'),
				'order_item_nonce' 				=> wp_create_nonce("order-item"),
				'add_attribute_nonce' 			=> wp_create_nonce("add-attribute"),
				'save_attributes_nonce' 		=> wp_create_nonce("save-attributes"),
				'calc_totals_nonce' 			=> wp_create_nonce("calc-totals"),
				'get_customer_details_nonce' 	=> wp_create_nonce("get-customer-details"),
				'search_products_nonce' 		=> wp_create_nonce("search-products"),
				'calendar_image'				=> $woocommerce->plugin_url().'/assets/images/calendar.png',
				'apply_giftcard_nonce'          => wp_create_nonce( "apply-giftcard" ),
				'base_country'					=> $woocommerce->countries->get_base_country(),
				'currency_format_num_decimals'	=> absint( get_option( 'woocommerce_price_num_decimals' ) ),
				'currency_format_symbol'		=> get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'	=> esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
				'currency_format_thousand_sep'	=> esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
				'currency_format'				=> esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
				'product_types'					=> array_map( 'sanitize_title', get_terms( 'product_type', array( 'hide_empty' => false, 'fields' => 'names' ) ) ),
				'default_attribute_visibility'  => apply_filters( 'default_attribute_visibility', false ),
				'default_attribute_variation'   => apply_filters( 'default_attribute_variation', false )
			);
		
			wp_localize_script( 'woocommerce_writepanel', 'woocommerce_writepanel_params', $woocommerce_witepanel_params );
		
		wp_enqueue_style('farbtastic');
		}
		
		do_action('woocommerce_admin_css');
		
	}
		

	
	add_action( 'add_meta_boxes', 'rpgc_meta_boxes' );
	/**
	 * Sets up the new meta box for the creation of a gift card.
	 * Removes the other three Meta Boxes that are not needed.
	 * 
	 */
	function rpgc_meta_boxes() {
		global $post;

		add_meta_box(
			'rpgc-woocommerce-data',
			__( 'Gift Card Data', RPWCGC_CORE_TEXT_DOMAIN ),
			'rpgc_meta_box', 
			'rp_shop_giftcard', 
			'normal', 
			'high'
		);


		if ( ! isset( $_GET['action'] ) )
				remove_post_type_support('rp_shop_giftcard', 'title');


		remove_meta_box( 'woothemes-settings', 'rp_shop_giftcard' , 'normal' );
		remove_meta_box( 'commentstatusdiv', 'rp_shop_giftcard' , 'normal' );
		remove_meta_box( 'slugdiv', 'rp_shop_giftcard' , 'normal' );
	}


	/**
	 * Creates the Giftcard Meta Box in the admin control panel.
	 * Allows you to create a giftcard manually.
	 * 
	 */
	function rpgc_meta_box( $post ) {
		global $woocommerce;

		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
			?>
		<style type="text/css">
			#edit-slug-box, #minor-publishing-actions { display:none }
		</style>
		
		<div id="giftcard_options" class="panel woocommerce_options_panel">
		<?php

			echo '<div class="options_group">';
			// Description
			woocommerce_wp_textarea_input( 
				array( 
					'id' => 'rpgc_description',
					'label' => __( 'Gift Card description', RPWCGC_CORE_TEXT_DOMAIN ),
					'placeholder' => '',
					'description' => __( 'Optionally enter a description for this gift card for your reference.', RPWCGC_CORE_TEXT_DOMAIN ), 
					
				) 
			);
			
			echo '<h2>Who are you sending this to?</h2>';
			// To
			woocommerce_wp_text_input(
				array(
					'id' => 'rpgc_to',
					'label' => __( 'To', RPWCGC_CORE_TEXT_DOMAIN ),
					'placeholder' => '',
					'description' => __( 'Who is getting this gift card.', RPWCGC_CORE_TEXT_DOMAIN ),
				)
			);
			// To Email
			woocommerce_wp_text_input( 
				array( 
					'id' => 'rpgc_email_to',
					'label' => __( 'Email To', RPWCGC_CORE_TEXT_DOMAIN ),
					'placeholder' => '',
					'description' => __( 'What email should we send this gift card to.', RPWCGC_CORE_TEXT_DOMAIN ), 
				) 
			);
			// From
			woocommerce_wp_text_input( 
				array( 
					'id' => 'rpgc_from',
					'label' => __( 'From', RPWCGC_CORE_TEXT_DOMAIN ),
					'placeholder' => '',
					'description' => __( 'Who is sending this gift card.', RPWCGC_CORE_TEXT_DOMAIN ), 
				) 
			);
			// From Email
			woocommerce_wp_text_input( 
				array( 
					'id' => 'rpgc_email_from',
					'label' => __( 'Email From', RPWCGC_CORE_TEXT_DOMAIN ),
					'placeholder' => '',
					'description' => __( 'What email account is sending this gift card.', RPWCGC_CORE_TEXT_DOMAIN ), 
				) 
			);
			
			echo '</div><div class="options_group">';
			
			echo '<h2>Personalize it</h2>';
			// Amount
			woocommerce_wp_text_input( 
				array( 
					'id' 				=> 'rpgc_amount',
					'label'				=> __( 'Gift Card amount', RPWCGC_CORE_TEXT_DOMAIN ),
					'placeholder'		=> '0.00',
					'description'		=> __( 'Value of the Gift Card.', RPWCGC_CORE_TEXT_DOMAIN ),
					'type'				=> 'number',
					'custom_attributes'	=> array(
						'step' 	=> 'any',
						'min'	=> '0'
					)
				)
			);
			if ( isset( $_GET['action']  ) ) {
				if ( $_GET['action'] == 'edit' ){
					// Remaining Balance
					woocommerce_wp_text_input( 
						array( 
							'id'				=> 'rpgc_balance',
							'label'				=> __( 'Gift Card balance', RPWCGC_CORE_TEXT_DOMAIN ),
							'placeholder'		=> '0.00',
							'description'		=> __( 'Remaining Balance of the Gift Card.', RPWCGC_CORE_TEXT_DOMAIN ),
							'type'				=> 'number',
							'custom_attributes'	=> array(
								'step' 	=> 'any',
								'min'	=> '0'
							)
						)
					);
				}
			}
			// Notes
			woocommerce_wp_textarea_input( 
				array( 
					'id' => 'rpgc_note',
					'label' => __( 'Gift Card Note', RPWCGC_CORE_TEXT_DOMAIN ),
					'description' => __( 'Optionally Message you can enter to your customer.', RPWCGC_CORE_TEXT_DOMAIN )
				) 
			);

			// Expiry date
			woocommerce_wp_text_input( 
				array( 
					'id' => 'rpgc_expiry_date',
					'label' => __( 'Expiry date', RPWCGC_CORE_TEXT_DOMAIN ), 
					'placeholder' => _x('Never expire', 'placeholder', RPWCGC_CORE_TEXT_DOMAIN ), 
					'description' => __( 'The date this coupon will expire, <code>YYYY-MM-DD</code>.', RPWCGC_CORE_TEXT_DOMAIN ), 
					'class' => 'short date-picker',
					'custom_attributes' => array( 
						'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"
					) 
				)
			);

			do_action( 'rpgc_woocommerce_options' );

			echo '</div>';


		?>
	</div>
	<?php
	}

	
	add_action( 'save_post', 'rpgc_process_giftcard_meta', 10, 2 );
	/**
	 * 
	 * 
	 * 
	 */
	function rpgc_process_giftcard_meta( $post_id, $post ) {
		global $wpdb, $woocommerce_errors;
		
		$description		= '';
		$to 				= '';
		$toEmail			= '';
		$from 				= '';
		$fromEmail			= '';
		$amount 			= '';
		$balance			= '';
		$note				= '';
		$expiry_date 		= '';
		
		// Ensure coupon code is correctly formatted
		$wpdb->update( $wpdb->posts, array( 'post_title' => $post->post_title ), array( 'ID' => $post_id ) );

		// Check for dupe coupons
		$giftcard_found = $wpdb->get_var( $wpdb->prepare( "
			SELECT $wpdb->posts.ID
	  		FROM $wpdb->posts
			WHERE $wpdb->posts.post_type = 'rp_shop_giftcard'
			AND $wpdb->posts.post_status = 'publish'
			AND $wpdb->posts.post_title = '%s'
		", $post->post_title ) );

		if ( $giftcard_found )
			$woocommerce_errors[] = __( 'Gift Card code already exists - customers will use the latest coupon with this code.', RPWCGC_CORE_TEXT_DOMAIN );

		if ( isset( $_POST['rpgc_description'] ) ) {
			$description		= woocommerce_clean( $_POST['rpgc_description'] );
			update_post_meta( $post_id, 'rpgc_description', $description );
		}
		if ( isset( $_POST['rpgc_to'] ) ) {
			$to 				= woocommerce_clean( $_POST['rpgc_to'] );
			update_post_meta( $post_id, 'rpgc_to', $to );
		}
		if ( isset( $_POST['rpgc_email_to'] ) ) {
			$toEmail			= woocommerce_clean( $_POST['rpgc_email_to'] );
			update_post_meta( $post_id, 'rpgc_email_to', $toEmail );
		}
		if ( isset( $_POST['rpgc_from'] ) ) {
			$from 				= woocommerce_clean( $_POST['rpgc_from'] );
			update_post_meta( $post_id, 'rpgc_from', $from );
		}
		if ( isset( $_POST['rpgc_email_from'] ) ) {
			$fromEmail			= woocommerce_clean( $_POST['rpgc_email_from'] );
			update_post_meta( $post_id, 'rpgc_email_from', $fromEmail );
		}
		if (isset( $_POST['rpgc_amount'] ) ) {
			$amount 			= woocommerce_clean( $_POST['rpgc_amount'] );
			update_post_meta( $post_id, 'rpgc_amount', $amount );
		}
		if ( isset( $_GET['action']  ) ) {
			if ( isset( $_POST['rpgc_balance'] ) ) {
				$balance 		= woocommerce_clean( $_POST['rpgc_balance'] );
				update_post_meta( $post_id, 'rpgc_balance', $balance );
			}
		} else {
			if ( isset( $_POST['rpgc_amount'] ) ) {
				$balance 		= woocommerce_clean( $_POST['rpgc_amount'] );
				update_post_meta( $post_id, 'rpgc_balance', $balance );
			}
		}
		if ( isset( $_POST['rpgc_note'] ) ) {
			$note				= woocommerce_clean( $_POST['rpgc_note'] );
			update_post_meta( $post_id, 'rpgc_note', $note );
		}
		if ( isset( $_POST['rpgc_expiry_date'] ) ) {
			$expiry_date		= woocommerce_clean( $_POST['rpgc_expiry_date'] );
			update_post_meta( $post_id, 'rpgc_expiry_date', $expiry_date );
		}
	
		/* Deprecated - same hook name as in the meta */ 
		do_action( 'woocommerce_rpgc_options' );
		do_action( 'woocommerce_rpgc_options_save' );
	}


	add_filter( 'manage_edit-rp_shop_giftcard_columns', 'rpgc_add_columns' );

	function rpgc_add_columns($columns) {
		$new_columns = ( is_array($columns) ) ? $columns : array();
		unset( $new_columns['date'] );
		unset( $new_columns['comments'] );

		//all of your columns will be added before the actions column on the Giftcard page

		$new_columns["amount"] 			= __( 'Giftcard Amount', RPWCGC_CORE_TEXT_DOMAIN );
		$new_columns["balance"] 		= __( 'Remaining Balance', RPWCGC_CORE_TEXT_DOMAIN );
		$new_columns["buyer"] 			= __( 'Buyer', RPWCGC_CORE_TEXT_DOMAIN );
		$new_columns["recipient"] 		= __( 'Recipient', RPWCGC_CORE_TEXT_DOMAIN );
		$new_columns["expiry_date"] 	= __( 'Expiry date', RPWCGC_CORE_TEXT_DOMAIN );

		$new_columns['comments'] 		= $columns['comments'];
		$new_columns['date'] 			= $columns['date'];
		
		return $new_columns;
	}


	add_action( 'manage_rp_shop_giftcard_posts_custom_column', 'rpgc_custom_columns', 2 );
	/**
	 * 
	 *
	 * 
	 */
	function rpgc_custom_columns( $column ) {
		global $post, $woocommerce;

		switch ( $column ) {
			
			case "buyer" :
				echo '<div><strong>' . esc_html( get_post_meta( $post->ID, 'rpgc_from', true ) ) . '</strong><br />';
				echo '<span style="font-size: 0.9em">' . esc_html( get_post_meta( $post->ID, 'rpgc_email_from', true ) ) . '</div>';
			break;
			
			case "recipient" :
				echo '<div><strong>' . esc_html( get_post_meta( $post->ID, 'rpgc_to', true ) ) . '</strong><br />';
				echo '<span style="font-size: 0.9em">' . esc_html( get_post_meta( $post->ID, 'rpgc_email_to', true ) ) . '</span></div>';
			break;
			
			case "amount" :
				$price = get_post_meta( $post->ID, 'rpgc_amount', true );
				$currency_symbol = get_woocommerce_currency_symbol();
				
				echo sprintf( get_woocommerce_price_format(), $currency_symbol, $price );
				
			break;
			
			case "balance" :
				$price = get_post_meta( $post->ID, 'rpgc_balance', true );
				$currency_symbol = get_woocommerce_currency_symbol();
				
				echo sprintf( get_woocommerce_price_format(), $currency_symbol, $price );
				
			break;

			case "expiry_date" :
				$expiry_date = get_post_meta($post->ID, 'rpgc_expiry_date', true);

				if ( $expiry_date )
					echo esc_html( date_i18n( 'F j, Y', strtotime( $expiry_date ) ) );
				else
					echo '&ndash;';
			break;
		}
	}


	add_filter( 'wp_insert_post_data' , 'rpgc_create_number' , '99', 2 );
	/**
	 * Creates a random 15 digit giftcard number
	 *
	 * 
	 */
	function rpgc_create_number( $data , $postarr ) {
  		if ( ( $data['post_type'] == 'rp_shop_giftcard' ) && ( ( $data['post_title'] == "" ) || ( $data['post_title'] == "Auto Draft" ) ) ) {
 
  			$randomNumber = substr(number_format(time() * rand(),0,'',''),0,15);
    		$data['post_title'] = $randomNumber;
    		$data['post_name'] = $randomNumber;
    		$data['post_status'] = 'publish';
  		}
  		
  		return $data;
	}


	add_action( 'woocommerce_before_checkout_form', 'rpgc_checkout_form', 10 ); 

	if ( ! function_exists( 'rpgc_checkout_form' ) ) {

		/**
		 * Output the Giftcard form for the checkout.
		 *
		 * @access public
		 * @subpackage	Checkout
		 * @return void
		 */
		function rpgc_checkout_form() {
			global $woocommerce;

			//get_template( '/woocommerce-gift-cards/form-giftcard.php', array( 'checkout' => $woocommerce->checkout() ) );

			if ( ! $woocommerce->cart->coupons_enabled() )
				return;

			$info_message = apply_filters('woocommerce_checkout_coupon_message', __( 'Have a giftcard?', RPWCGC_CORE_TEXT_DOMAIN ));
			?>

			<p class="woocommerce-info"><?php echo $info_message; ?> <a href="#" class="showgiftcard"><?php _e( 'Click here to enter your giftcard', RPWCGC_CORE_TEXT_DOMAIN ); ?></a></p>

			<form class="checkout_giftcard" method="post" style="display:none">

				<p class="form-row form-row-first">
					<input type="text" name="giftcard_code" class="input-text" placeholder="<?php _e( 'Gift Card', RPWCGC_CORE_TEXT_DOMAIN ); ?>" id="giftcard_code" value="" />
				</p>

				<p class="form-row form-row-last">
					<input type="submit" class="button" name="apply_giftcard" value="<?php _e( 'Apply Giftcard', RPWCGC_CORE_TEXT_DOMAIN ); ?>" />
				</p>

				<div class="clear"></div>
			</form>
			
			<script>
				jQuery(document).ready(function($) {
					$('a.showgiftcard').click(function(){
						$('.checkout_giftcard').slideToggle();
						$('#giftcard_code').focus();
							return false;
						});
						
						/* AJAX Coupon Form Submission */
						$('form.checkout_giftcard').submit( function() {
							var $form = $(this);

							if ( $form.is('.processing') ) return false;

							$form.addClass('processing').block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});

							var data = {
								action: 			'woocommerce_apply_giftcard',
								security: 			'apply-giftcard',
								giftcard_code:		$form.find('input[name=giftcard_code]').val()
							};

							$.ajax({
								type: 		'POST',
								url: 		woocommerce_params.ajax_url,
								data: 		data,
								success: 	function( code ) {
									$('.woocommerce-error, .woocommerce-message').remove();
									$form.removeClass('processing').unblock();

									if ( code ) {
										$form.before( code );
										$form.slideUp();

										$('body').trigger('update_checkout');
									}
								},
								dataType: 	"html"
							});
							return false;
						});
				
				});
			
			</script>
			
			<?php		
		}
	}
	
	
	add_action('wp_ajax_woocommerce_apply_giftcard', 'woocommerce_ajax_apply_giftcard');
	add_action('wp_ajax_nopriv_woocommerce_apply_giftcard', 'woocommerce_ajax_apply_giftcard');
	/**
	 * AJAX apply coupon on checkout page
	 * 
	 * @access public
	 * @return void
	 */
	function woocommerce_ajax_apply_giftcard() {
		global $woocommerce, $wpdb;
			
		if ( ! empty( $_POST['giftcard_code'] ) ) {
			$giftCardNumber = sanitize_text_field( $_POST['giftcard_code'] );
				
			if( $giftCardNumber <> $woocommerce->session->giftcard_id ) {

				// Check for Giftcard
				$giftcard_found = $wpdb->get_var( $wpdb->prepare( "
					SELECT $wpdb->posts.ID
					FROM $wpdb->posts
					WHERE $wpdb->posts.post_type = 'rp_shop_giftcard'
					AND $wpdb->posts.post_status = 'publish'
					AND $wpdb->posts.post_title = '%s'
				", $giftCardNumber ) );

				$orderTotal = (float) $woocommerce->cart->total;

				if( $giftcard_found ) {
					// Valid Gift Card Entered
					$oldBalance = get_post_meta( $giftcard_found, 'rpgc_balance' );

					if( is_string( $oldBalance[0] ) )  // Determin if the Value from $oldBalance is a String and convert it
						$oldGiftcardValue = (float) $oldBalance[0];

					if( is_string( $orderTotal ) )   // Determin if the Value from $orderTotal is a String and convert it
						$orderTotalCost = (float) $orderTotal;
					
					$woocommerce->session->giftcard_post = $giftcard_found;
					$woocommerce->session->giftcard_id = $giftCardNumber;
					
					if($oldGiftcardValue == 0) {
						// Giftcard Entered does not have a balance
						$woocommerce->add_error( __( 'Gift Card does not have a balance!', RPWCGC_CORE_TEXT_DOMAIN ) );

					} elseif( $oldGiftcardValue >= $orderTotal ) {
						//  Giftcard Balance is more than the order total.
						//  Subtract the order from the card
						$woocommerce->session->giftcard_payment = $orderTotal;
						$woocommerce->session->giftcard_balance = $oldGiftcardValue - $orderTotal;
						$msg = __( 'Gift card applied successfully.', RPWCGC_CORE_TEXT_DOMAIN );
						$woocommerce->add_message(  __( 'Gift card applied successfully.', RPWCGC_CORE_TEXT_DOMAIN ) );

					} elseif($oldGiftcardValue < $orderTotal ) {
						//  Giftcard Balance is less than the order total.
						//  Subtract the giftcard from the order total
						$woocommerce->session->giftcard_payment = $oldGiftcardValue;
						$woocommerce->session->giftcard_balance = 0;
						$woocommerce->add_message(  __( 'Gift card applied successfully.', RPWCGC_CORE_TEXT_DOMAIN ) );

					}
				} else {
					// Giftcard Entered does not exist
					$woocommerce->add_error( __( 'Gift Card does not exist!', RPWCGC_CORE_TEXT_DOMAIN ) );
				}
			}
			//$woocommerce->cart->add_discount( sanitize_text_field( $_POST['coupon_code'] ) );
		}

		$woocommerce->show_messages();
		
		die();
	}
	
	
	add_action( 'woocommerce_calculate_totals', 'subtract_giftcard' );
	/**
	 * Function to decrease the giftcard amount from the cart total
	 * 
	 */
	function subtract_giftcard( $wc_cart ){
		global $woocommerce;
		
		$wc_cart->cart_contents_total = $wc_cart->cart_contents_total - $woocommerce->session->giftcard_payment;
	}
	

	add_action( 'woocommerce_admin_order_totals_after_shipping', 'rpgc_show_giftcard_in_order' );

	/**
	 * Function to add the giftcard data to the order summary page
	 *
	 */
	function rpgc_show_giftcard_in_order() {
		global $woocommerce, $post;
		
		$data = get_post_meta( $post->ID );
		?>
		
			<h4><?php _e( 'Giftcard Information', RPWCGC_CORE_TEXT_DOMAIN ); ?></h4>
			
			<ul class="totals">
				<li class="wide">
					<label><?php _e( 'Gift Card #:', RPWCGC_CORE_TEXT_DOMAIN ); ?></label>
					<input type="text" id="_giftcard_id" name="_giftcard_id" placeholder="<?php _e( 'The gift Card that was used on the order.', 'woocommerce' ); ?>" value="<?php
						if ( isset( $data['rpgc_id'][0] ) )
							echo esc_attr( $data['rpgc_id'][0] );
					?>" class="first" />
				</li>
				<li class="wide">
					<label><?php _e( 'Payment:', RPWCGC_CORE_TEXT_DOMAIN ); ?></label>
					<input type="text" id="_giftcard_payment" name="_giftcard_payment" placeholder="<?php _e( 'The gift Card that was used on the order.', RPWCGC_CORE_TEXT_DOMAIN ); ?>" value="<?php
						if ( isset( $data['rpgc_payment'][0] ) )
							echo esc_attr( $data['rpgc_payment'][0] );
					?>" class="first" />
				</li>

			</ul>

		<?php		
	}
	

	add_action('woocommerce_review_order_before_order_total', 'rpgc_order_giftcard');
	/**
	 * Function to add the giftcard data to the cart display
	 *
	 */
	function rpgc_order_giftcard() {
		global $woocommerce;
		
		if ( isset( $_GET['remove_giftcards'] ) ) {
			$type = $_GET['remove_giftcards'];

			if ( 1 == $type )
				unset( $woocommerce->session->giftcard_payment, $woocommerce->session->giftcard_id, $woocommerce->session->giftcard_post );
		}

		if ( isset($woocommerce->session->giftcard_payment )) {
			
			$currency_symbol = get_woocommerce_currency_symbol();
			$price = $woocommerce->session->giftcard_payment;
			
			?>
			
			<tr class="giftcard">
				<th><?php _e( 'Giftcard Payment', RPWCGC_CORE_TEXT_DOMAIN ); ?> </th>
				<td style="font-size:0.85em;"><?php echo sprintf( get_woocommerce_price_format(), $currency_symbol, $price ); ?> <a alt="<?php echo $woocommerce->session->giftcard_id; ?>" href="<?php echo add_query_arg( 'remove_giftcards', '1', $woocommerce->cart->get_checkout_url() ) ?>">[<?php _e( 'Remove Gift Card', RPWCGC_CORE_TEXT_DOMAIN ); ?>]</a></td>
			</tr>
			
			<?php
		}
	}
	

	add_action('woocommerce_order_details_after_order_table', 'rpgc_display_giftcard');
	add_action( 'woocommerce_email_after_order_table', 'rpgc_display_giftcard' );
	/**
	 * Displays the giftcard data on the order thank you page
	 *
	 */
	function rpgc_display_giftcard() {
		global $woocommerce;

		$currency_symbol = get_woocommerce_currency_symbol();
		?>
		<h4><?php _e( 'Remaining Gift Card Balance:', RPWCGC_CORE_TEXT_DOMAIN ); ?><?php echo ' ' . sprintf( get_woocommerce_price_format(), $currency_symbol, $woocommerce->session->giftcard_balance ); ?> </h4>
		
		<?php
	}
	

	add_action( 'woocommerce_checkout_order_processed', 'rpgc_update_card' );
	/**
	 * Updates the Gift Card and the order information when the order is processed
	 *
	 */
	function rpgc_update_card( $order_id ) {
		global $woocommerce;
	
		update_post_meta( $woocommerce->session->giftcard_post, 'rpgc_balance', $woocommerce->session->giftcard_balance ); // Update balance of Giftcard
		update_post_meta( $order_id, 'rpgc_id', $woocommerce->session->giftcard_id );
		update_post_meta( $order_id, 'rpgc_payment', $woocommerce->session->giftcard_payment );
	
		unset( $woocommerce->session->giftcard_payment, $woocommerce->session->giftcard_id, $woocommerce->session->giftcard_post );

	}
	
}
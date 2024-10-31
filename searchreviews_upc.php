<?php
/*
Plugin Name: SearchReviews.com Widget (UPC)
Plugin URI: http://searchreviews.com/publisher
Description: Get reviews via UPC from SearchReviews.com on your website.
Version: 1.0
Author: Sumeet Jain
Author URI: http://sumeetjain.com
License: GPL2
*/

// ### FRONT-END ###

add_filter('the_content', 'searchreviews_link_upc');

function searchreviews_link_upc($content){
	global $post;
	
	if(get_post_meta($post->ID, 'sr_upc', true) != ''){
		$upc = get_post_meta($post->ID, 'sr_upc', true);
		
		// Add widget script.
		$widget_script = "<script type='text/javascript' charset='utf-8' src='http://searchreviews.com/widget/snapshot_popup.jsp?id=&upc=" . $upc . "'></script>";
		
		// Add widget link placeholder.
		$link = '<a href="http://searchreviews.com/best/product.jsp?id=&upc=' . $upc . '" title="Reviews for ' . $upc . '" class="searchReviews_snapshot">' . $upc . ' reviews</a>';

		return $widget_script . $link . $content;
	}
	else {
		return $content;
	}
}


// ### BACK-END ###

// ### post settings ###
add_action('admin_init', 'sr_add_boxes');
add_action('save_post', 'sr_save_postdata');

function sr_add_boxes(){
	add_meta_box("searchreviews_box", __('SearchReviews.com Widget'), "sr_box_content", "post", "side");
	add_meta_box("searchreviews_box", __('SearchReviews.com Widget'), "sr_box_content", "page", "side");
}
function sr_box_content(){
	global $post;
	// Use nonce for verification
	wp_nonce_field(plugin_basename( __FILE__ ), 'searchreviews_upc');
	// The actual fields for data entry
	echo '<label for="sr_upc">';
	_e("UPC for reviews:");
	echo '</label> ';
	echo '<input type="text" id="sr_upc" name="sr_upc" value="' . get_post_meta($post->ID, 'sr_upc', true) . '" size="25" />';
}
function sr_save_postdata($post_id){
	// Check if this is an auto save routine. 
	// If it is our form has not been submitted, so we dont want to do anything
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
		return;
	}
	// Check if this came from our screen and with proper authorization,
	// because save_post can be triggered at other times
	if (!wp_verify_nonce($_POST['searchreviews_upc'], plugin_basename(__FILE__))){
		return;
	}
	// Check permissions
	if ('page' == $_POST['post_type']){
		if (!current_user_can('edit_page', $post_id)){
			return;
		}
	}
	else{
		if (!current_user_can('edit_post', $post_id)){
			return;
		}
	}
	// OK, we're authenticated: we need to find and save the data
	$mydata = $_POST['sr_upc'];

	// Do something with $mydata 
	add_post_meta($post_id, 'sr_upc', $mydata, true) or update_post_meta($post_id, 'sr_upc', $mydata);
	return $mydata;
}

?>

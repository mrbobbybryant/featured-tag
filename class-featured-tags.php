<?php
/*
Plugin Name: Featured Tags
Plugin URI: http://www.github.com/mrbobbybryant/featured-tags
Description: This plugin allows users to choose a featured tag and to display it on the front-end of their website.
Version: 0.0.2
Author: Bobby Bryant
Author URI: http://www.github.com/mrbobbybryant
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: featured-tags

Featured Tags is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Featured Tags is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Featured Tags. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FeaturedTagPlugin {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_post_meta' ) );
		add_filter( 'the_content', array( $this, 'add_featured_tag' ) );
	}

	public function register_metabox() {
		add_meta_box( 
			'featured-tag',
			'Featured Tag',
			array( $this, 'render_metabox_callback' ),
			'post',
			'side',
			'high'
		);
	}
	public function query_tags() {
	global $post;
	$args = array(
		'orderby'           => 'name', 
    	'order'             => 'ASC'
	);

	$terms = wp_get_object_terms( $post->ID, 'post_tag' );
	
	return $terms; 
}
	public function render_metabox_callback( $post ) {
		//Set Nonce for security reasons.
		wp_nonce_field( basename( __FILE__ ), 'featured_nonce' );
		
		//Query post for all tags
		$terms = self::query_tags();

		if( !empty( $terms ) ) { ?>
		
		<!-- Create select dropdown and populate it with tags -->
		<label for="featured-tag"><?php _e( 'Select a featured tag.', 'featured-tags' )?></label>
		<select name="featured-tag" id="featured-tag">
			<!-- create an open options -->
			<option><?php _e('Select Tag') ?></option>

		<?php 
		//Store featured tag in a variable
		$selected = get_post_meta( $post->ID, 'featured-tag', true );
		
		// Loop through all the tags for a given post and create an option.
		foreach ( $terms as $term ) { 
		
			// Populate Dropdown with terms.
			echo '<option value="'. $term->name .'"';
			//If term matches database meta value mark it as selected.
        	if ($selected == $term->name) {
            echo ' selected="selected"';
        	}
        	echo '>' . $term->name . '</option>';
		 } ?> 

		</select>

		<?php

		//If no tags exist display error message.
	} else {
		echo "This post does not contain any tags. To add a Feature Tag you must first save tags to this post.";
	}
		
	}

	/**
	 * Saves the custom meta input
	 */
	function save_post_meta( $post_id ) {
	    
	    // Checks save status
	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	    $is_valid_nonce = ( isset( $_POST[ 'featured_nonce' ] ) && wp_verify_nonce( $_POST[ 'featured_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
	    
	    // Exits script depending on save status
	    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
	        return;
	    }
	    
	    // Checks for input and sanitizes/saves if needed
	    if( isset( $_POST[ 'featured-tag' ] ) ) {
	        update_post_meta( $post_id, 'featured-tag', sanitize_text_field( $_POST[ 'featured-tag' ] ) );
	    }
	    
	}

	//Display Featured tag on the front-end
	function add_featured_tag($content) {
		
		//Only load on single posts in the main query
		if ( is_singular( 'post' ) && is_main_query() ) {
		
			// Store Featured Tag in Variable
			$featured_tag = get_post_meta( get_the_ID(), 'featured-tag', true );
			
			//Wrap featured tag in html markup
			$featured = '<div class="featured-tag"><h4>'.$featured_tag.'</h4></div>';
			
			//Append featured tag to end of content.
			$content = $content . $featured;

		}

		return $content;
	}


}

$featured_tags = new FeaturedTagPlugin;
// $featured_tags->register_metabox();

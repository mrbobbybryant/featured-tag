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
along with Featured Tags. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FeaturedTagPlugin
 */
class FeaturedTagPlugin {

	/**
	 *
	 */
	public function __construct() {

		/**
		 * The class responsible for setting up the plugins Customizer settings and sections.
		 */
		require_once 'class-customizer.php';
		$Featured_Plugin_Customizer = new FeaturedPluginCustomizer();


		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_post_meta' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'customize_preview_init', array( $this, 'featured_tag_customizer' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_links' ) );
		add_filter( 'the_content', array( $this, 'add_featured_tag' ) );

	}

	/**
	 * @param $links
	 * Method adds settings link to WordPress Customizer.
	 * Settings link sends users to special customizer.
	 *
	 * @return array
	 */
	public function add_settings_links( $links ) {

		//Query for the most recent post.
		$args        = array(
			'posts_per_page' => 1,
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'meta_key'       => '_featured-tag'
		);
		$posts_array = get_posts( $args );

		//As long as a featured tag is set, display a settings link
		if ( ! empty( $posts_array ) ) {

			//Loop through post and retrieve post link
			foreach ( $posts_array as $key => $post ) {
				$permalinks[] = get_permalink( $post );

			}

			//Store default customizer url and variable.
			$url = admin_url( 'customize.php' );
			//Modify url to load special Customizer experience.
			$post_link = add_query_arg(
				array(
					'url'                             => $permalinks[0],
					'return'                          => admin_url( 'plugins.php' ),
					'featured-customizer'             => true,
					urlencode( 'autofocus[section]' ) => 'featured_tag_settings'
				), $url
			);


			$mylinks = array(
				'<a href="' . esc_url( $post_link ) . '">Settings</a>',
			);

			return array_merge( $links, $mylinks );
		}

		//If no featured tags are set, don't show settings link.
		return $links;

	}

	/**
	 * Register Metabox
	 */
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

	/**
	 * Enqueue Featured Tag styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'featured_css',
			plugins_url( 'featured-tag.css', __FILE__ ),
			array(),
			'20150516'
		);
	}

	/**
	 * Enqueue javascript responsible for Customizer postMessage transport method.
	 */
	public function featured_tag_customizer() {
		wp_enqueue_script(
			'plugin_customizer',
			plugins_url( 'featured-tag.js', __FILE__ ),
			array( 'jquery', 'customize-preview' ),
			'20150516',
			true
		);
	}

	/**
	 * @return array|WP_Error
	 * Queries all tags for a given post.
	 */
	private function query_tags() {
		global $post;
		$args = array(
			'orderby' => 'name',
			'order'   => 'ASC'
		);

		$terms = wp_get_object_terms( $post->ID, 'post_tag' );

		return $terms;
	}

	/**
	 * @param $post
	 * Metabox Callback Function
	 * Outputs post tags in custom dropdown meta field.
	 * If no tags exist, outputs helpful text.
	 */
	public function render_metabox_callback( $post ) {
		//Set Nonce for security reasons.
		wp_nonce_field( basename( __FILE__ ), 'featured_nonce' );

		//Query post for all tags
		$terms = self::query_tags();

		if ( ! empty( $terms ) ) { ?>

			<!-- Create select dropdown and populate it with tags -->
			<label for="featured-tag"><?php _e( 'Select a featured tag.', 'featured-tags' ) ?></label>
			<select name="_featured-tag" id="_featured-tag">
				<!-- create an open options -->
				<option><?php _e( 'Select a Tag', 'featured-tags' ) ?></option>

				<?php
				//Store featured tag in a variable
				$selected = get_post_meta( $post->ID, '_featured-tag', true );

				// Loop through all the tags for a given post and create an option.
				foreach ( $terms as $term ) {

					// Populate Dropdown with terms.
					echo '<option value="' . esc_attr( $term->name ) . '"';
					//If term matches database meta value mark it as selected.
					if ( $selected == $term->name ) {
						echo ' selected="selected"';
					}
					echo '>' . esc_attr( $term->name ) . '</option>';
				} ?>

			</select>

			<?php

			//If no tags exist display error message.
		} else {
			_e( 'This post does not contain any tags. To add a Feature Tag you must first save tags to this post.', 'featured-tags' );
		}

	}

	/**
	 * @param $post_id
	 * Post Meta saving method.
	 */
	public function save_post_meta( $post_id ) {

		// Checks save status
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['featured_nonce'] ) && wp_verify_nonce( $_POST['featured_nonce'], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Checks for input and sanitizes/saves if needed
		if ( isset( $_POST['_featured-tag'] ) ) {
			update_post_meta( $post_id, '_featured-tag', sanitize_text_field( $_POST['_featured-tag'] ) );
		}

	}

	/**
	 * @param $content
	 *
	 * @return string
	 * Responsible for outputing featured tag meta on front-end
	 */
	public function add_featured_tag( $content ) {

		$terms = self::query_tags();
		//Only load on single posts in the main query
		if ( is_singular( 'post' ) && is_main_query() ) {

			// Store Featured Tag in Variable
			$featured_tag = get_post_meta( get_the_ID(), '_featured-tag', true );

			foreach ( $terms as $term ) {

				if ( $term->name == $featured_tag ) {

					$term_link = get_tag_link( $term->term_id );

					//Wrap featured tag in html markup
					$featured = '<div class="featured-tag"><a href="' . esc_url( $term_link ) . '">' . esc_html( $featured_tag ) . '</a></div>';

					//Setting used by Customizer to control where featured tag is displayed.
					if ( get_option( 'featured_tags_placement' ) == 'after_content' ) {
						//Append featured tag to end of content.
						$content .= $featured;
					} else {
						$content = $featured . $content;
					}

				}
			}
		}

		return $content;
	}
}

$featured_tags = new FeaturedTagPlugin;


<?php

// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_post_meta_by_key( '_featured_tag' );

delete_option( 'featured_tags_placement' );
delete_option( 'featured_tag_font' );
delete_option( 'tag_font_color' );
delete_option( 'featured_tag_border_radius' );
delete_option( 'tag_border_color' );
delete_option( 'tag_hover_color' );
delete_option( 'tag_color' );
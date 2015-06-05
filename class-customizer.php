<?php

/**
 * Created by PhpStorm.
 * User: Bobby
 * Date: 5/16/15
 * Time: 3:30 PM
 */
class FeaturedPluginCustomizer {

	public function __construct() {
		add_action( 'customize_register', array( $this, 'register_section' ) );
		add_action( 'wp_head', array( $this, 'add_customizer_styles' ) );
	}

	/**
	 * @param $wp_customize
	 */
	public function register_section( $wp_customize ) {

		$wp_customize->add_section( 'featured_tag_settings',
			array(
				'title'       => __( 'Featured Tag Settings', 'featured-tags' ),
				'priority'    => 30,
				'description' => __( 'This section allows you to customize how featured tags are displayed.', 'featured-tags' ),
			)
		);
		$this->register_location_setting( $wp_customize );
		$this->register_font_size_setting( $wp_customize );
		$this->register_font_color_setting( $wp_customize );
		$this->register_hover_color_setting( $wp_customize );
		$this->register_border_color_setting( $wp_customize );
		$this->register_border_radius_setting( $wp_customize );
		$this->register_tag_color_setting( $wp_customize );

	}

	/**
	 * @param $wp_customize
	 */
	private function register_location_setting( $wp_customize ) {
		$wp_customize->add_setting(
			'featured_tags_placement', array(
				'default'    => 'after_content',
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'refresh',
			)
		);
		$wp_customize->add_control(
			'featured_tags_placement', array(
				'type'        => 'radio',
				'label'       => __( 'Display Featured Tag', 'featured-tags' ),
				'description' => __( 'Select where you would like the featured tag displayed.', 'featured-tags' ),
				'section'     => 'featured_tag_settings',
				'setting'     => 'featured_tags_placement',
				'choices'     => array(
					'after_title'   => __( 'After Post Title', 'featured-tags' ),
					'after_content' => __( 'After Main Content', 'featured-tags' ),
				),
			)
		);


	}

	/**
	 * @param $wp_customize
	 */
	private function register_font_size_setting( $wp_customize ) {
		$wp_customize->add_setting(
			'featured_tag_font',
			array(
				'default'    => 14,
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'featured_tag_font',
			array(
				'type'        => 'range',
				'setting'     => 'featured_tag_font',
				'section'     => 'featured_tag_settings',
				'label'       => __( 'Font Size', 'featured-tags' ),
				'input_attrs' => array(
					'min'  => 1,
					'max'  => 100,
					'step' => 1,
				),
			)
		);
	}

	/**
	 * @param $wp_customize
	 */
	private function register_font_color_setting( $wp_customize ) {
		$wp_customize->add_setting(
			'tag_font_color', [
				'default'           => '#333',
				'type'              => 'option',
				'transport'         => 'postMessage',
				'capability'        => 'manage_options',
				'sanitize_callback' => 'sanitize_hex_color',
			]
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'tag_font_color',
				array(
					'label'   => __( 'Featured Tag Font Color', 'featured-tags' ),
					'section' => 'featured_tag_settings',
					'setting' => 'tag_font_color'
				)
			)
		);
	}

	/**
	 * @param $wp_customize
	 */
	private function register_border_radius_setting( $wp_customize ) {
		$wp_customize->add_setting(
			'featured_tag_border_radius',
			array(
				'default'    => 0,
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'featured_tag_border_radius',
			array(
				'type'        => 'range',
				'setting'     => 'featured_tag_border_radius',
				'section'     => 'featured_tag_settings',
				'label'       => __( 'Rounded Borders', 'featured-tags' ),
				'input_attrs' => array(
					'min'  => 1,
					'max'  => 20,
					'step' => .5,
				),
			)
		);
	}

	/**
	 * @param $wp_customize
	 */
	private function register_border_color_setting( $wp_customize ) {
		$wp_customize->add_setting(
			'tag_border_color', [
				'default'           => '#333',
				'type'              => 'option',
				'transport'         => 'postMessage',
				'capability'        => 'manage_options',
				'sanitize_callback' => 'sanitize_hex_color',
			]
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'tag_border_color',
				array(
					'label'   => __( 'Featured Tag Border Color', 'featured-tags' ),
					'section' => 'featured_tag_settings',
					'setting' => 'tag_border_color'
				)
			)
		);
	}

	/**
	 * @param $wp_customize
	 */
	private function register_hover_color_setting( $wp_customize ) {
		$wp_customize->add_setting(
			'tag_hover_color', [
				'default'           => '#333',
				'type'              => 'option',
				'capability'        => 'manage_options',
				'sanitize_callback' => 'sanitize_hex_color',
			]
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'tag_hover_color',
				array(
					'label'   => __( 'Featured Tag Hover Color', 'featured-tags' ),
					'section' => 'featured_tag_settings',
					'setting' => 'tag_hover_color'
				)
			)
		);
	}

	/**
	 * @param $wp_customize
	 */
	private function register_tag_color_setting( $wp_customize ) {
		$wp_customize->add_setting(
			'tag_color', [
				'default'           => '#fff',
				'type'              => 'option',
				'transport'         => 'postMessage',
				'capability'        => 'manage_options',
				'sanitize_callback' => 'sanitize_hex_color',
			]
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'tag_color',
				array(
					'label'   => __( 'Featured Tag Color', 'featured-tags' ),
					'section' => 'featured_tag_settings',
					'setting' => 'tag_color'
				)
			)
		);
	}

	/**
	 * Save Customizer style settings
	 */
	public function add_customizer_styles() {
		$font_size     = get_option( 'featured_tag_font' );
		$font_color    = get_option( 'tag_font_color' );
		$border_color  = get_option( 'tag_border_color' );
		$hover_color   = get_option( 'tag_hover_color' );
		$tag_color     = get_option( 'tag_color' );
		$border_radius = get_option( 'featured_tag_border_radius' );
		?>
		<style type="text/css">

			.featured-tag a {
				font-size: <?php echo $font_size . 'px'; ?>;
				color: <?php echo $font_color; ?>;
				border-color: <?php echo $border_color; ?>;
				background-color: <?php echo $tag_color; ?>;
				border-radius: <?php echo $border_radius . 'px'; ?>;
			}

			.featured-tag a:hover {
				background-color: <?php echo $hover_color; ?>;
			}

		</style>
	<?php
	}

}
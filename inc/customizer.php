<?php
/**
 * Safflower Theme Customizer
 *
 * @package Safflower
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 * Set a custom theme option that will allow us to select the featured series from
 * the WordPress admin panel directly.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function safflower_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

  /* Create a new section for featured series */
  $wp_customize->add_section( 'safflower_featured_series_panel', array(
    'title'    => __( 'Featured Series', 'safflower' ),
    'priority' => 1,
  ) );

  /* Add the setting & control */
  $wp_customize->add_setting( 'safflower_featured_series', array(
    'sanitize_callback' => 'safflower_sanitize_featured_series',
  ) );

  $wp_customize->add_control( 'safflower_featured_series', array(
    'label'             => __( 'Select the series to feature on the homepage.', 'safflower' ),
    'section'           => 'safflower_featured_series_panel',
    'priority'          => 20,
    'type'              => 'select',
    'choices'           => safflower_get_series_list(),
  ) );
}
add_action( 'customize_register', 'safflower_customize_register' );

/*
 * Make sure the entered series ID is one of
 * our permitted series.
 */
function safflower_sanitize_featured_series( $value ) {
  $allowed_series = safflower_get_series_list();
  unset( $allowed_series[0] );
  if ( ! array_key_exists( $value, $allowed_series ) ):
    $value = '';
  endif;
  return $value;
}

/*
 * Generate a list of series that can be featured.
 * We currently only feature direct children (not grandchildren)
 * of the Design Resources and Shopping Guides categories,
 * so that's all we'll show.
 */
function safflower_get_series_list() {

  $shopping_guides = get_categories( array(
    'parent'      => '5',
    'exclude'     => '',
  ) );

  $design_resources = get_categories( array(
    'parent'      => '6',
    'exclude'     => '',
  ) );

  foreach ( $shopping_guides as $category ):
    $category_list[$category->term_id] = $category->name;
  endforeach;

  $category_list[0] = ''; // Just for some visual separation between the lists

  foreach ( $design_resources as $category ):
    $category_list[$category->term_id] = $category->name;
  endforeach;

return $category_list;
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function safflower_customize_preview_js() {
	wp_enqueue_script( 'safflower_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'safflower_customize_preview_js' );

<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Safflower
 */

function safflower_jetpack_setup() {
  /**
   * Add theme support for Infinite Scroll.
   * See: http://jetpack.me/support/infinite-scroll/
   */
  add_theme_support( 'infinite-scroll', array(
    'container'      => 'main',
    'footer'         => false,
    'render'         => 'safflower_render_post_content',
    'wrapper'        => 'infinite-wrapper',
  ) );

  /**
   * Add theme support for Jetpack responsive videos
   * See: http://jetpack.me/support/responsive-videos/
   */
  add_theme_support( 'jetpack-responsive-videos' );

  /**
   * Add theme support for Jetpack featured content
   * See: http://jetpack.me/support/featured-content/
   */
  add_theme_support( 'featured-content', array(
    'filter'    => 'safflower_get_featured_posts',
    'max_posts' => 2,
  ) );

  /**
   * Add theme support for Jetpack site logo
   * See: http://jetpack.me/support/site-logo/
   */
  add_image_size( 'safflower-logo', 700 ); // Restrict logo to 700 pixels in width (double-sized for Retina)
  add_theme_support( 'site-logo', array( 'size' => 'safflower-logo' ) );

}
add_action( 'after_setup_theme', 'safflower_jetpack_setup' );

/**
* Render a different post template depending on which page we're on
*/
function safflower_render_post_content() {
  while( have_posts() ) {
    the_post();
    if ( is_category( 'shopping-guides' ) ):
      get_template_part( 'content', 'blank' );
    elseif ( is_tag() OR is_search() ):
      get_template_part( 'content', 'short' );
    else:
      get_template_part( 'content', get_post_format() );
    endif;
  }
}

/**
* Remove ability to comment on Jetpack carousel images
* See: http://jetpack.me/support/carousel/
*/
function filter_media_comment_status( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'filter_media_comment_status', 10 , 2 );


/**
* We've disabled Sharedaddy's scripts on the front-end, in order to customize its display.
* See: http://themeshaper.com/2014/05/30/customizing-jetpacks-sharing-module/
*/
function safflower_remove_sharedaddy_script() {
  remove_action( 'wp_head', 'sharing_add_header', 1 );
}
add_action( 'template_redirect', 'safflower_remove_sharedaddy_script' );


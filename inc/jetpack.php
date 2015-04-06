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
    'footer'         => 'colophon',
    'footer_widgets' => 'footer-sidebar',
    'wrapper'        => false,
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
* Remove ability to comment on Jetpack carousel images
*/
function filter_media_comment_status( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'filter_media_comment_status', 10 , 2 );

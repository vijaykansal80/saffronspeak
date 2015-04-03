<?php
/**
 * Series styling functions
 *
 * Different post series (categories) have unique styles applied.
 * We need to subvert how WordPress typically handles things for this.
 * Nothing too wild, but it calls for some special functions. These are them.
 *
 * @package Safflower
 */

// Define a category as featured by setting the category slug below.
$featured_series = 110;
// Now fetch the full category object for use.
$featured = get_term_by( 'id', $featured_series, 'category' );


/**
 * If the category name has a "series" suffix, we're going to remove it.
 * This ensures that we look in the correct folder locations for our files,
 * as well as ensuring that we use the correct class names. Currently, no
 * categories use the "series" suffix anymore, but this is retained
 * for backwards (as well as future) compatibility.
 */
function smarter_slug( $category ) {
  $slug = strtolower( $category->name );
  $slug = str_replace( 'series', '', $slug );
  $slug = str_replace( ' ', '-', trim( $slug ) );
  return $slug;
}


/**
* Get a link to the currently featured series.
* This function is primarily used on the homepage, in order to
* dynamically generate a link to the featured series.
*/
function get_featured_series_link( $slug ) {
  $category = get_category_by_slug( $slug );
  $series_link = '<a href="'. get_parent_post_link( $category->term_id ) . '">' . $category->name . '</a>';
  return $series_link;
}

/**
* Get a link to the "parent post" (sticky post) of a given "series" (category).
* Instead of using the category archive page, we use a custom post
* to list all the posts within that series. This allows us manual
* control of their display, order, and formatting from within WordPress.
* The parent post in a given category is marked as a sticky post in order
* differentiate it from all the other posts.
*
* This function returns the URL for a given category's parent post.
*/
function get_parent_post_link( $category_id ) {
  // Find all sticky posts in the category (there should only be one)
  $args = array(
    'cat'                 => $category_id,
    'post__in'            => get_option( 'sticky_posts' ),
    'ignore_sticky_posts' => 1,
    'orderby'             => 'date',
    'order'               => 'ASC',
  );
  $cat_posts = $the_query = new WP_Query( $args );

  /* If there's at least one sticky post, get its permalink.
   * Note that this technically loops through *all* sticky posts returned above
   * and returns a permalink, but we ordered the array above in ascending order
   * of publication date. If there are two sticky posts within the same category,
   * we'll end up returning a link to the most recently-published sticky post,
   * which is most likely to be the one we're looking for.
   */
  if ( $the_query->have_posts() ):
    while ( $the_query->have_posts() ) {
      $the_query->the_post();
      $series_link = get_permalink();
    }

  // If there are no sticky posts in the category, just show a link to the category page itself
  else:
    $series_link = get_category_link( $category->term_id );
  endif;
  wp_reset_postdata();
  return $series_link;
}

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

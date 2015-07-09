<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Safflower
 */

/**
 * Remove the 'Category:' prefix on category archive pages
 */
add_filter( 'get_the_archive_title', function ( $title ) {
  if( is_category() ) {
    $title = single_cat_title( '', false );
  }
	return $title;
});


if ( ! function_exists( 'safflower_breadcrumbs' ) ) :
/**
 * Display breadcrumb navigation, where applicable
 *
 */
function safflower_breadcrumbs() {

  // Only show breadcrumbs on category and post pages
  if ( ! is_home() &&  ! is_front_page() && ! is_page() ):
    echo '<div class="breadcrumbs">';
    echo '<a href="';
    echo home_url();
    echo '">';
    echo 'Blog';
    echo "</a>";

    // Single post
    if ( is_single() ):
        echo ' &raquo; ';
      	// Parent categories
        the_category( ' &raquo; ', 'multiple' );
        echo ' &raquo; ';
        // Post title
        the_title();

    // Category archive
    elseif (is_category()):
        echo ' &raquo; ';

        // Get a list of the current category's parent categories
        $category_list = get_category_parents( get_query_var( 'cat' ), true, ' &raquo; ' );

        // Remove current category from list (in order to display without a link or trailing arrow)
        $categories = explode( ' &raquo; ', $category_list );
        array_pop( $categories );
        array_pop( $categories );
        foreach ( $categories as $category ):
          echo $category .' &raquo; ';
        endforeach;

        // Display current category name, without a link or trailing arrow
        echo get_the_category_by_id( get_query_var( 'cat' ) );

    // Static page
    elseif (is_page()):
        echo " &raquo; ";
        echo the_title();

    // Search results
    elseif ( is_search() ):
      echo ' &raquo; Search results for: ';
      echo '&ldquo;<em>';
      echo the_search_query();
      echo '</em>&rdquo;';

    // Tag archives
    elseif ( is_tag() ):
      echo ' &raquo; ';
      echo 'Tag archive: ';
      echo safflower_full_tag_string();
    endif;

  	echo '</div>';
  endif; // ! is_home() &&  ! is_front_page() && ! is_page()
}
endif;

if ( ! function_exists( 'safflower_full_tag_string' ) ) :
/**
 * If you're using a more complex tag string, like red+bedding
 * or red-bedding, WordPress doesn't parse these properly for display
 * in a list of breadcrumbs. This parses the tags directly from the request
 * URI and outputs them in a logical, user-friendly fashion.
 *
 */
function safflower_full_tag_string() {
  global $tag_query;
  $tag_query = $_SERVER['REQUEST_URI'];
  $tag_query = str_replace( '/blog/', '', $tag_query );
  $tag_query = str_replace( 'tag/', '', $tag_query );
  $tag_query = str_replace( ',', ', ', $tag_query );
  $tag_query = str_replace( '/', '', $tag_query );
  $tag_query = str_replace( '+', ', ', $tag_query );
  $tag_query = str_replace( '-', ' ', $tag_query );
  $primary_tag = strtolower( single_tag_title( '', false ) );
  $secondary_tags = str_replace( $primary_tag, '', $tag_query );
  $secondary_tags = ltrim( $secondary_tags, ', ' );
  if ( '' != $secondary_tags ) {
    $tag_string = '<strong>'. $primary_tag .'</strong> and '. $secondary_tags;
  } else {
    $tag_string = $primary_tag;
  }
  return $tag_string;
}

function all_tags() {
  global $tag_query;
  $all_tags = explode(', ', $tag_query);
  return $all_tags;
}
endif;

if ( ! function_exists( 'safflower_list_tags' ) ) :
/**
 * Show a list of tags to which the current post belongs.
 * Should be used in the Loop.
 */
function safflower_list_tags() {
  $tag_string = get_the_tag_list( '', ',', '' );
  $tags = explode( ',', $tag_string );
	?>
	<section class="tag-list">
		<span>Tags</span>
		<?php
	  foreach( $tags as $key => $tag ):
		  if ( 0 != $key ):
		  	echo ' &middot; ';
		  endif;
		  // Strip HTML and convert to lowercase, for consistency
	   	$tagless_tag = strtolower( strip_tags( $tag ) );
	    if ( in_array( $tagless_tag, all_tags() ) ):
	      echo '<strong>'. $tag . '</strong>';
	    else:
	      echo $tag;
	    endif;
	  endforeach;
	?>
	</section>
	<?php }
endif;


if ( ! function_exists( 'the_posts_navigation' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function the_posts_navigation() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation posts-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'safflower' ); ?></h2>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( 'Previous Entires', 'safflower' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts', 'safflower' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'the_post_navigation' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function the_post_navigation() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Post navigation', 'safflower' ); ?></h2>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', '%title' );
				next_post_link( '<div class="nav-next">%link</div>', '%title' );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'safflower_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function safflower_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( 'Posted on %s', 'post date', 'safflower' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		_x( 'by %s', 'post author', 'safflower' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>';

	edit_post_link( __( ' (Edit) ', 'safflower' ), '<span class="edit-link">', '</span>' );

}
endif;

if ( ! function_exists( 'safflower_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function safflower_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {

		/* We don't currently show categories at the bottom of posts
		$categories_list = get_the_category_list( __( ', ', 'safflower' ) );
		if ( $categories_list && safflower_categorized_blog() ) {
			printf( '<span class="cat-links">' . __( 'Posted in %1$s', 'safflower' ) . '</span>', $categories_list );
		}
		*/

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( '&middot;', 'safflower' ) );
		//$tags_list = get_the_tag_list('', ' &middot; ', '');
		if ( $tags_list ) {
			printf( '<section class="tags-links">' . __( '<span>Find related posts by tags:</span>  %1$s', 'safflower' ) . '</section>', $tags_list );
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', 'safflower' ), __( '1 Comment', 'safflower' ), __( '% Comments', 'safflower' ) );
		echo '</span>';
	}
}
endif;

if ( ! function_exists( 'the_archive_title' ) ) :
/**
 * Shim for `the_archive_title()`.
 *
 * Display the archive title based on the queried object.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the title. Default empty.
 * @param string $after  Optional. Content to append to the title. Default empty.
 */
function the_archive_title( $before = '', $after = '' ) {
	if ( is_category() ) {
		$title = sprintf( __( 'Category: %s', 'safflower' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( __( 'Tag: %s', 'safflower' ), single_tag_title( '', false ) );
	} elseif ( is_author() ) {
		$title = sprintf( __( 'Author: %s', 'safflower' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( __( 'Year: %s', 'safflower' ), get_the_date( _x( 'Y', 'yearly archives date format', 'safflower' ) ) );
	} elseif ( is_month() ) {
		$title = sprintf( __( 'Month: %s', 'safflower' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'safflower' ) ) );
	} elseif ( is_day() ) {
		$title = sprintf( __( 'Day: %s', 'safflower' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'safflower' ) ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title', 'safflower' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title', 'safflower' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( __( 'Archives: %s', 'safflower' ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( __( '%1$s: %2$s', 'safflower' ), $tax->labels->singular_name, single_term_title( '', false ) );
	} else {
		$title = __( 'Archives', 'safflower' );
	}

	/**
	 * Filter the archive title.
	 *
	 * @param string $title Archive title to be displayed.
	 */
	$title = apply_filters( 'get_the_archive_title', $title );

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;
	}
}
endif;

if ( ! function_exists( 'the_archive_description' ) ) :
/**
 * Shim for `the_archive_description()`.
 *
 * Display category, tag, or term description.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the description. Default empty.
 * @param string $after  Optional. Content to append to the description. Default empty.
 */
function the_archive_description( $before = '', $after = '' ) {
	$description = apply_filters( 'get_the_archive_description', term_description() );

	if ( ! empty( $description ) ) {
		/**
		 * Filter the archive description.
		 *
		 * @see term_description()
		 *
		 * @param string $description Archive description to be displayed.
		 */
		echo $before . $description . $after;
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function safflower_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'safflower_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'safflower_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so safflower_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so safflower_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in safflower_categorized_blog.
 */
function safflower_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'safflower_categories' );
}
add_action( 'edit_category', 'safflower_category_transient_flusher' );
add_action( 'save_post',     'safflower_category_transient_flusher' );

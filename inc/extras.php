<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Safflower
 */

/**
 * Filter the output of WordPress Popular Posts plugin in order to use custom HTML.
 * https://github.com/cabrerahector/wordpress-popular-posts/wiki/3.-Filters
 */
function custom_popular_posts_list( $mostpopular, $instance ) {
?>
  <section class="post-list favourite-posts">
    <h3>Most-loved Posts</h3>
    <?php
    global $post;
    foreach( $mostpopular as $popular ):
      $post = get_post( $popular->id );
      setup_postdata( $post );
      format_post_preview( $post );
      wp_reset_postdata();
    endforeach;
    ?>
  </section>
<?php
}
add_filter( 'wpp_custom_html', 'custom_popular_posts_list', 10, 2 );

/**
 * Show a formatted list of posts.
 * This is currently used only to list popular and new posts on the homepage,
 * but in the future could be used in a sidebar or similar.
 */
function format_post_preview($post) {
?>
  <article class="post-preview">
    <a href="<?php the_permalink(); ?>">
    <?php if ( has_post_thumbnail() ) {
    	the_post_thumbnail();
    } ?>
    </a>
    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
    <p><?php the_advanced_excerpt( 'length=10&use_words=1&no_custom=1&ellipsis=&finish_sentence=1&allowed_tags=a,em,strong' ); ?></p>
  </article>
<?php
}


/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function safflower_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'safflower_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function safflower_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary:
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( __( 'Page %s', 'safflower' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'safflower_wp_title', 10, 2 );

	/**
	 * Title shim for sites older than WordPress 4.1.
	 *
	 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
	 * @todo Remove this function when WordPress 4.3 is released.
	 */
	function safflower_render_title() {
		?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}
	add_action( 'wp_head', 'safflower_render_title' );
endif;

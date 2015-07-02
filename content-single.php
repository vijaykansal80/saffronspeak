<?php
/**
 * @package Safflower
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php
/*
 * For QA purposes, we want a way to easily show the featured
 * image attached to a post. Since these aren't displayed in
 * the actual post themselves, we add a query var to the URL to
 * manually check the featured image, like so:
 * http://saffronmarigold.com/blog/monday-mix-summer-party-inspiration/?featured=yes
 */
if ( "yes" == get_query_var( 'featured' ) ):
	if ( has_post_thumbnail() ) {
    echo '<div class="center">';
    echo the_post_thumbnail( 'medium' );
    echo '</div>';
  }
endif;
?>

	<?php
	// If this is the parent (sticky) post, we won't show the post header
	if ( ! is_sticky() ):
	?>
		<header class="entry-header">
			<div class="entry-meta">
				<?php safflower_posted_on(); ?>
			</div><!-- .entry-meta -->
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'safflower' ),
				'after'  => '</div>',
			) );
		?>
		<?php safflower_series_nav(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php safflower_entry_footer(); ?>
	</footer><!-- .entry-footer -->

	<?php
	// Custom YARRP query (https://wordpress.org/plugins/yet-another-related-posts-plugin/faq/)
	yarpp_related( array(
    'template' => 'yarpp-template-custom.php',
    'limit'    => 3,
  ) );
	?>

</article><!-- #post-## -->

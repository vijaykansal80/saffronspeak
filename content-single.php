<?php
/**
 * @package Safflower
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

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
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php safflower_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

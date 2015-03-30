<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package safflower
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<?php
			if ( is_category() ):
				$category_id = get_query_var('cat');
        $category = get_category($category_id);

			  if ( $category->category_parent === 0 ):
			  	$extra_header_classes = "category-intro " . $category->slug;
			  	$fleurons = '<i class="icon-header-fleuron-left"></i><?php echo $category->name; ?><i class="icon-header-fleuron-right"></i>';
			  	$description = str_replace('#', get_category_link($category->term_id), $category->description);
			  endif;

      endif;
      ?>

      <header class="page-header <?php echo $extra_header_classes; ?>">
      	<img src="<?php bloginfo(stylesheet_directory); ?>/images/categories/<?php echo $category->slug; ?>.jpg">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

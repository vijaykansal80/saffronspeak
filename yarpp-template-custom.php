<?php
 /*
 * YARPP Template: Custom
 * This customizes the output of YARRP.
 * See: https://wordpress.org/plugins/yet-another-related-posts-plugin/faq/
 * @package Safflower
 */
?>

<section class="related-posts">
    <h3>Find related posts by content:</h3>
    <?php if ( have_posts() ): ?>
      <?php while ( have_posts() ) : the_post(); ?>
      <div class="related-post">
            <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="saffron-related-thumb">
                <?php the_post_thumbnail( 'yarpp-thumbnail' ); ?></a>
            <h4><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h4>
            <p><?php echo get_the_excerpt(); ?>  <span class="read-more-inline">(<a href="<?php the_permalink(); ?>">Read more &raquo;</a>)</span></p>
        </div>
      <?php endwhile; ?>
</section>

<?php else: ?>
    <p>No related posts.</p>
<?php endif; ?>

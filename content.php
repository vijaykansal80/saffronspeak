<?php
/**
 * @package Safflower
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-excerpt long-excerpt' ); ?>>

<?php // Show featured images
if (has_post_thumbnail()): ?>
  <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
    <?php the_post_thumbnail(''); ?>
  </a>
<?php endif; ?>

  <header class="entry-header">
  	<div class="entry-meta">
      <?php echo safflower_posted_on(); ?>
    </div>
    <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
  </header>

  <div class="entry-content">
    <p><?php the_advanced_excerpt('length=40&use_words=1&no_custom=1&ellipsis=&finish_sentence=1'); ?></p>
  </div>

  <p class="read-more"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">Read more</a></p>
</article><!-- #post-## -->

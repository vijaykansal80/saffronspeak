<?php
/**
 * @package Safflower
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-excerpt short-excerpt' ); ?>>

<?php // Show featured images
if ( has_post_thumbnail() ): ?>
  <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="post-thumbnail">
    <?php the_post_thumbnail(); ?>
  </a>
<?php endif; ?>

  <div class="entry-content">
    <?php the_title( sprintf( '<h4 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' ); ?>
    <?php
    // For Relevanssi's search term highlighting to work, we need to use the_excerpt
    if ( is_search() ):
      the_excerpt();
    // Otherwise, we'll use a custom excerpt that allows us greater control
    else:
      the_advanced_excerpt( 'length=40&use_words=1&no_custom=1&ellipsis=&finish_sentence=1' );
    endif;
    ?>

  <?php safflower_list_tags() ?>

  </div>

</article><!-- #post-## -->

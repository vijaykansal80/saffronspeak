<?php 
/*
YARPP Template: Custom
Author: sarah semark
Description: A simple example YARPP template.
*/
?>

<section class="related-posts">
    <h3>Find related posts by content:</h3>
    <?php if (have_posts()):?>
    	<?php while (have_posts()) : the_post(); ?>
    	<div class="related-post">
            <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                <?php the_post_thumbnail('yarpp-thumbnail'); ?></a>
            <h4><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h4>
            <p><?php echo get_the_excerpt(); ?>  (<a class="inline-readmore" href="<?php the_permalink(); ?>">Read more &raquo;</a>)</p>
        </div>
    	<?php endwhile; ?>
</section>

<?php else: ?>
    <p>No related posts.</p>
<?php endif; ?>

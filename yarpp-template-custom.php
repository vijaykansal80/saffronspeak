<?php 
/*
YARPP Template: Custom
Author: sarah semark
Description: A simple example YARPP template.
*/
?>

<section class="related-posts">
    <h3>Related Posts</h3>
    <?php if (have_posts()):?>
    	<?php while (have_posts()) : the_post(); ?>
    	<div class="related-post">
            <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                <?php the_post_thumbnail('yarpp-thumbnail'); ?></a>
            <h4><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h4>
            <?php the_excerpt(); ?> 
            <p class="read-more"><a href="<?php the_permalink(); ?>">Read more</a></p>
        </div>
    	<?php endwhile; ?>
</section>

<?php else: ?>
    <p>No related posts.</p>
<?php endif; ?>

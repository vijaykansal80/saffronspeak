<?php
/**
 * Homepage template
 *
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Safflower
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<header class="entry-header">
      	<h1 class="site-title"><?php echo bloginfo('title'); ?></h1>
      	<p class="tagline"><?php echo bloginfo('description'); ?></p>
      </header>

      <?php
      /*
       * Show introductory panel for each of our top-level categories
       *
       */
    	$categories = get_categories( array('parent' => 0, 'exclude' => 1) );
    	foreach( $categories as $category ):
        ?>
        <div class="category <?php echo $category->slug; ?>">
            <a href="<?php echo get_category_link( $category->term_id ); ?>"><img src="<?php bloginfo(stylesheet_directory); ?>/images/categories/<?php echo $category->slug; ?>.jpg"></a>
            <h3><a href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name; ?></a></h3>
            <p><?php echo str_replace('#', get_category_link($category->term_id), $category->description); ?></p>
        </div>
    <?php
    endforeach;

        //echo featured_series(smarter_slug($featured), $featured->slug); ?>

        <h2>Read more posts</h2>
        <?php //echo list_posts('latest'); ?>

        <?php if (function_exists('wpp_get_mostpopular')):
            //wpp_get_mostpopular("range=monthly&limit=10&post_type=post");
        endif; ?>



<?php


// Get custom link for series index (parent or category index)
function get_parent_post_link($category_id) {

    // get link to parent post (should be sticky)
    $args = array(
        'cat' => $category_id,
        'post__in' => get_option( 'sticky_posts' ),
        'ignore_sticky_posts' => 1,
    );
    $cat_posts = $the_query = new WP_Query($args);

    if ( $the_query->have_posts() ) {
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $series_link = get_permalink();
        }

    // otherwise, just show a link to the category page
    } else {
        $series_link = get_category_link($category->term_id);
    }
    wp_reset_postdata();
    return $series_link;
}


// Show custom HTML for featured series
function featured_series($slug, $seo_slug=false) {
    $dir = plugin_dir_path( __FILE__ );
    $template = parse_url(get_bloginfo('template_directory'));
    $path = $template['path']."/custom/series/".$slug;
    if ($seo_slug) {
        $category = get_category_by_slug($seo_slug);
    } else {
        $category = get_category_by_slug($slug);
    }

    $series_link = get_parent_post_link($category->term_id);

    echo '<h2>Featured series: <a href="'.$series_link.'">'. $category->name . '</a></h2>';
    echo '<div class="featured-series '.$slug.'">';
    include($dir."/series/".$slug."/".$slug.".php");
    echo '</div>';
}

// List posts widget
function list_posts($type, $number=10) {
    ?>
    <div class="post-list <?php echo $type; ?>">
        <h3><?php echo $type; ?> Posts</h3>
        <?php
        global $post;
        if ($type === "latest") {
            $args = array('posts_per_page' => $number, 'orderby' => 'post_date', 'order' => 'DESC', 'post_type' => 'post');
        } elseif ($type === "favorite") {
            $args = array('posts_per_page' => $number, 'orderby' => 'comment_count', 'order' => 'DESC', 'post_type' => 'post');
        }
        $recent_posts = get_posts($args);
        foreach($recent_posts as $post):
            setup_postdata($post);
            preview_post($post);
            wp_reset_postdata();
        endforeach; ?>
    </div>
<?php
}


// Show custom popular posts widget
function custom_popular_posts_list($mostpopular, $instance) {
    ?>
    <div class="post-list favourite">
        <h3>Most-loved Posts</h3>
        <?php
        global $post;
        foreach($mostpopular as $popular):
            $post = get_post($popular->id);
            setup_postdata($post);
            preview_post($post);
            wp_reset_postdata();
        endforeach; ?>
    </div>
<?php
}
add_filter( 'wpp_custom_html', 'custom_popular_posts_list', 10, 2 );


// Format post previews in lists
function preview_post($post) {
    ?>
    <div class="post-preview">
        <a href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail(''); } ?></a>
        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
        <p><?php the_advanced_excerpt('length=10&use_words=1&no_custom=1&ellipsis=&finish_sentence=1'); ?></p>
    </div>
<?php
}
?>



		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

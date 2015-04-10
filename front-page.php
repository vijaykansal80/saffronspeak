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

			<header class="section-header">
      	<h2 class="site-title"><?php echo bloginfo( 'title' ); ?></h2>
      	<p class="tagline"><?php echo bloginfo( 'description' ); ?></p>
      </header>

      <?php
      /* Show introductory panel for each of our top-level categories (excluding "Uncategorized")
       * This shows a custom image for each category, along with its title & description.
       * If the description includes a "#" element, this will be dynamically replaced with the category's URL.
       */
    	$categories = get_categories( array(
    		'parent' => 0,
    		'exclude' => 1,
    	) );
    	foreach( $categories as $category ): ?>
        <div class="category <?php echo $category->slug; ?>">
          <a href="<?php echo get_category_link( $category->term_id ); ?>">
          	<img src="<?php echo get_template_directory_uri(); ?>/images/categories/<?php echo $category->slug; ?>.jpg">
          </a>
          <h3><a href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name; ?></a></h3>
          <p><?php echo str_replace( '#', get_category_link( $category->term_id ), $category->description ); ?></p>
        </div>
    <?php endforeach; ?>

    <?php if ( $safflower_featured_series_id ): ?>
      <header class="section-header">
      	<h2>Featured series<span class="mobile-hide">: <?php echo safflower_featured_series_link( $safflower_featured_series->slug ); ?></span></h2>
      </header>

      <section class="featured-series <?php echo safflower_smart_slug( $safflower_featured_series ); ?>">
  	    <?php
  	    /*
  	     * For each series, we want to show a customized panel. Since this panel can be completely
  	     * different in design and content from series to series, we can't automate this process.
  	     * Instead, we store a php file within the /series/{series-slug} directory that contains
  	     * the panel content. So, we're dynamically generating the include path based on the series
  	     * slug and including the file in our output HTML.
  	     */
  					$slug = safflower_smart_slug( $safflower_featured_series );
  			    $dir = plugin_dir_path( __FILE__ );
  			    $template = parse_url( get_template_directory_uri() );
  			    $path = $template['path']. '/series/' .$slug;
  			    include( $dir. '/series/' .$slug. '/' .$slug. '.php' );
  			?>
  		</section>
    <?php endif; ?>

    <header class="section-header">
      <h2>Read more posts</h2>
    </header>

    <section class="post-list latest-posts">
      <h3>Latest Posts</h3>
      <?php
      	// Show a list of the most recently-published posts, in descending order
        $recent_posts = get_posts( array(
        	'posts_per_page' => 10,
        	'orderby' => 'post_date',
        	'order' => 'DESC',
        	'post_type' => 'post',
        ) );
        foreach( $recent_posts as $post ):
          setup_postdata( $post );
          format_post_preview( $post );
          wp_reset_postdata();
        endforeach;
        ?>
    </section>

	  <?php if ( function_exists( 'wpp_get_mostpopular' ) ):
	    wpp_get_mostpopular( 'range=monthly&limit=10&post_type=post' );
	  endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

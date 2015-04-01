<?php
/**
 * The template for displaying category pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package safflower
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

    <?php if ( have_posts() ) :

      $category_id = get_query_var('cat');
      $category = get_category($category_id);
      $subcategories = get_categories('hide_empty=0&parent='.$category_id);

      // If this is a top-level category, we display a fancy introductory panel
      if ( $category->category_parent === 0 ):
        $extra_header_classes = "category-intro " . $category->slug;
        $fleurons = '<i class="icon-header-fleuron-left"></i><?php echo $category->name; ?><i class="icon-header-fleuron-right"></i>';
        $description = str_replace('#', get_category_link($category->term_id), $category->description);
      endif;
      ?>

      <header class="page-header <?php echo $extra_header_classes; ?>">
        <img src="<?php bloginfo(stylesheet_directory); ?>/images/categories/<?php echo $category->slug; ?>.jpg">
        <?php
          the_archive_title( '<h1 class="page-title">', '</h1>' );
          the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
      </header><!-- .page-header -->

      <?php
        // If we're in the Design Resources category, display an expandable panel with a list of sub-categories
        if( "Design Resources" === $category->name ):
            $series_subcategories = array_slice($subcategories, 0, 4);
            $print_subcategories = array_slice($subcategories, 4);
            ?>
            <a href="#" class="subcategory-expander-link"><i class="icon-caret-down"></i>By series or print</a>
            <section class="subcategory-expander">
                <div>
                    <section>
                        <h2><i class="icon-bullet-fleuron"></i>By series</h2>
                        <ul>
                    <?php
                    foreach ($series_subcategories as $subcategory):
                        echo '<li>'.$count.'<a href="'.get_category_link($subcategory->term_id).'">'.$subcategory->name.'</a></li>';
                    endforeach; ?>
                        </ul>
                    </section>

                    <section>
                        <h2><i class="icon-bullet-fleuron"></i>By print</h2>
                    <?php $count = 0;
                    foreach ($print_subcategories as $subcategory):
                        if ($count % 3 === 0) { echo '<ul>'; }
                        echo '<li><a href="'.get_category_link($subcategory->term_id).'">'.$subcategory->name.'</a></li>';
                        $count++;
                        if ($count % 3 === 0 or $count - 1 === count($print_subcategories)) { echo '</ul>'; }
                    endforeach; ?>
                    </section>
                </div>
            </section>
        <?php endif; // Design Resources subcategory panel

        // If we're in the Shopping Guides category, display a styled block for each sub-category
        if( "Shopping Guides" === $category->name ):
          foreach ($subcategories as $subcategory):
          ?>
            <div class="subcategory <?php echo smarter_slug($subcategory); ?>">
                <h2><?php echo $subcategory->name; ?></h2>
                <?php // Currently featured category should show an "Updated for..." badge
                    global $featured;
                    if( $subcategory->term_id == $featured->term_id ):
                ?>
                    <img class="badge" src="<?php bloginfo(stylesheet_directory); ?>/custom/images/updated-for-2015.png" alt="Updated for 2015"/>
                <?php endif; ?>
                <p class="read-more"><a href="<?php echo get_category_link($subcategory->term_id); ?>">Read more</a></p>
                <a class="div-link" href="<?php echo get_category_link($subcategory->term_id); ?>"></a>
            </div>
        <?php endforeach;

      // Otherwise, display a list of posts
      else:
        while ( have_posts() ) : the_post(); ?>

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

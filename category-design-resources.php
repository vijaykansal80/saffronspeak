<?php
/**
 * The template for displaying the Design Resources category.
 *
 * For this category, we first display a collapsible panel of
 * all its sub-categories. Then we list the posts as usual.
 *
 * @package Safflower
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
    <?php get_template_part( 'category-header' ); ?>

    <?php
    // Fetch all sub-categories (that have posts)
    $subcategories = get_categories( 'hide_empty=0&parent='.get_query_var( 'cat' ) );
    // Divide subcategories into two different sections, "series" or "print"
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
        // We're going to use some math to split our categories into columns of three
        foreach ($print_subcategories as $subcategory):
          if ($count % 3 === 0) {
            echo '<ul>';
          }
          echo '<li><a href="'.get_category_link($subcategory->term_id).'">'.$subcategory->name.'</a></li>';
          $count++;
          if ($count % 3 === 0 or $count - 1 === count($print_subcategories)) {
            echo '</ul>';
          }
        endforeach; ?>
        </section>
      </div>
    </section>

    <?php if ( have_posts() ) :
      /* Start the Loop */
      while ( have_posts() ) : the_post(); ?>

        <?php
          /* Include the Post-Format-specific template for the content. */
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

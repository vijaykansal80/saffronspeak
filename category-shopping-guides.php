<?php
/**
 * The template for displaying the Shopping Guides category.
 *
 * For this category, instead of listing its posts, we're just
 * going to show a list of all its sub-categories. These are
 * then individually styled using CSS.
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
    foreach ( $subcategories as $subcategory ):
    ?>

      <div class="subcategory <?php echo smarter_slug( $subcategory ); ?>">
        <h2><?php echo $subcategory->name; ?></h2>
        <?php // Our current featured category should show an "Updated for 2015" badge
        if ( $subcategory->term_id == $featured->term_id ): ?>
          <img class="badge" src="<?php bloginfo( stylesheet_directory ); ?>/images/updated-for-2015.png" alt="Updated for 2015"/>
        <?php endif; ?>
          <p class="read-more"><a href="<?php echo get_category_link( $subcategory->term_id ); ?>">Read more</a></p>
          <a class="div-link" href="<?php echo get_category_link( $subcategory->term_id ); ?>"></a>
      </div>
    <?php endforeach; ?>


    </main><!-- #main -->
  </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>

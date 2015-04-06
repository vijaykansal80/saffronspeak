    <?php
    $category_id = get_query_var( 'cat' );
    $category = get_category( $category_id );

    // If this is a top-level category, we display a fancy introductory panel
    if ( $category->category_parent === 0 ):
      $category_image = '<img src="'. get_bloginfo( stylesheet_directory ) .'/images/categories/'. $category->slug .'.jpg" />';
      $extra_header_classes = "category-intro " . $category->slug;
      $description = str_replace( '#', get_category_link( $category->term_id ), $category->description );
    endif;
    ?>

      <header class="page-header <?php echo $extra_header_classes; ?>">
        <?php
        echo $category_image;
        the_archive_title( '<h1 class="page-title">', '</h1>' );
        the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
      </header><!-- .page-header -->

    <?php
    $category_id = get_query_var( 'cat' );
    $category = get_category( $category_id );

    // If this is a top-level category, we display a fancy introductory panel
    if ( $category->category_parent === 0 ):
      $extra_header_classes = "category-intro " . $category->slug;
      $fleurons = '<i class="icon-header-fleuron-left"></i><?php echo $category->name; ?><i class="icon-header-fleuron-right"></i>';
      $description = str_replace( '#', get_category_link( $category->term_id ), $category->description );
    endif;
    ?>

      <header class="page-header <?php echo $extra_header_classes; ?>">
        <img src="<?php bloginfo( stylesheet_directory ); ?>/images/categories/<?php echo $category->slug; ?>.jpg">
        <?php
        the_archive_title( '<h1 class="page-title">', '</h1>' );
        the_archive_description( '<div class="taxonomy-description">', '</div>' );
        ?>
      </header><!-- .page-header -->

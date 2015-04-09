<?php

// Using hooks is absolutely the smartest, most bulletproof way to implement things like plugins,
// custom design elements, and ads. You can add your hook calls below, and they should take the
// following form:
// add_action('thesis_hook_name', 'function_name');
// The function you name above will run at the location of the specified hook. The example
// hook below demonstrates how you can insert Thesis' default recent posts widget above
// the content in Sidebar 1:
// add_action('thesis_hook_before_sidebar_1', 'thesis_widget_recent_posts');


// Get rid of empty p tags in posts
add_filter('the_content', 'remove_empty_p', 20, 1);
function remove_empty_p($content){
    $content = force_balance_tags($content);
    return preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
}


// Disable certain plugins' ugly stylesheets
function remove_plugin_styles() {
    wp_dequeue_style('yarppRelatedCss');
    wp_deregister_style('yarppRelatedCss');
}
add_action('wp_footer', remove_plugin_styles);

// Re-queue Sharedaddy JS
function tweakjp_add_sharing_js() {
    wp_enqueue_script( 'sharing-js', WP_SHARING_PLUGIN_URL . 'sharing.js', array( ), 3 );
}
add_action( 'wp_enqueue_scripts', 'tweakjp_add_sharing_js' );


// Add custom JS
function custom_scripts() {
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/custom/scripts.js', array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'custom_scripts' );


// Enable featured images/post thumbnails

add_theme_support('post-thumbnails');

function add_custom_sizes() {
    add_image_size('yarpp-thumbnail', 400, auto, true);
    add_image_size('square', 300, 300, true);
}
add_action('after_setup_theme','add_custom_sizes');

define('YARPP_GENERATE_THUMBNAILS', true);

// Remove comments on Jetpack's Carousel images
function filter_media_comment_status( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'filter_media_comment_status', 10 , 2 );


// Show custom number of posts for different archive types
function custom_posts_per_page($query) {
    if (is_tag()):
        $query->set('posts_per_page', 10);
    endif;
    if (is_search()):
        $query->set('posts_per_page', -1);
    endif;
}
add_action('pre_get_posts', 'custom_posts_per_page');


// Remove sharing links from post excerpts
function my_remove_filters_func() {
     remove_filter( 'the_excerpt', 'sharing_display', 19 );
}
add_action( 'init', 'my_remove_filters_func' );

// Add a "more" link to excerpts
function new_excerpt_more($more) {
    global $post;
    return '&hellip;';
}
add_filter('excerpt_more', 'new_excerpt_more');


/* Replace WordPress captions with HTML5 captions */

function cleaner_caption( $output, $attr, $content ) {

    /* We're not worried abut captions in feeds, so just return the output here. */
    if ( is_feed() )
        return $output;

    /* Set up the default arguments. */
    $defaults = array(
        'id' => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => '',
        'class' => '',
    );

    /* Merge the defaults with user input. */
    $attr = shortcode_atts( $defaults, $attr );

    /* If there is no caption, return the content wrapped between the [caption] tags only. */
    if ( empty( $attr['caption'] ) )
        return $content;

    /* Strip out line breaks (WordPress seems to be adding some for no good reason) */
    $attr['caption'] = str_replace("<br />", "", $attr['caption']);

    /* Set up the attributes for the caption <div>. */
    $attributes = ( !empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '' );
    $attributes .= ' class="' . esc_attr( $attr['align'] ) . ' ' . esc_attr( $attr['class'] ) . '"';

    /* Open the caption <div>. */
    $output = '<figure' . $attributes .'>';

    /* Allow shortcodes for the content the caption was created for. */
    $output .= do_shortcode( $content );

    /* Append the caption text. */
    $output .= '<figcaption>' . $attr['caption'] . '</figcaption>';

    /* Close the caption </div>. */
    $output .= '</figure>';

    /* Return the formatted, clean caption. */
    return $output;
}

add_filter( 'img_caption_shortcode', 'cleaner_caption', 10, 3);
add_filter( 'thesis_img_caption_shortcode', 'cleaner_caption', 10, 3);




/*****************************
     SERIES-SPECIFIC STYLES
******************************/

// Set featured category here
$featured_series = 107;
$featured = get_term_by('id', $featured_series, 'category');

// Return a more logical slug for categories (will relate to folder locations in theme)
function smarter_slug($category) {
    $slug = strtolower($category->name);
    $slug = str_replace('series', '', $slug);
    $slug = str_replace(' ', '-', trim($slug));
return $slug;
}

// Add page-specific stylesheets and tags
function set_custom_styles() {

    global $featured;
    global $post;

    // for individual post pages
    if (is_single()):
        $categories = get_the_category($post->ID);

        foreach($categories as $category):
            // check to see if it's a sub-category of the holiday category
            if ($category->parent == 130):
                $slug = "holidays";
            elseif ($category->slug != "shopping-guides"):
                $slug = smarter_slug($category);
            endif;
        endforeach;
    endif;

    // for category archive pages
    if (is_category()):
        $slug = get_category(get_query_var('cat'))->slug;
    endif;

    // for homepage
    if (is_front_page()):
        $slug = smarter_slug($featured);
    endif;

    // register styles for category
    if ( isset( $slug ) ) {
        wp_register_style( 'category-style',  get_template_directory_uri() . '/custom/series/'.$slug.'/styles.css' );
        wp_enqueue_style( 'category-style' );
    }

    // for promotion pages
    if ( "Promotions" == get_post_field( 'post_title', $post->post_parent ) ):
        $slug = basename( get_permalink() );
        wp_register_style( 'page-style',  get_template_directory_uri() . '/custom/promotions/'.$slug.'.css' );
        wp_enqueue_style( 'page-style' );
    endif;
}

add_action( 'wp_enqueue_scripts', 'set_custom_styles' );



// Add classes to different pages for styling purposes
function category_class($classes) {

    // for individual post pages
    if (is_single()):
        // Put category slugs in body class
        $categories = get_the_category($post->ID);
        foreach($categories as $category):
            // Generate a list of parent categories as well
            $parents = get_category_parents($category, false, ',');
            $parents = explode(',', $parents);
            foreach ($parents as $parent):
                $classes[] = str_replace(' ', '-', strtolower($parent));
            endforeach;
        endforeach;
        // If this post is sticky (ie, a parent post), add a tag for that, too
        if ( is_sticky() ):
            $classes[] = "parent-post";
        endif;
    $classes[] = "single-post";
    endif;

    // for category archive pages
    if (is_category()):
        $classes[] = get_category(get_query_var('cat'))->slug;
        $classes[] = "post-archive";
    endif;

    // for the homepage
    if (is_front_page()):
        $classes[] = "homepage";
    endif;

    // for search results pages
    if (is_search()):
        $classes[] = "search-results";
    endif;

return $classes;
}

add_filter('thesis_body_classes', 'category_class');



/*****************************
           HEADER
******************************/

// This replaces the header with a custom header to match the catalog site

function custom_header() { ?>
		<a id="logo" href="http://www.saffronmarigold.com"><img src="<?php bloginfo('stylesheet_directory'); ?>/custom/images/saffronmarigold.gif" border="0" alt="Saffron Marigold" title=" Saffron Marigold " width="324" height="90"></a>

		<form id="newsletter-signup" name="emailSignUp" method="post" action="http://www.saffronmarigold.com/catalog/email_signup.php">
    		Sign up for exclusive savings &amp; announcements
    		<br><input name="emailAddress" type="text" value="Email Address" size="18" maxlength="100" class="main" onFocus="if(this.value=='Email Address')this.value='';" onFocusout="if(this.value=='')this.value='Email Address';">&nbsp;
		<input type="image" src="http://saffronmarigold.com/catalog/images/gosign_up.gif" alt="Go" border="0">
    	</form>

    	<div id="contact-cart">
			<img src="http://saffronmarigold.com/catalog/images/saffronmarigold_small_dark.gif" width="20" height="20"> Contact us: <b>877-749-9948</b>
			or <a href="http://www.saffronmarigold.com/catalog/contact_us.php"><u>email</u></a>

        	<br>

        	<img src="http://saffronmarigold.com/catalog/images/saffronmarigold_small_light.gif" width="20" height="20">
        	<a href="http://www.saffronmarigold.com/catalog/account.php" >Sign in</a> |

        	<img src="http://saffronmarigold.com/catalog/images/shopping_bag_small.gif">
        	<a href="http://www.saffronmarigold.com/catalog/shopping_cart.php">Shopping Bag</a> |

        	<a href="http://www.facebook.com/SaffronMarigold" rel="nofollow" target="_blank"><img src="http://saffronmarigold.com/catalog/images/icons/facebook.gif" align="bottom" border="0" /></a>
    		<a href="http://twitter.com/saffronmarigold" rel="nofollow" target="_blank"><img src="http://saffronmarigold.com/catalog/images/icons/twitter.gif" align="bottom" border="0" /></a>
        </div>
<?php
}

remove_action('thesis_hook_header', 'thesis_default_header');
add_action('thesis_hook_header', 'custom_header');




// This replaces the menu with a custom menu to match the catalog site

function custom_menu() { ?>

<ul id="main-nav">

		<li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=22">Bed<br>Spreads</a>
			<ul>
			  	<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=22_30">Twin Bedspreads</a></li>
			  	<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=22_31">Queen Bedspreads</a></li>
			  	<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=22_32">King Bedspreads</a></li>
			  	<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=22_33">Quilted Bedspreads</a></li>
			</ul>
		</li>

		<li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=21">Duvet<br>Covers</a>
			<ul>
			  	<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=21_34">Twin Duvet Covers</a></li>
			  	<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=21_35">Queen Duvet Covers</a></li>
			  	<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=21_36">King Duvet Covers</a></li>
			</ul>
		</li>

		<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=24">Shower<br>Curtains</a></li>

	    <li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=25">Sheer<br>Curtains</a>
			<ul>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=25">Sheer Curtain Panels</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=26">Beaded Valances</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=62">Kitchen Curtains</a></li>
			</ul>
	    </li>

	    <li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=43">Table<br>Linen</a>
	    	<ul>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=43_29">Tablecloth Rectangular</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=43_52">Tablecloth Round</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=43_45">Dinner Napkins</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=43_56">Table Runner</a></li>
			</ul>
	    </li>


	    <li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=23">Pillow<br>Covers</a>
	    	<ul>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=23">Pillow Shams</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=28_38">Euro Shams</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=28_37">Decorative Throws</a></li>
				<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=23_63">Boudoir Shams</a></li>
			</ul>
	    </li>

	    <li id="sale-tab"><a href="http://www.saffronmarigold.com/catalog/specials.php">SALE</a></li>

	    <li id="shopby-tab"><a href="http://www.saffronmarigold.com/catalog/shop_by_print.php?cPath=59">Shop&nbsp;by<br>Print</a>
		    <ul>
		    	<li><a href="http://www.saffronmarigold.com/catalog/shop_by_print.php?cPath=59">Shop by Print</a>
			    <li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=58">Shop by Swatches</a></li>
		    </ul>
	    </li>

	    <li id="blog-tab" class="active"><a href="/blog">Blog</a>
	    	<ul>
                <?php
                $categories = get_categories(array('parent' => 0, 'exclude' => 1));
                foreach ( $categories as $category ):
                    echo '<li class="cat-item"><a href="' . get_category_link( $category->term_id ) . '">' . $category->name . '</a></li>';
                endforeach;
                ?>
				<li class="rss"><a href="http://feeds.feedburner.com/SaffronMarigoldBlog" title="Saffron Marigold Blog RSS Feed" rel="nofollow">Subscribe</a></li>
			</ul>
	    </li>
	</ul>

<?php
}

// replace with custom menu and move below header
remove_action('thesis_hook_before_header', 'thesis_nav_menu');
add_action('thesis_hook_after_header', 'custom_menu');


// Get supplementary tag information
function full_tag_string() {
    global $tag_query;
    $tag_query = $_SERVER['REQUEST_URI'];
    $tag_query = str_replace('/blog/', '', $tag_query);
    $tag_query = str_replace('tag/', '', $tag_query);
    $tag_query = str_replace(',', ', ', $tag_query);
    $tag_query = str_replace('/', '', $tag_query);
    $tag_query = str_replace('+', ', ', $tag_query);
    $tag_query = str_replace('-', ' ', $tag_query);
    $primary_tag = strtolower(single_tag_title('', false));
    $secondary_tags = str_replace($primary_tag, '', $tag_query);
    $secondary_tags = ltrim($secondary_tags, ', ');
    if ($secondary_tags != "") {
        $tag_string = "<strong>". $primary_tag ."</strong> and ". $secondary_tags;
    } else {
        $tag_string = $primary_tag;
    }
    return $tag_string;
}

function all_tags() {
    global $tag_query;
    $all_tags = explode(', ', $tag_query);
    return $all_tags;
}


// Add location-aware breadcrumbs to the left, and a search bar to the right

function breadcrumbs_and_search() {
    echo '<div class="breadcrumbs-and-search">';

    // Only show breadcrumbs on category and post pages
    if ( ! is_home() &&  ! is_front_page() && ! is_page() ):
        echo '<div class="breadcrumbs">';
        echo '<a href="';
        echo get_option('home');
        echo '">';
        echo 'Blog';
        echo "</a>";

        if (is_single()):
            echo " &raquo; ";
            the_category(' &raquo; ', 'multiple');
            echo " &raquo; ";
            the_title();

        elseif (is_category()):
            echo " &raquo; ";
            // get a list of the category's parent
            $category_list = get_category_parents(get_query_var('cat'), true, ' &raquo; ' );
            // remove current category from list (in order to display without a link or trailing arrow)
            $categories = explode(' &raquo; ', $category_list);
            array_pop($categories);
            array_pop($categories);
            foreach ($categories as $category):
                echo $category ." &raquo; ";
            endforeach;
            // and display current category name, without a link or trailing arrow
            echo get_the_category_by_id(get_query_var('cat'));

        elseif (is_page()):
            echo " &raquo; ";
            echo the_title();

        elseif (is_search()):
            echo " &raquo; Search results for: ";
            echo '"<em>';
            echo the_search_query();
            echo '</em>"';

        elseif (is_tag()):
            echo " &raquo; ";
            echo "Tag archive: ";
            echo full_tag_string();
        endif;

    echo '</div>';
    endif; // ! is_home() &&  ! is_front_page() && ! is_page()

    // Search box
    echo '<div class="search-box">';
    get_search_form();
    echo '<i class="icon-search"></i>';
    echo '</div>';

    echo '</div>'; // .breadcrumbs-and-search
}

add_action('thesis_hook_after_header','breadcrumbs_and_search');


// Remove post title for parent posts and pages
function suppress_title() {
    if ( !is_page() and is_sticky() ) {
        $return = false;
    } elseif ( is_page() ) {
        $return = false;
    } else {
        $return = true;
    }
    return $return;
}

add_filter('thesis_show_headline_area', 'suppress_title');


// Custom pages
function custom_page_templates(){
    global $post;
    if ( is_home() || is_front_page() ):
        new_homepage();
    elseif ( "Promotions" == get_post_field( 'post_title', $post->post_parent ) ):
        promotion_page();
    endif;
}
add_action('thesis_hook_custom_template', 'custom_page_templates');



/*****************************
           HOMEPAGE
******************************/

// This creates an entirely different layout for the homepage

function new_homepage() {
    global $featured; ?>
    <div id="content" class="home-content">

        <h2><?php echo bloginfo('title'); ?></h2>
        <p class="tagline"><?php echo bloginfo('description'); ?></p>
        <?php echo show_categories(); ?>

        <?php echo featured_series(smarter_slug($featured), $featured->slug); ?>

        <h2>Read more posts</h2>
        <?php echo list_posts('latest'); ?>

        <?php if (function_exists('wpp_get_mostpopular')):
            wpp_get_mostpopular("range=monthly&limit=10&post_type=post");
        endif; ?>

    </div>
<?php }



/*****************************
        PROMOTION PAGES
******************************/

function promotion_page() {
    global $post;
    if ( "Promotions" == get_post_field( 'post_title', $post->post_parent ) ):

        // Determine where our file will be located
        $dir = plugin_dir_path( __FILE__ );
        $template = parse_url(get_bloginfo('template_directory'));
        $slug = basename( get_permalink() );
        $path = $template['path']."/custom/promotions/";
        $category = get_category_by_slug($slug);

        echo '<div class="promotion '.$slug.'">';
            include($dir."/promotions/".$slug.".php");
        echo '</div>';
    endif;
}

//add_action('thesis_hook_after_header', 'promotion_page');



// Show categories widget
function show_categories($args = array('parent' => 0, 'exclude' => 1)) {
    $categories = get_categories($args);
    foreach($categories as $category):
        ?>
        <div class="category <?php echo $category->slug; ?>">
            <a href="<?php echo get_category_link( $category->term_id ); ?>"><img src="<?php bloginfo(stylesheet_directory); ?>/custom/images/categories/<?php echo $category->slug; ?>.jpg"></a>
            <h3><a href="<?php echo get_category_link( $category->term_id ); ?>"><?php echo $category->name; ?></a></h3>
            <p><?php echo str_replace('#', get_category_link($category->term_id), $category->description); ?></p>
        </div>
    <?php
    endforeach;
}


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




/*****************************
           ARCHIVE PAGES
******************************/

// Remove pagination for Shopping Guides parent
function nix_nav() {
    if (is_category()):
        if(get_query_var('cat') === 5):
            remove_action('thesis_hook_after_content', 'thesis_post_navigation');
        endif;
    endif;
}
add_action('thesis_hook_before_content','nix_nav');


// Show custom archive pages for different archive types
class archive_looper extends thesis_custom_loop {

    // Category archives
    function category() {

        // Determine what category is being shown, and generate category object & list of subcategories
        $category_id = get_query_var('cat');
        $category = get_category($category_id);
        $subcategories = get_categories('hide_empty=0&parent='.$category_id);

        // Display a customized category intro panel, for top-level categories only
        if ($category->category_parent === 0):
            ?>
            <header class="category-intro <?php echo $category->slug; ?>">
                <img src="<?php bloginfo(stylesheet_directory); ?>/custom/images/categories/<?php echo $category->slug; ?>.jpg">
                <div>
                    <h1><i class="icon-header-fleuron-left"></i><?php echo $category->name; ?><i class="icon-header-fleuron-right"></i></h1>
                    <p><?php echo str_replace('#', get_category_link($category->term_id), $category->description); ?></p>
                </div>
            </header>
        <?php else: ?>
            <section class="headline_area">
                <h1 class="entry-title"><?php echo $category->name; ?></h1>
            </section>
        <?php endif;


        // If we're in the Design Resources category, display an expandable panel with a list of sub-categories
        if($category->name === "Design Resources"):
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
        <?php endif; ?>


        <?php // If we're in the Shopping Guides category, display a styled block for each sub-category
        if($category->name === "Shopping Guides"):
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

            while (have_posts()):
                the_post();
                echo '<div class="post-excerpt">';
                if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                        <?php the_post_thumbnail(''); ?>
                    </a>
                <?php endif; ?>
                    <div class="headline_area">
                        <?php echo post_meta(); ?>
                        <a href="<?php the_permalink() ?>"><h2 class="entry-title"><?php echo get_the_title() ?></h2></a>
                    </div>
                    <div class="format_text entry-content">
                        <p><?php the_advanced_excerpt('length=40&use_words=1&no_custom=1&ellipsis=&finish_sentence=1'); ?></p>
                        <p class="read-more"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">Read more</a></p>
                    </div>
                </div>
            <?php endwhile;
        endif;
    }

    // Tag archives
    function tag() {
        while ( have_posts() ):
            show_shorter_posts( 'tag' );
        endwhile;
    }

    // Search results
    function search() {
        if ( have_posts() ):
            while ( have_posts() ):
                show_shorter_posts( 'search' );
            endwhile;

        // Show a basic error message
        else:
            echo '<div class="no-results">';
            echo '<h1>No results found.</h1>';
            echo '<div class="format_text">Sorry, we were unable to find any posts that matched your query. Try again with a less specific search term.</div>';
            echo '</div>';
        endif;
    }

}
$the_looper = new archive_looper;


function show_shorter_posts( $archive ) {
    the_post();
    echo '<div class="post-excerpt-alt">';
        if (has_post_thumbnail()): ?>
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                <?php the_post_thumbnail(''); ?>
            </a>
        <?php endif; ?>
        <div class="format_text entry-content">
            <h4 class="entry-title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
            <p>
            <?php
            if ( "search" === $archive ):
                the_excerpt();
            else:
                the_advanced_excerpt('length=40&use_words=1&no_custom=1&ellipsis=&finish_sentence=1');
            endif;
            ?>
            </p>

            <?php
                $tag_string = get_the_tag_list('', ',', '');
                $tags = explode(',', $tag_string);
            ?>
            <p class="tags"><span>Tags</span>
            <?php
                foreach($tags as $key => $tag):
                    if ($key != 0):
                        echo " &middot; ";
                    endif;
                    $tagless_tag = strtolower(strip_tags($tag));
                    if (in_array($tagless_tag, all_tags())):
                        echo "<strong>". $tag . "</strong>";
                    else:
                        echo $tag;
                    endif;
                endforeach;
            ?>
            </p>
        </div>
    </div>
<?php }


/*****************************
           POSTS
******************************/

// Show featured image if requested

function add_params($query_vars) {
    $query_vars[] = 'featured';
    return $query_vars;
}
add_filter('query_vars', 'add_params' );

function show_featured_image() {
    global $post;
    if ( "Promotions" == get_post_field( 'post_title', $post->post_parent ) || ( ! is_page() AND "yes" == get_query_var('featured') ) ):
        if ( has_post_thumbnail() ) {
            echo "<br />";
            echo the_post_thumbnail('medium');
        }
    endif;
}
add_filter('thesis_hook_after_headline', 'show_featured_image');

// This creates a custom instance of the byline/post meta boxes—publishing information on top, category information below

function post_meta() {
    if (!is_page() and !is_sticky()): ?>
        </section>
        <section class="post-meta">
            Published
            <abbr class="published" title="<?php echo get_the_time('Y-m-d H:i'); ?>"><?php echo get_the_time(get_option('date_format')); ?></abbr>
            by <?php the_author_posts_link(); ?>
        </section>

    <?php
    endif;
}

add_action('thesis_hook_before_headline', 'post_meta');




// This adds series-specific navigation and text blocks to the bottom of posts

function series_navigation() {
    if (is_single() && !is_sticky()):

        // Make sure we only have a single category to work with
        $categories = get_the_category($post->ID);
        if (count($categories) === 1) {
            $category = $categories[0];
        }

        // If we're in a nested sub-category, we want to jump up to the parent
        $parent = get_term_by('id', $category->category_parent, 'category');
        if ($parent->parent != 0):
            $category = $parent;
        endif;

        $series_link = get_parent_post_link($category->term_id);

        // Only show for Shopping Guide posts (at least for now!)
        if ($category->parent === 5): ?>
            <section class="panel series-navigation">

                <p><?php echo $category->description; ?></p>

                <div class="previous-post">
                    <?php $previous_post = get_adjacent_post(true, '', true); ?>
                    <?php if (!empty($previous_post)): ?>
                        <a href="<?php echo get_permalink($previous_post->ID); ?>">&laquo; Previous post</a>
                    <?php endif; ?>
                </div>

                <div class="all-posts">
                    <a href="<?php echo $series_link; ?>">View all <?php echo $category->name; ?> posts</a>
                </div>

                <div class="next-post">
                    <?php $next_post = get_adjacent_post(true, '', false); ?>
                    <?php if (!empty($next_post)): ?>
                        <a href="<?php echo get_permalink($next_post->ID); ?>">Next post &raquo;</a>
                    <?php endif; ?>
                </div>

            </section>
        <?php endif;

    endif;
}

add_action('thesis_hook_after_post', 'series_navigation', '1');



// This adds tags to the bottom of posts, and moves YARPP below the tags

function post_tags() {
    if (is_single()): ?>
        <section class="post-tags">
            <span>Find related posts by tags: </span> <?php echo get_the_tag_list('', ' &middot; ', ''); ?>
        </section>

    <?php
    add_action('thesis_hook_after_post', 'related_posts', '2');
    endif;
}

add_action('thesis_hook_after_post', 'post_tags', '1');




// This replaces the footer with a custom footer

function custom_footer() { ?>

	<p>Copyright &copy; <?php echo date('Y'); ?>  Saffron Marigold</p>

<?php }

remove_action('thesis_hook_footer', 'thesis_attribution');
add_action('thesis_hook_footer', 'custom_footer');



/**
 * function custom_bookmark_links() - outputs an HTML list of bookmarking links
 * NOTE: This only works when called from inside the WordPress loop!
 * SECOND NOTE: This is really just a sample function to show you how to use custom functions!
 *
 * @since 1.0
 * @global object $post
*/

function custom_bookmark_links() {
	global $post;
?>
<ul class="bookmark_links">
	<li><a rel="nofollow" href="http://delicious.com/save?url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>" onclick="window.open('http://delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url=<?php urlencode(the_permalink()); ?>&amp;title=<?php urlencode(the_title()); ?>', 'delicious', 'toolbar=no,width=550,height=550'); return false;" title="Bookmark this post on del.icio.us">Bookmark this article on Delicious</a></li>
</ul>
<?php
}

/*
remove_action('thesis_hook_after_post', 'thesis_comments_link');
add_action('thesis_hook_after_headline', 'thesis_comments_link');
*/

/*
function fb_like() {
	if (is_single()) { ?>
		<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:60px;"></iframe>
		<?php }
}
add_action('thesis_hook_after_post','fb_like');
*/


/*
function my_comments_link() {
  if (!is_single() && !is_page()) {
    echo '<p class="to_comments"><a href="';
    the_permalink();
    echo '#comments" rel="nofollow">';
    comments_number(__('Be the first to comment >', 'thesis'), __('<span>1</span> comment... add one >', 'thesis'), __('<span>%</span> comments... add one >', 'thesis'));
    echo '</a></p>';
  }
}
add_action('thesis_hook_after_post', 'my_comments_link');
*/
remove_action('thesis_hook_after_post', 'thesis_comments_link');

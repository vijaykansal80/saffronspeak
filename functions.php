<?php
/**
 * Safflower functions and definitions
 *
 * @package safflower
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 850; /* pixels */
}

if ( ! function_exists( 'safflower_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function safflower_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on safflower, use a find and replace
	 * to change 'safflower' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'safflower', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'safflower' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'safflower_custom_background_args', array(
		'default-color' => '#FEE6D6',
		'default-image' => '',
	) ) );
}
endif; // safflower_setup
add_action( 'after_setup_theme', 'safflower_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function safflower_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'safflower' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'safflower_widgets_init' );

/**
 * Load all Google fonts used in theme
 */
function safflower_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	* supported by PT Serif, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$gentium = _x( 'on', 'Gentium Basic font: on or off', 'safflower' );

	if ( 'off' !== $pt_serif || 'off' !== $open_sans ) :
		$font_families = array();

		if ( 'off' !== $pt_serif ) {
			$font_families[] = 'Gentium Basic:400,400italic,700,700italic';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );

	endif;
	return $fonts_url;
}


/**
 * Enqueue scripts and styles.
 */
function safflower_scripts() {
	wp_enqueue_style( 'safflower-style', get_stylesheet_uri() );

	// General JS functions
	wp_enqueue_script( 'safflower-scripts', get_template_directory_uri() . '/js/safflower.js', array(), '20150402', true );

	// Custom icon font & web fonts
	wp_enqueue_style( 'safflower-icons', get_template_directory_uri() . '/fonts/icons.css', array(), '20150326' );
	wp_enqueue_style( 'safflower-nuptial', get_template_directory_uri() . '/fonts/nuptial.css', array(), '20150326' );
	wp_enqueue_style( 'safflower-fonts', safflower_fonts_url(), array(), null );

	// Navigation
	wp_enqueue_script( 'safflower-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'safflower-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'safflower_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Custom functions that allow for series-specific styling.
 */
require get_template_directory() . '/inc/series-styles.php';

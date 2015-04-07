<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Safflower
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'safflower' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<?php
			if ( function_exists( 'jetpack_the_site_logo' ) ):
				 jetpack_the_site_logo();
			endif;
			?>
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		</div><!-- .site-branding -->

		<div class="site-contact">
			<img class="flower" src="http://saffronmarigold.com/catalog/images/saffronmarigold_small_dark.gif" width="20" height="20">
			<strong>877-749-9948</strong> &middot; <a href="http://www.saffronmarigold.com/catalog/contact_us.php">email</a>
    	<img class="flower" src="http://saffronmarigold.com/catalog/images/saffronmarigold_small_light.gif" width="20" height="20">
    	<a href="http://www.saffronmarigold.com/catalog/account.php">Sign in</a>
    	<img src="http://saffronmarigold.com/catalog/images/shopping_bag_small.gif">
    	<a href="http://www.saffronmarigold.com/catalog/shopping_cart.php">Shopping Bag</a>
    	<a href="http://www.facebook.com/SaffronMarigold" rel="nofollow" target="_blank"><img src="http://saffronmarigold.com/catalog/images/icons/facebook.gif" /></a>
  		<a href="http://twitter.com/saffronmarigold" rel="nofollow" target="_blank"><img src="http://saffronmarigold.com/catalog/images/icons/twitter.gif" /></a>

			<form class="site-signup" name="emailSignUp" method="post" action="http://www.saffronmarigold.com/catalog/email_signup.php">
	  		<h5>Sign up for <span>exclusive</span> savings &amp; announcements</h5>
	  		<input name="emailAddress" type="email" value="Email Address" placeholder="Email address" maxlength="100" />
				<input type="submit" value="Go" />
	    </form><!-- .site-signup -->

    </div><!-- .site-contact -->

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php _e( 'Primary Menu', 'safflower' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<section class="breadcrumbs-and-search">
		<?php safflower_breadcrumbs(); ?>

	  <div class="search-box">
	    <?php get_search_form(); ?>
	    <i class="icon-search"></i>
	  </div>

  </section>

	<div id="content" class="site-content">

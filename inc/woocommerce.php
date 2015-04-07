<?php
/**
 * WooCommerce Compatibility File
 * See: http://docs.woothemes.com/documentation/plugins/woocommerce/
 *
 * @package Safflower
 */

/**
 * Remove WooCommerce's default wrapper and replace with one that matches our theme.
 * See: http://docs.woothemes.com/document/third-party-custom-theme-compatibility/
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

function safflower_wrapper_start() {
  echo '<div id="primary" class="content-area">';
  echo '<main id="main" class="site-main" role="main">';
}

function safflower_wrapper_end() {
  echo '</main><!-- #main -->';
  echo '</div><!-- #primary -->';
}

add_action( 'woocommerce_before_main_content', 'safflower_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'safflower_wrapper_end', 10 );

/**
 * Declare WooCommerce theme support. (A little bold, but so be it!)
 */
function safflower_woocommerce_support() {
  add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'safflower_woocommerce_support' );

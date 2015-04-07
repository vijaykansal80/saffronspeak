<?php
/**
 * This is a hacky way of ensuring that posts don't appear on the Shopping Guides
 * archive, even if Infinite Scroll is active. If we decide to use IS, this should
 * be replaced with a more elegant solution (ie, destroy the WP Query on that page)
 * @todo Replace with a more elegant solution!
 *
 * @package Safflower
 */

the_title( '<h6 style="display: none">', '</h6>' );

?>

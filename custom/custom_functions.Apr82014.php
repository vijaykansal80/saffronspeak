<?php

// Using hooks is absolutely the smartest, most bulletproof way to implement things like plugins,
// custom design elements, and ads. You can add your hook calls below, and they should take the 
// following form:
// add_action('thesis_hook_name', 'function_name');
// The function you name above will run at the location of the specified hook. The example
// hook below demonstrates how you can insert Thesis' default recent posts widget above
// the content in Sidebar 1:
// add_action('thesis_hook_before_sidebar_1', 'thesis_widget_recent_posts');

// Delete this line, including the dashes to the left, and add your hooks in its place.

/* Add the top navigation menu */
/* Links for top menu will need to be manually added here, since this menu isn't supported by Thesis 
function topnav_menu() {
?>
  <ul id="topnav">
<li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=23">Pillow Covers</a></li>
<li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=22">Bedspreads</a></li>
<li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=21">Duvet Covers</a></li>
<li><a href="http://www.saffronmarigold.com/catalog/gateway.php?cPath=43">Table Linens</a></li>
<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=24">Shower Curtains</a></li>
<li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=25">Sheer Curtains</a></li>
</ul>
<?php
}
add_action('thesis_hook_before_header', 'topnav_menu');
*/

remove_action('thesis_hook_before_header', 'thesis_nav_menu');
add_action('thesis_hook_after_header', 'thesis_nav_menu');

function search_with_button_submit() { ?>
<div id="my-search"><form method="get" class="search_form_visible" action="<?php bloginfo('home'); ?>/"><p><input class="text_input" type="text" value="Enter Text &amp; Click Search" name="s" id="s" onfocus="if (this.value == 'Enter Text &amp; Click Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter Text &amp; Click Search';}" /><input type="submit" class="my-search" id="searchsubmit" value="SEARCH" /></p></form></div>
<?php
}
add_action('thesis_hook_header', 'search_with_button_submit');
//add_action('thesis_hook_last_nav_item', 'search_with_button_submit');

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


function my_comments_link() {
  if (!is_single() && !is_page()) {
    echo '<p class="to_comments"><a href="';
    the_permalink();
    echo '#comments" rel="nofollow">';
    comments_number(__('Be the first to comment >', 'thesis'), __('<span>1</span> comment... add one >', 'thesis'), __('<span>%</span> comments... add one >', 'thesis'));
    echo '</a></p>';
  }
}

remove_action('thesis_hook_after_post', 'thesis_comments_link');
add_action('thesis_hook_after_post', 'my_comments_link');

remove_action('thesis_hook_footer', 'thesis_attribution');

function copyright() {
		echo '<p>Copyright &copy; ' . date('Y') . ' Saffron Marigold</p>';
}
add_action('thesis_hook_footer', 'copyright', '99');

/*Build Header Widget*/
register_sidebars(1,
    array(
        'name' => 'Header Widget',
        'before_widget' => '<li class="widget %2$s" id="%1$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    )
);


function header_widget() { ?>
	<div id="header_widget_1" class="sidebar">
		<ul class="sidebar_list">
			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Header Widget') ){	?>
						<li class="widget"><h3><?php _e('Header Widget', 'thesis'); ?></h3>You can edit the content that appears here by visiting your Widgets panel and modifying the <em>current widgets</em> there.</li><?php } ?>
		</ul>
	</div>
<?php }
add_action('thesis_hook_header', 'header_widget', '1');

function single_subscribe() { ?>
<!--  if (is_single()) { ?> -->

  <div id="singlesubscribe">
    <form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=SaffronMarigoldBlog', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
    <span style="font-size:14px;font-weight:normal">You can also receive these posts via email..</span>
    <input class="txt" value="Enter email address here" onfocus="if (this.value == 'Enter email address here') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter email address here';}" name="email" type="text">
    <input name="uri" value="SaffronMarigoldBlog" type="hidden">
    <input value="en_US" name="loc" type="hidden"> 
    <input value="Subscribe" class="btn" type="submit" onclick="_gaq.push(['_trackEvent', 'Lead Capture', 'Subscribe', 'Blog Feedburner email subscription']);">
    </form>
    </div>
    <?php
    /*    }*/
}

add_action('thesis_hook_after_post_box', 'single_subscribe');

<?php

// Using hooks is absolutely the smartest, most bulletproof way to implement things like plugins,
// custom design elements, and ads. You can add your hook calls below, and they should take the 
// following form:
// add_action('thesis_hook_name', 'function_name');
// The function you name above will run at the location of the specified hook. The example
// hook below demonstrates how you can insert Thesis' default recent posts widget above
// the content in Sidebar 1:
// add_action('thesis_hook_before_sidebar_1', 'thesis_widget_recent_posts');


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
	    
	    <li id="sale-tab"><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=60">SALE</a></li>
	    
	    <li id="shopby-tab"><a href="http://www.saffronmarigold.com/catalog/shop_by_print.php?cPath=59">Shop&nbsp;by<br>Print</a>
		    <ul>
		    	<li><a href="http://www.saffronmarigold.com/catalog/shop_by_print.php?cPath=59">Shop by Print</a>
			    <li><a href="http://www.saffronmarigold.com/catalog/directory.php?cPath=58">Shop by Swatches</a></li>
		    </ul>
	    </li>
	    
	    <li id="blog-tab" class="active"><a href="/blog">Blog</a>
	    	<ul>
				<li class="cat-item cat-item-7"><a href="http://www.saffronmarigold.com/blog/category/customer-solutions/" title="Enter the world of our customers">Customer Connection</a></li>
				<li class="cat-item cat-item-6"><a href="http://www.saffronmarigold.com/blog/category/inspirational-ideas/" title="Home decor ideas that inspire">Design Resources</a></li>
				<li class="cat-item cat-item-5"><a href="http://www.saffronmarigold.com/blog/category/shopping-guides/" title="So many options, so hard to decide - we can help!">Shopping Guides</a></li>
				<li class="cat-item cat-item-8"><a href="http://www.saffronmarigold.com/blog/category/tradition-technology/" title="A look behind the scenes ">Technology &amp; Tradition</a></li>
				<li class="rss"><a href="http://feeds.feedburner.com/SaffronMarigoldBlog" title="Saffron Marigold Blog RSS Feed" rel="nofollow">Subscribe</a></li>
			</ul>
	    </li>
	</ul>

<?php
}

// replace with custom menu and move below header
remove_action('thesis_hook_before_header', 'thesis_nav_menu');
add_action('thesis_hook_after_header', 'custom_menu');



// add some breadcrumbs

function thesis_breadcrumbs() {
	if (!is_home()) {
	echo '<div class="breadcrumbs">';
	echo '<a href="';
	echo get_option('home');
	echo '">';
	//bloginfo('name');
	echo 'Blog';
	echo "</a>";
		if (is_category() || is_single()) {
			echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
			the_category(' &bull; ');
				if (is_single()) {
					echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
					the_title();
				}
        } elseif (is_page()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
            echo the_title();
		} elseif (is_search()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
			echo '"<em>';
			echo the_search_query();
			echo '</em>"';
        }
	}
	echo '</div>';
}


add_action('thesis_hook_after_header','thesis_breadcrumbs');




// This replaces the footer with a custom footer and search box

function custom_footer() { ?>

		<div id="my-search">
			<form method="get" class="search_form_visible" action="http://www.saffronmarigold.com/blog/">
				<input class="text_input" type="text" value="Enter Text &amp; Click Search" name="s" id="s" onfocus="if (this.value == 'Enter Text &amp; Click Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter Text &amp; Click Search';}" />
				<input type="submit" class="my-search" id="searchsubmit" value="SEARCH" />
			</form>
		</div>

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


/* 2014-04-26 Sandip: Get rid of this. Hardly anyone subscribes to the blog anyway */
/**
function single_subscribe() { ?>
<!--  if (is_single()) { ?> -->
<!--
  <div id="singlesubscribe">
    <form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=SaffronMarigoldBlog', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
    <span style="font-size:14px;font-weight:normal">You can also receive these posts via email..</span>
    <input class="txt" value="Enter email address here" onfocus="if (this.value == 'Enter email address here') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Enter email address here';}" name="email" type="text">
    <input name="uri" value="SaffronMarigoldBlog" type="hidden">
    <input value="en_US" name="loc" type="hidden"> 
    <input value="Subscribe" class="btn" type="submit" onclick="_gaq.push(['_trackEvent', 'Lead Capture', 'Subscribe', 'Blog Feedburner email subscription']);">
    </form>
    </div>
-->
    <?php
}

//add_action('thesis_hook_after_post_box', 'single_subscribe');

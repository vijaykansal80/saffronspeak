<?php
function thesis_register_widgets(){
	register_widget('thesis_search_widget');
	register_widget('thesis_widget_subscriptions');
	register_widget('thesis_widget_google_cse');
	register_widget('thesis_killer_recent_entries');
}

function thesis_register_sidebars() {
	register_sidebars(2,
		array(
			'name' => 'Sidebar %d',
			'before_widget' => '<li class="widget %2$s" id="%1$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3>',
			'after_title' => '</h3>'
		)
	);
}

function thesis_default_widget($sidebar = 1) {
	global $thesis_design;

	if ((!dynamic_sidebar($sidebar) && $thesis_design->display['sidebars']['default_widgets']) || ! empty($_GET['template'])) {
?>
					<li class="widget">
						<div class="widget_box">
							<h3><?php _e('Default Widget', 'thesis'); ?></h3>
							<p class="remove_bottom_margin"><?php printf(__('This is Sidebar %1$d. You can edit the content that appears here by visiting your <a href="%2$s">Widgets panel</a> and modifying the <em>current widgets</em> in Sidebar %1$d. Or, if you want to be a true ninja, you can add your own content to this sidebar by using the <a href="%3$s">appropriate hooks</a>.', 'thesis'), $sidebar, get_bloginfo('wpurl') . '/wp-admin/widgets.php', 'http://diythemes.com/thesis/rtfm/hooks/'); ?></p>
						</div>
					</li>
<?php
	}
}

function thesis_search_form() {
	$field_value = apply_filters('thesis_search_form_value', __('To search, type and hit enter', 'thesis'));
?>
	<form method="get" class="search_form" action="<?php bloginfo('url'); ?>/">
		<p>
			<input class="text_input" type="text" value="<?php echo esc_html($field_value); ?>" name="s" id="s" onfocus="if (this.value == '<?php echo esc_html($field_value); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo esc_html($field_value); ?>';}" />
			<input type="hidden" id="searchsubmit" value="Search" />
		</p>
	</form>
<?php
}

class thesis_search_widget extends WP_Widget {
	function thesis_search_widget() {
		$widget_ops = array('classname' => 'thesis_widget_search', 'description' => __('The WordPress search form with helpful options that make it more flexible.', 'thesis'));
		$control_ops = array('id_base' => 'thesis-search-widget');
		$this->WP_Widget('thesis-search-widget', __('Thesis &raquo; Search Widget', 'thesis'), $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);
		$title = $instance['title'];
		$default_value = $instance['default_value'];
		$type = $instance['show_submit'] === 'true' ? 'submit' : 'hidden';
		$submit_value = $instance['submit_value'];
		echo $before_widget;
		if ($title)
			printf('%s', $before_title . wp_kses_post($title) . $after_title);
?>
	<form method="get" class="search_form" action="<?php printf('%s', esc_url(bloginfo('url'))); ?>">
		<p>
			<input class="text_input" type="text" value="<?php echo esc_html($default_value); ?>" name="s" id="s" onfocus="if (this.value == '<?php echo esc_html($default_value); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo esc_html($default_value); ?>';}" />
			<input type="<?php echo $type; ?>" id="searchsubmit" value="<?php echo esc_attr($submit_value); ?>" />
		</p>
	</form>
<?php
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = wp_parse_args($new_instance, $old_instance);
		return $instance;
	}

	function form($instance){
		$defaults = array(
			'title' => '',
			'default_value' => esc_html__('To search, type and hit enter', 'thesis'),
			'show_submit' => false,
			'submit_value' => __('Search', 'thesis')
		);
		
		$instance = wp_parse_args($instance, $defaults);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'thesis'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('default_value'); ?>"><?php _e('Search Field Text:', 'thesis'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('default_value'); ?>" name="<?php echo $this->get_field_name('default_value'); ?>" value="<?php echo $instance['default_value']; ?>" style="width:90%;" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_submit'], 'true' ); ?> id="<?php echo $this->get_field_id('show_submit'); ?>" name="<?php echo $this->get_field_name('show_submit'); ?>" value="true" /> 
			<label for="<?php echo $this->get_field_id('show_submit'); ?>"><?php _e('Display Submit Button', 'thesis'); ?></label>
		</p>
		<p>
			<label type="text" for="<?php echo $this->get_field_id('submit_value'); ?>"><?php _e('Submit Button Text:', 'thesis'); ?></label>
			<input id="<?php echo $this->get_field_id('submit_value'); ?>" name="<?php echo $this->get_field_name('submit_value'); ?>" value="<?php esc_attr_e($instance['submit_value']); ?>" style="width:90%;" />
		</p>
<?php
	}
}

class thesis_widget_subscriptions extends WP_Widget {
	function thesis_widget_subscriptions() {
		$widget_ops = array(
			'classname' => 'thesis_widget_subscriptions',
			'description' => __('Provide visitors to your site a link to your RSS feed, a description of your RSS subscription options, and information about how to contact you via email.', 'thesis')
		);
		$control_ops = array(
			'id_base' => 'thesis-subscriptions'
		);
		$this->WP_Widget('thesis-subscriptions', __('Thesis &raquo; Subscriptions', 'thesis'), $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);
		$description = !empty($instance['description']) ? "<p>" . wp_kses_post($instance['description']) . "</p>\n" : '';
		if (!empty($instance['rss_text']) || !empty($instance['email'])) {
			$list = "<ul>\n";
			$list .= !empty($instance['rss_text']) ? "\t<li class=\"sub_rss\"><a href=\"" . esc_url(thesis_feed_url()) . '">' . esc_attr__($instance['rss_text']) . "</a></li>\n" : '';
			$list .= !empty($instance['email']) ? "\t<li class=\"sub_email\">" . $instance['email'] . "</li>\n" : '';
			$list .= "</ul>\n";
		}
		echo $before_widget . "\n" . $before_title . wp_kses_post($instance['title']) . $after_title . "\n" . $description . $list . "\n" . $after_widget . "\n";
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = sprintf('%s', wp_kses_post(stripslashes($new_instance['title'])));
		$instance['description'] = sprintf('%s', wp_kses_post($new_instance['description']));
		$instance['rss_text'] = sprintf('%s', wp_kses_data(stripslashes($new_instance['rss_text'])));
		$instance['email'] = sprintf('%s', wp_kses_data($new_instance['email']));
		return $instance;
	}

	function form($instance) {
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'thesis'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php esc_attr_e($instance['title']); ?>" />
		</p>
		<p>
			<label for"<?php echo $this->get_field_id('description'); ?>"><?php _e('Describe your subscription options:', 'thesis'); ?></label>
			<textarea class="widefat" rows="8" cols="10" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php printf('%s', esc_attr($instance['description'])); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('rss_text'); ?>"><?php _e('<acronym title="Really Simple Syndication">RSS</acronym> link text:', 'thesis'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('rss_text'); ?>" name="<?php echo $this->get_field_name('rss_text'); ?>" value="<?php esc_attr_e($instance['rss_text']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email link and text:', 'thesis'); ?></label>
			<textarea class="widefat" rows="8" cols="10" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>"><?php printf('%s', esc_textarea($instance['email'])); ?></textarea>
		</p>
<?php	
	}
}

class thesis_widget_google_cse extends WP_Widget {
	function thesis_widget_google_cse() {
		$widget_ops = array(
			'classname' => 'thesis_widget_google_cse',
			'description' => __('Add Google Custom Search to your site by pasting your code here.', 'thesis')
		);
		$control_ops = array(
			'id_base' => 'thesis-google-cse'
		);
		$this->WP_Widget('thesis-google-cse', __('Thesis &raquo; Google Custom Search', 'thesis'), $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);
		$title = $instance['title'];
		$code = $instance['code'];
		if ($code) {
			echo "$before_widget\n";
			if ($title)
				echo $before_title . wp_kses_post($title) . $after_title . "\n";
			echo stripslashes($code) . "\n";
			echo "$after_widget\n";
		}
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = sprintf('%s', wp_kses_post(stripslashes($new_instance['title'])));
		$instance['code'] = $new_instance['code'];
		return $instance;
	}

	function form($instance) {
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'thesis'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php esc_attr_e($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('code'); ?>"><?php _e('Google Custom Search Code:'); ?></label>
			<textarea class="widefat" rows="8" cols="10" name="<?php echo $this->get_field_name('code'); ?>" id="<?php echo $this->get_field_id('code'); ?>"><?php printf('%s', esc_attr($instance['code'])); ?></textarea>
		</p>
<?php
	}
}

class thesis_killer_recent_entries extends WP_Widget {
	function thesis_killer_recent_entries() {
		$widget_ops = array(
			'classname' => 'widget_killer_recent_entries',
			'description' => __('Add a customizable list of recent posts from any category on your site.', 'thesis')
		);
		$control_ops = array(
			'id_base' => 'thesis-killer-recent-entries'
		);
		$this->WP_Widget('thesis-killer-recent-entries', __('Thesis &raquo; Killer Recent Entries', 'thesis'), $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		global $posts;
		extract($args);
		if (empty($instance['title'])) {
			if (!is_int($instance['cat'])) // all cats selected
				$title = __('More Recent Posts', 'thesis');
			else { // a cat has been selected, but keine title so we use the cat name
				$cat_info = get_term((int) $instance['cat'], 'category');
				$title = __($cat_info->name, 'thesis');
			}
		}
		else // title was input by user
			$title = $instance['title'];
		$offset = is_home() && $instance['cat'] == 'all' ? count($posts) : 0;
		$num = (int) $instance['numposts'];
		$cat_num = ($instance['cat'] == 'all' ? null : (int) $instance['cat']); // "all" if all, some integer if a specific cat
		$comms = (int) $instance['comments'];
		$thesis_kre_args = array(
			'offset' => $offset,
			'posts_per_page' => $num,
			'cat' => $cat_num
		);
		$thesis_kre_query = new WP_Query($thesis_kre_args);
		$out = $before_widget . $before_title . wp_kses_post($title) . $after_title . "<ul>";
		while ($thesis_kre_query->have_posts()) {
			$thesis_kre_query->the_post();
			$comments_number = (int) get_comments_number();
			$out .= "<li><a href=\"" . esc_url(get_permalink($thesis_kre_query->post->ID)) . "\" title=\"" . __('Click to read ', 'thesis')
			 	 . wp_kses($thesis_kre_query->post->post_title, array()) . "\" rel=\"bookmark\">" . wp_kses_post($thesis_kre_query->post->post_title) . "</a>";
			if ($comms == 1) {
				$out .= " <a href=\"" . esc_url(get_permalink($thesis_kre_query->post->ID)) . "#comments\">"
					 . "<span class=\"num_comments\" title=\"$comments_number ";
				$out .= $comments_number == 1 ? __("comment", 'thesis') : __("comments", 'thesis');
				$out .= __(' on this post', 'thesis') . "\">$comments_number</span></a>";
			}
			$out .= "</li>";
		}
		$out .= "</ul>" . $after_widget;
		echo $out;		
		wp_reset_query();
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = sprintf('%s', $new_instance['title']);
		$instance['numposts'] = sprintf('%d', (int) $new_instance['numposts']);
		$instance['cat'] = (is_int($instance['cat']) ? sprintf('%d', (int) $new_instance['cat']) : sprintf('%s', (string) wp_kses($new_instance['cat'], array())));
		$instance['comments'] = sprintf('%d', (int) $new_instance['comments']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
			'title' => null,
			'numposts' => 5,
			'cat' => null,
			'comments' => 0
		);
		$instance = wp_parse_args((array) $instance, $defaults);
		$cats = get_categories();
		$all_cats = empty($instance['cat']) || !is_int($instance['cat']) ? ' selected="selected"' : '';
		
		$cat_options = '';
		$numposts_options = '';
		
		foreach ($cats as $category) {
			$selected = ($category->cat_ID == $instance['cat'] ? ' selected="selected"' : '');
			$cat_options .= "\t<option value=\"" . intval($category->cat_ID) . "\"" . $selected . ">"
				 . __(esc_attr($category->name)) . "</option>\n";
		}
		for ($i = 1; $i <= 20; $i++) {
			$selected_n = ($instance['numposts'] == $i ? ' selected="selected"' : '');
			$numposts_options .= "\t<option value=\"" . $i . "\"" . $selected_n . ">" . $i . "</option>\n";
		}
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php printf('%s', esc_attr((string)$instance['title'])); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Show posts from this category:'); ?></label>
			<select id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>" size="1">
				<option value="all"<?php echo $all_cats; ?>><?php _e('All recent posts'); ?></option>
				<?php echo $cat_options; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('numposts'); ?>"><?php _e('Number of posts to show:'); ?></label>
			<select id="<?php echo $this->get_field_id('numposts'); ?>" name="<?php echo $this->get_field_name('numposts'); ?>" size="1">
			<?php echo $numposts_options; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('comments'); ?>"><?php _e('Show number of comments? '); ?></label>
			<input type="checkbox" id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>" value="1" <?php checked($instance['comments'], 1); ?>/>
		</p>	
<?php
	}
}

$thesis_dashboard_news_box = new thesis_dashboard_rss;
class thesis_dashboard_rss {
	var $feed = 'http://diythemes.com/thesis/feed/';

	function __construct() {
		add_action('wp_dashboard_setup', array($this, 'add'));
	}

	function thesis_dashboard_rss() {
		$this->__construct();
	}

	function add() {
		add_meta_box('thesis_news_widget', __('The latest from the <strong>DIY</strong>themes Blog', 'thesis'), array($this, 'widget'), 'dashboard', 'normal', 'high');
	}

	function widget() {
		$rss = fetch_feed($this->feed);
		if (!is_wp_error($rss)) {
			$max_items = $rss->get_item_quantity(5);
			$rss_items = $rss->get_items(0, $max_items);
		}
		$out = "<div class=\"rss-widget rss-thesis\">\n\t<ul>\n";
		if (!empty($rss_items)) {
			$date_format = get_option('date_format');
			foreach ($rss_items as $item)
				$out .= "\t\t<li><a class=\"rsswidget\" href=\"" . esc_url($item->get_permalink()) . "\" title=\"" . esc_attr__($item->get_description(), 'thesis') . "\">" . esc_attr__($item->get_title(), 'thesis') . "</a> <span class=\"rss-date\">" . esc_attr__($item->get_date($date_format), 'thesis') . "</span></li>\n";
		}
		else
			$out .= "\t\t<li><a href=\"" . $this->feed . "\">" . __('Check out the <strong>DIY</strong>themes blog!') . "</a></li>\n";
		$out .= "\t</ul>\n</div>\n";
		echo $out;
	}
}

// upgrade the widgets
add_action('after_switch_theme', 'thesis_update_all_widgets');

function thesis_update_all_widgets() {
	$alloptions = wp_load_alloptions();
	if (! isset($alloptions['theme_mods_thesis_183']) || ! isset($alloptions['theme_mods_thesis_184']) || ! isset($alloptions['theme_mods_thesis_185'])) {
		$search = get_option('thesis_widget_search');
		$subs = get_option('thesis_widget_subscriptions');
		$google = get_option('thesis_widget_google_cse');
		$kre = get_option('widget_killer_recent_entries');

		foreach ($alloptions as $key => $val) {
			if (preg_match('/^theme_mods_thesis_(?P<version>[0-9b]+)$/', $key, $mods)) {
				if ($mods['version'] == '183' || $mods['version'] == '184' || $mods['version'] == '185') continue;
				else $tmod[] = $mods['version'];
			}
		}
		if ((! $kre && ! $google && ! $subs && ! $search) || ! $tmod) // there either are none or we have taken care of them
			return;
		else {
			sort($tmod);
			$option_to_grab = 'theme_mods_thesis_' . current($tmod);
			$actual_option = maybe_unserialize($alloptions[$option_to_grab]);
			$sidebars = $actual_option['sidebars_widgets']['data'];
			if (! empty($sidebars)) {
				foreach ($sidebars as $area => $current_widgets) {
					if (! is_array($current_widgets) || empty($current_widgets)) continue;

					foreach ($current_widgets as $position => $current_widget) {
						if (in_array($current_widget, array('search', 'subscriptions', 'google-custom-search')) || preg_match('/^widget_killer_recent_entries-([0-9]+)$/', $current_widget))
							$to_replace[$area][$position] = $current_widget;
					}
				}
			}

			if (! empty($to_replace)) {
				foreach ($to_replace as $where => $what) {
					foreach ($what as $what_position => $what_widget) {
						if ($what_widget == 'search') {
							$sidebars[$where][$what_position] = 'thesis-search-widget-2';
							$new_search_options = array(
								2 => array(
									'title' => $search['thesis-search-title'],
									'default_value' => 'To search, type and hit enter',
									'show_submit' => false,
									'submit_value' => 'Search'
								),
								'_multiwidget' => 1
							);
							update_option('widget_thesis-search-widget', $new_search_options);
							delete_option('thesis_widget_search');
						}
						elseif ($what_widget == 'subscriptions') {
							$sidebars[$where][$what_position] = 'thesis-subscriptions-2';
							$new_subs_options = array(
								2 => array(
									'title' => $subs['thesis-subscriptions-title'],
									'description' => $subs['thesis-subscriptions-description'],
									'rss_text' => $subs['thesis-subscriptions-rss-text'],
									'email' => $subs['thesis-subscriptions-email']
								),
								'_multiwidget' => 1
							);
							update_option('widget_thesis-subscriptions', $new_subs_options);
							delete_option('thesis_widget_subscriptions');
						}
						elseif ($what_widget == 'google-custom-search') {
							$sidebars[$where][$what_position] = 'thesis-google-cse-2';
							$new_cse_options = array(
								2 => array(
									'title' => $google['thesis-google-cse-title'],
									'code' => $google['thesis-google-cse-code']
								),
								'_multiwidget' => 1
							);
							update_option('widget_thesis-google-cse', $new_cse_options);
							delete_option('thesis_widget_google_cse');
						}
						elseif (preg_match('/^widget_killer_recent_entries-([0-9]+)$/', $what_widget, $kre_matches)) {
							$sidebars[$where][$what_position] = 'thesis-killer-recent-entries-' . $kre_matches[1];
							$cat_obj = $kre[$kre_matches[1]]['category'] !== 'all' ? get_category_by_slug($kre[$kre_matches[1]]['category']) : 'all';
							$new_kre_options[$kre_matches[1]] = array(
								'title' => $kre[$kre_matches[1]]['title'],
								'cat' => is_object($cat_obj) ? $cat_obj->term_id : $cat_obj,
								'numposts' => $kre[$kre_matches[1]]['numposts'],
								'comments' => $kre[$kre_matches[1]]['comments']
							);
						}
					}
				}
				if (! empty($new_kre_options)) {
					ksort($new_kre_options);
					$new_kre_options['_multiwidget'] = 1;
					update_option('widget_thesis-killer-recent-entries', $new_kre_options);
				}
				if ($kre) delete_option('widget_killer_recent_entries');
				update_option('sidebars_widgets', $sidebars);
				wp_cache_flush();
				thesis_generate_css();
			}
		}
	}
}
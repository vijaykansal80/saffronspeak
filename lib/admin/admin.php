<?php

function thesis_admin_setup() {
	global $thesis_terms;
	add_action('admin_menu', 'thesis_add_menu');
	add_action('admin_menu', array('thesis_post_options', 'add_meta_boxes'));
	add_action('admin_post_thesis_options', array('thesis_site_options', 'save_options'));
	add_action('admin_post_thesis_design_options', array('thesis_design_options', 'save_options'));
	add_action('admin_post_thesis_upgrade', 'thesis_upgrade');
	add_action('admin_post_thesis_file_editor', array('thesis_custom_editor', 'save_file'));
	$thesis_terms->actions();
	new thesis_dashboard_rss;
	add_action('init', 'thesis_options_head');
}

function thesis_multisite_admin() {
	$errors = thesis_multisite_structure();
	return ! empty($errors) ? implode(' ', $errors) : true;
}

function thesis_add_menu() {
	global $menu, $wp_version, $thesis_header, $thesis_favicon;
	if (version_compare($wp_version, '2.9', '>=')) 
		$menu[30] = array('', 'read', 'separator-thesis', '', 'wp-menu-separator');
	
	add_menu_page(__('Thesis', 'thesis'), __('Thesis', 'thesis'), 'edit_theme_options', 'thesis-options', array('thesis_site_options', 'options_page'), THESIS_IMAGES_FOLDER . '/icon-swatch.png', 31); #wp
	add_submenu_page('thesis-options', __('Site Options', 'thesis'), __('Site Options', 'thesis'), 'edit_theme_options', 'thesis-options', array('thesis_site_options', 'options_page')); #wp
	add_submenu_page('thesis-options', __('Design Options', 'thesis'), __('Design Options', 'thesis'), 'edit_theme_options', 'thesis-design-options', array('thesis_design_options', 'options_page')); #wp
	add_submenu_page('thesis-options', __('Header Image', 'thesis'), __('Header Image', 'thesis'), 'edit_theme_options', 'thesis-header-image', array($thesis_header, 'options_page')); #wp
	add_submenu_page('thesis-options', __('Favicon Uploader', 'thesis'), __('Favicon Uploader', 'thesis'), 'edit_theme_options', 'thesis-favicon', array($thesis_favicon, 'options_page')); #wp
	add_submenu_page('thesis-options', __('Custom File Editor', 'thesis'), __('Custom File Editor', 'thesis'), 'edit_theme_options', 'thesis-file-editor', array('thesis_custom_editor', 'options_page')); #wp
	add_submenu_page('thesis-options', __('Manage Options', 'thesis'), __('Manage Options', 'thesis'), 'edit_theme_options', 'options-manager', array('thesis_options_manager', 'options_page')); #wp
}

function thesis_options_head() {
	global $pagenow, $wp_version;
	if (! empty($_GET['page']) && in_array($_GET['page'], array('thesis-options', 'thesis-design-options', 'thesis-header-image', 'thesis-favicon', 'thesis-file-editor', 'options-manager')) || in_array($pagenow, array('post-new.php', 'post.php', 'edit-tags.php'))) {
		
		wp_enqueue_style('thesis-options-stylesheet', THESIS_CSS_FOLDER . '/options.css'); #wp

		if (! empty($_GET['page']) && $_GET['page'] == 'thesis-file-editor')
			wp_enqueue_script('color-picker', THESIS_SCRIPTS_FOLDER . '/jscolor/jscolor.js'); #wp
		elseif (! empty($_GET['page']) && $_GET['page'] == 'options-manager') {
			$manager = new thesis_options_manager;
			$manager->add_js();
			$manager->manage_options();
		}
		else {
			wp_enqueue_script('jquery-ui-core'); #wp
			wp_enqueue_script('jquery-ui-sortable'); #wp
			wp_enqueue_script('jquery-ui-tabs'); #wp
			wp_enqueue_script('thesis-admin-js', THESIS_SCRIPTS_FOLDER . '/thesis.js'); #wp

			if (! empty($_GET['page']) && $_GET['page'] == 'thesis-design-options')
				wp_enqueue_script('color-picker', THESIS_SCRIPTS_FOLDER . '/jscolor/jscolor.js'); #wp
		}
	}
}

/*---:[ random admin file functions that will probably have a new home at some point as Thesis grows ]:---*/

function thesis_version_indicator($depth = 1) {
	$indent = str_repeat("\t", $depth);
	echo "$indent<span id=\"thesis_version\">" . sprintf(__('You are rocking Thesis version <strong>%1$s</strong>', 'thesis'), thesis_version()) . "</span>\n";
}

function thesis_options_title($title, $switch = true, $depth = 1) {
	$indent = str_repeat("\t", $depth);
	$master_switch = ($switch) ? ' <a id="master_switch" href="" title="' . __('Big Ass Toggle Switch', 'thesis') . '"><span class="pos">+</span><span class="neg">&#8211;</span></a>' : '';
	echo "$indent<h2>$title$master_switch</h2>\n";
}

function thesis_options_nav($depth = 1) {
	$indent = str_repeat("\t", $depth);
	echo "$indent<ul id=\"thesis_links\">\n";
	echo "$indent\t<li><a href=\"http://diythemes.com/thesis/\" title=\"" . __('Thesis news plus tutorials and advice from Thesis pros!', 'thesis') . "\">" . __('Thesis Blog', 'thesis') . "</a></li>\n";
	echo "$indent\t<li><a href=\"http://diythemes.com/thesis/rtfm/\" title=\"" . __('Documentation, tutorials, and how-tos that will help you get the most out of Thesis', 'thesis') . "\">" . __('User&#8217;s Guide', 'thesis') . "</a></li>\n";
	echo "$indent\t<li><a href=\"http://diythemes.com/forums/\" title=\"" . __('Stuck? Don&#8217;t worry&#8212;you can find expert help in our support forums.', 'thesis') . "\">" . __('Support Forums', 'thesis') . "</a></li>\n";
	echo "$indent\t<li><a href=\"http://diythemes.com/affiliate-program/\" title=\"" . __('Join the Thesis Affiliate Program and earn money selling Thesis!', 'thesis') ."\">" . __('Affiliate Program', 'thesis') . "</a></li>\n";
	echo "$indent</ul>\n";
}

function thesis_options_status_check($depth = 1, $file = false) {
	$indent = str_repeat("\t", $depth);

	if (isset($_GET['updated'])) {
		$out = ! $file ?  __('Options updated!', 'thesis') :  __('File updated!', 'thesis');
		echo "$indent<div id=\"updated\" class=\"updated fade\">\n";
		echo "$indent\t<p>" . $out . ' <a href="' . get_bloginfo('url') . '/">' . __('Check out your site &rarr;', 'thesis') . "</a></p>\n";
		echo "$indent</div>\n";
	}
	elseif (isset($_GET['upgraded'])) {
		echo "$indent<div id=\"updated\" class=\"updated fade\">\n";
		echo "$indent\t<p>" . sprintf(__('Nicely done&#8212;Thesis <strong>%1$s</strong> is ready to rock. Take a moment to browse around the options panels and check out the new awesomeness, or simply <a href="%2$s">check out your site now</a>.', 'thesis'), thesis_version(), get_bloginfo('url') . '/') . "</p>\n";
		echo "$indent</div>\n";
	}
}

function thesis_is_css_writable() {
	global $blog_id;
	$ms_errors = array();
	if (is_multisite()) {
		$maybe_errors = thesis_multisite_admin();
		if (is_array($maybe_errors)) {
			foreach ($maybe_errors as $ms_error)
				$ms_errors[] = "<li>" . esc_html($ms_error) . "</li>";
		}
	}
	if (@file_exists(THESIS_CUSTOM)) {
		$location = '/' . basename(dirname(THESIS_CUSTOM)) . '/' . basename(THESIS_CUSTOM) . '/layout.css';
		$folder = false;
	}
	elseif (@file_exists(TEMPLATEPATH . '/custom-sample')) {
		$location = '/thesis/custom-sample/layout.css';
		$folder = "<div class=\"warning\">\n\t<p>" . __('<strong>Attention!</strong> In order to take advantage of all the controls that Thesis offers, you need to change the name of your <code>custom-sample</code> folder to <code>custom</code>.', 'thesis') . "</p>\n</div>\n";
	}

	if (@file_exists(THESIS_LAYOUT_CSS) && !is_writable(THESIS_LAYOUT_CSS))
		echo "<div class=\"warning\">\n\t<p><strong>" . __('Attention!', 'thesis') . '</strong> ' . sprintf(__('Your <code>%s</code> file is not writable by the server, and in order to work the full extent of its magic, Thesis needs to be able to write to this file. All you have to do is set your <code>layout.css</code> file permissions to 666, and you&#8217;ll be good to go. After setting your file permissions, you should head to the <a href="%s">Design Options</a> page and hit the save button.', 'thesis'), esc_html($location),  admin_url('admin.php?page=thesis-design-options')) . "</p>\n</div>\n";
	
	if ($folder) echo $folder;
	
	if(! empty($ms_errors))
		echo "<div class=\"warning\"><p><strong>" . esc_html__('Attention!', 'thesis') . "</strong> " . esc_html__('The following errors were generated while Thesis was making the files for your network site: ', 'thesis') . "</p><ul>" . implode(' ', $ms_errors) . "</ul></div>";
}

function thesis_save_button_text($return = false) {
	global $thesis_site, $thesis_data;
	$text = $thesis_site->save_button_text ? $thesis_data->o_texturize($thesis_site->save_button_text, true) : __('Big Ass Save Button', 'thesis');
	if (! $return)
		echo $text;
	else
		return $text;
		
}

function thesis_css_check() {
	global $thesis_site;
	$last_css_mod = get_option('thesis_css_mod_time');
	$css_file = @file_exists(THESIS_CUSTOM . '/custom.css') ? THESIS_CUSTOM . '/custom.css' : false;	
	$current = $css_file ? filemtime($css_file) : false;
		
	if ($css_file) {
		if ($last_css_mod === false) {
			update_option('thesis_css_mod_time', $current);
			wp_cache_flush();
		}
		elseif ($last_css_mod == $current)
			return;
		elseif (empty($thesis_site->custom{'design_mode'})) {
			$message = "<div class=\"warning\">\n\t<p>" . sprintf(__('Thesis detected that you modified your custom stylesheet, and it has recompiled your <abbr title="Cascading Style Sheet">CSS</abbr> to reflect these changes. If you&#8217;re going to be making custom stylesheet changes, try out Thesis&#8217; handy <a href="%1$s">custom <abbr title="Cascading Style Sheet">CSS</abbr> Design Mode</a> (under <abbr title="Cascading Style Sheet">CSS</abbr>/Stylesheet Options), which will allow your changes to appear without having to recompile the stylesheets.', 'thesis'), admin_url('admin.php?page=thesis-options#stylesheet-options')) . "</p>\n</div>\n";
			thesis_generate_css();
			update_option('thesis_css_mod_time', $current);
			wp_cache_flush();
			echo $message;
		}
	}
}
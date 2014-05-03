<?php
/**
 * class thesis_javascript
 *
 * @package Thesis
 * @since 1.7
 */
class thesis_javascript {
	static $libs = array(
		'jquery' => array(
			'name' => 'jQuery',
			'url' => 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
			'info_url' => 'http://jquery.com/'
		),
		'jquery_ui' => array(
			'name' => 'jQuery UI',
			'url' => 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js',
			'info_url' => 'http://jqueryui.com/'
		),
		'prototype' => array(
			'name' => 'Prototype',
			'url' => 'http://ajax.googleapis.com/ajax/libs/prototype/1.7.0.0/prototype.js',
			'info_url' => 'http://www.prototypejs.org/'
		),
		'scriptaculous' => array(
			'name' => 'script.aculo.us',
			'url' => 'http://ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js',
			'info_url' => 'http://script.aculo.us/'
		),
		'mootools' => array(
			'name' => 'MooTools',
			'url' => 'http://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js',
			'info_url' => 'http://mootools.net/'
		),
		'dojo' => array(
			'name' => 'Dojo',
			'url' => 'http://ajax.googleapis.com/ajax/libs/dojo/1.7.2/dojo/dojo.js',
			'info_url' => 'http://dojotoolkit.org/'
		),
		'yui' => array(
			'name' => 'Yahoo! User Interface (YUI)',
			'url' => 'http://ajax.googleapis.com/ajax/libs/yui/2.8.0r4/build/yuiloader/yuiloader-min.js',
			'info_url' => 'http://developer.yahoo.com/yui/'
		),
		'ext' => array(
			'name' => 'Ext Core',
			'url' => 'http://ajax.googleapis.com/ajax/libs/ext-core/3.1.0/ext-core.js',
			'info_url' => 'http://www.extjs.com/products/extcore/'
		),
		'chrome' => array(
			'name' => 'Chrome Frame',
			'url' => 'http://ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js',
			'info_url' => 'http://code.google.com/chrome/chromeframe/'
		)
	);
	
	public function __get($name) {
		if ($name == 'libs')
			return self::$libs;
	}

	public function output_scripts() {
		global $thesis_site, $thesis_design, $wp_query, $wp_scripts;
		$javascript = self::$libs;
		$to_check = array(
			'jquery', 'prototype', 'scriptaculous', 'jquery-ui-core'
		);
		
		$design_scripts = ($thesis_design->javascript['scripts']) ? $thesis_design->javascript['scripts'] . "\n" : '';
		$user_scripts = ($thesis_site->javascript['scripts']) ? $thesis_site->javascript['scripts'] : '';

		if ($wp_query->is_home || is_front_page()) {
			if (get_option('show_on_front') == 'page') $page_id = (is_front_page()) ? get_option('page_on_front') : get_option('page_for_posts');
			$libs = ! empty($page_id) ? get_post_meta($page_id, 'thesis_javascript_libs', true) : $thesis_design->home['javascript']['libs'];
			$page_scripts = ! empty($page_id) ? get_post_meta($page_id, 'thesis_javascript_scripts', true) : $thesis_design->home['javascript']['scripts'];
		}
		elseif ($wp_query->is_page || $wp_query->is_single) { #wp
			global $post; #wp
			$libs = get_post_meta($post->ID, 'thesis_javascript_libs', true);
			$page_scripts = get_post_meta($post->ID, 'thesis_javascript_scripts', true);
		}
		
		foreach ($to_check as $script_check) {
			if ($script_check == 'jquery-ui-core' && isset($wp_scripts->groups[$script_check]) && isset($libs['jquery_ui']))
				unset($libs['jquery_ui']);
			elseif (isset($wp_scripts->groups[$script_check]) && isset($libs[$script_check]))
				unset($libs[$script_check]);
		}

		if (is_array($thesis_design->javascript['libs'])) {
			foreach ($thesis_design->javascript['libs'] as $lib_name => $include) {
				if ((isset($libs[$lib_name]) && $libs[$lib_name]) || (!isset($libs[$lib_name]) && $include))
					$output[$lib_name] = '<script type="text/javascript" src="' . esc_url($javascript[$lib_name]['url']) . '"></script>';
			}
			if (! empty($output)) echo implode("\n", $output) . "\n";
		}

		$scripts = ! empty($page_scripts) ? "$design_scripts$page_scripts\n$user_scripts" : "$design_scripts$user_scripts";
		if ($scripts != '') echo stripslashes($scripts) . "\n";
	}
}
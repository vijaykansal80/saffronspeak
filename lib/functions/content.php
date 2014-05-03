<?php

function thesis_post_box($classes = '', $post_count = false) {
	$post_image = thesis_post_image_info('image');

	thesis_hook_before_post_box($post_count);
	echo "\t\t\t<div class=\"" . join(' ', get_post_class($classes)) . "\" id=\"post-" . get_the_ID() . "\">\n";
	thesis_hook_post_box_top($post_count);
	thesis_headline_area($post_count, $post_image);
	echo "\t\t\t\t<div class=\"format_text entry-content\">\n";
	thesis_post_content($post_count, $post_image);
	echo "\t\t\t\t</div>\n";
	thesis_hook_post_box_bottom($post_count);
	echo "\t\t\t</div>\n\n";
	thesis_hook_after_post_box($post_count);
}

function thesis_headline_area($post_count = false, $post_image = false) {
	if (apply_filters('thesis_show_headline_area', true)) {
?>
				<div class="headline_area">
<?php

	thesis_hook_before_headline($post_count);

	if ($post_image['show'] && $post_image['y'] == 'before-headline')
		echo $post_image['output'];

	if (is_404()) {
		echo "\t\t\t\t\t<h1>";
		thesis_hook_404_title();
		echo "</h1>\n";
	}
	elseif (is_page()) {
		echo (is_front_page()) ? "\t\t\t\t\t<h2>" . get_the_title() . "</h2>\n" : "\t\t\t\t\t<h1>" . get_the_title() . "</h1>\n";

		if ($post_image['show'] && $post_image['y'] == 'after-headline')
			echo $post_image['output'];

		thesis_hook_after_headline($post_count);
		thesis_byline();
	}
	else {
		if (is_single()) {
?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
<?php
		}
		else {
?>
					<h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
<?php
		}
		
		if ($post_image['show'] && $post_image['y'] == 'after-headline')
			echo $post_image['output'];

		thesis_hook_after_headline($post_count);
		thesis_byline($post_count);
		thesis_post_categories();
	}
?>
				</div>
<?php
	}
}

function thesis_show_byline() {
	global $thesis_design;

	if (is_page()) {
		if ($thesis_design->display['byline']['page']['author'] || $thesis_design->display['byline']['page']['date'] || ($thesis_design->display['byline']['num_comments']['show'] && comments_open() && !$thesis_design->display['comments']['disable_pages']))
			return true;
	}
	else {
		if ($thesis_design->display['byline']['author']['show'] || $thesis_design->display['byline']['date']['show'] || ($thesis_design->display['byline']['num_comments']['show'] && (comments_open() || get_comments_number() > 0)))
			return true;
	}
}

function thesis_byline($post_count = false) {
	global $thesis_design;

	if (thesis_show_byline()) {
		if (is_page()) {
			$author = $thesis_design->display['byline']['page']['author'];
			$date = $thesis_design->display['byline']['page']['date'];

			if (!$thesis_design->display['comments']['disable_pages'] && comments_open() && $thesis_design->display['byline']['num_comments']['show'])
				$show_comments = true;
		}
		else {
			$author = $thesis_design->display['byline']['author']['show'];
			$date = $thesis_design->display['byline']['date']['show'];

			if ($thesis_design->display['byline']['num_comments']['show'] && (comments_open() || get_comments_number() > 0))
				$show_comments = true;
		}
	}
	elseif ($thesis_design->display['admin']['edit_post'] && is_user_logged_in())
		$edit_link = true;
	elseif (! empty($_GET['template']))
		$author = $date = true;

	if (! empty($author) || ! empty($date) || ! empty($show_comments) || ! empty($edit_link)) {
		echo "\t\t\t\t\t<p class=\"headline_meta\">";

		if (! empty($author))
			thesis_author();

		if (! empty($author) && ! empty($date))
			echo ' ' . __('on', 'thesis') . ' ';

		if (! empty($date))
			echo '<abbr class="published" title="' . get_the_time('Y-m-d') . '">' . get_the_time(get_option('date_format')) . '</abbr>';
		
		if (! empty($show_comments)) {
			if ($author || $date)
				$sep = ' &middot; ';

			echo $sep . '<span><a href="' . get_permalink() . '#comments" rel="nofollow">';
			comments_number(__('0 comments', 'thesis'), __('1 comment', 'thesis'), __('% comments', 'thesis'));
			echo '</a></span>';
		}

		thesis_hook_byline_item($post_count);

		if ((! empty($author) || ! empty($author) || ! empty($author)) && $thesis_design->display['admin']['edit_post'])
			edit_post_link(__('edit', 'thesis'), '<span class="edit_post pad_left">[', ']</span>');
		elseif ($thesis_design->display['admin']['edit_post'])
			edit_post_link(__('edit', 'thesis'), '<span class="edit_post">[', ']</span>');

		echo "</p>\n";
	}
}

function thesis_author() {
	global $thesis_design;

	if ($thesis_design->display['byline']['author']['link']) {
		if ($thesis_design->display['byline']['author']['nofollow'])
			$nofollow = ' rel="nofollow"';

		$author = '<a href="' . get_author_posts_url(get_the_author_ID()) . '" class="url fn"' . $nofollow .'>' . get_the_author() . '</a>';
	}
	else
		$author = "<span class=\"fn\">" . get_the_author() . "</span>";

	echo __('by', 'thesis') . " <span class=\"author vcard\">$author</span>";
}

function thesis_post_categories() {
	global $thesis_design;

	if ($thesis_design->display['byline']['categories']['show'] && get_the_category())
		echo "\t\t\t\t\t<p class=\"headline_meta\">" . __('in', 'thesis') . ' <span>' . get_the_category_list(', ') . "</span></p>\n";
}

function thesis_post_tags() {
	global $thesis_design;

	if ((is_single() && $thesis_design->display['tags']['single']) || (!is_single() && $thesis_design->display['tags']['index'])) {
		$post_tags = get_the_tags();

		if ($post_tags) {
			echo "\t\t\t\t\t<p class=\"post_tags\">" . __('Tagged as:', 'thesis') . "\n";
			$num_tags = count($post_tags);
			$tag_count = 1;

			if ($thesis_design->display['tags']['nofollow'])
				$nofollow = ' nofollow';

			foreach ($post_tags as $tag) {			
				$html_before = "\t\t\t\t\t\t<a href=\"" . get_tag_link($tag->term_id) . "\" rel=\"tag$nofollow\">";
				$html_after = '</a>';
				
				if ($tag_count < $num_tags)
					$sep = ", \n";
				elseif ($tag_count == $num_tags)
					$sep = "\n";
				
				echo $html_before . $tag->name . $html_after . $sep;
				$tag_count++;
			}
			
			echo "\t\t\t\t\t</p>\n";
		}
	}
}

function thesis_post_content($post_count = false, $post_image = false) {
	global $wp_query, $post, $thesis_design;

	thesis_hook_before_post($post_count);

	if ($post_image['show'] && $post_image['y'] == 'before-post')
		echo $post_image['output'];

	if ($wp_query->is_page && (get_post_meta($post->ID, '_wp_page_template', true) == 'archives.php'))
		thesis_hook_archives_template();
	elseif ((($wp_query->is_home || $wp_query->is_archive || $wp_query->is_search) && $thesis_design->display['posts']['excerpts']) || (($wp_query->is_archive || $wp_query->is_search) && $thesis_design->display['archives']['style'] == 'excerpts'))
		the_excerpt();
	else
		the_content(thesis_read_more_text());

	if ($wp_query->is_single || $wp_query->is_page) {
		$lp_args = array(
			'before' => '<p><strong>Pages:</strong> ',
			'after' => '</p>',
			'next_or_number' => 'number'
		);
		wp_link_pages($lp_args);
	}

	thesis_hook_after_post($post_count);
}

function thesis_read_more_text($entities = false) {
	global $thesis_design, $thesis_data;
	$custom_read_more = strip_tags(stripslashes(get_post_meta(get_the_ID(), 'thesis_readmore', true)));
	$read_more = ($custom_read_more != '') ? $custom_read_more : (($thesis_design->display['posts']['read_more_text']) ? urldecode($thesis_design->display['posts']['read_more_text']) : __('[click to continue&hellip;]', 'thesis'));
	return ($entities) ? $thesis_data->o_htmlspecialchars($read_more) : $thesis_data->o_texturize($read_more);
}

function thesis_post_navigation() {
	global $wp_query;

	if ($wp_query->is_home || $wp_query->is_archive || $wp_query->is_search) {
		if ($wp_query->max_num_pages > 1) {
			$previous = apply_filters('thesis_previous', __('Previous Entries', 'thesis'));
			$next = apply_filters('thesis_next', __('Next Entries', 'thesis'));
			echo "\t\t\t<div class=\"prev_next\">\n";

			if ($wp_query->query_vars['paged'] <= 1) {
				echo "\t\t\t\t<p class=\"previous\">";
				next_posts_link($previous);
				echo "</p>\n";
			}
			elseif ($wp_query->query_vars['paged'] < $wp_query->max_num_pages) {
				echo "\t\t\t\t<p class=\"previous floated\">";
				next_posts_link($previous);
				echo "</p>\n";

				echo "\t\t\t\t<p class=\"next\">";
				previous_posts_link($next);
				echo "</p>\n";
			}
			elseif ($wp_query->query_vars['paged'] >= $wp_query->max_num_pages) {
				echo "\t\t\t\t<p class=\"next\">";
				previous_posts_link($next);
				echo "</p>\n";
			}
		
			echo "\t\t\t</div>\n\n";
		}
	}
}

function default_skin_previous($previous) {
	return "&larr; $previous";
}

function default_skin_next($next) {
	return "$next &rarr;";
}

function thesis_prev_next_posts() {
	global $thesis_design;

	if (is_single() && $thesis_design->display['posts']['nav']) {
		$previous = get_previous_post();
		$next = get_next_post();
		$previous_text = apply_filters('thesis_previous_post', __('Previous post: ', 'thesis')); #filter
		$next_text = apply_filters('thesis_next_post', __('Next post: ', 'thesis')); #filter

		if ($previous || $next) {
			echo "\t\t\t\t\t<div class=\"prev_next post_nav\">\n";

			if ($previous) {
				if ($previous && $next)
					$add_class = ' class="previous"';
				else $add_class = '';

				echo "\t\t\t\t\t\t<p$add_class>$previous_text";
				previous_post_link('%link', '%title');
				echo "</p>\n";
			}

			if ($next) {
				echo "\t\t\t\t\t\t<p>$next_text";
				next_post_link('%link', '%title');
				echo "</p>\n";
			}

			echo "\t\t\t\t\t</div>\n";
		}
	}
}

function thesis_archive_intro($depth = 3) {
	global $thesis_terms, $wp_query; #wp
	$tab = str_repeat("\t", $depth);
	$wp_query->get_queried_object();
	$output = "$tab<div id=\"archive_intro\">\n";
	
	if ($wp_query->is_category || $wp_query->is_tax || $wp_query->is_tag) { #wp
		$headline = trim(wptexturize(!empty($thesis_terms->terms[$wp_query->queried_object->taxonomy][$wp_query->queried_object->term_id]['headline']) ? stripslashes($thesis_terms->terms[$wp_query->queried_object->taxonomy][$wp_query->queried_object->term_id]['headline']) : $wp_query->queried_object->name)); #wp
		$output .= "$tab\t<h1>" . apply_filters('thesis_archive_intro_headline', $headline) . "</h1>\n"; #filter
		if (! empty($thesis_terms->terms) && $thesis_terms->terms[$wp_query->queried_object->taxonomy][$wp_query->queried_object->term_id]['content'])
			$output .= "$tab\t<div class=\"format_text\">\n" . apply_filters('thesis_archive_intro_content', $thesis_terms->terms[$wp_query->queried_object->taxonomy][$wp_query->queried_object->term_id]['content']) . "$tab\t</div>\n"; #filter
	}
	elseif ($wp_query->is_author) #wp
		$output .= "$tab\t<h1>" . apply_filters('thesis_archive_intro_headline', get_author_name($wp_query->query_vars['author'])) . "</h1>\n"; #wp
	elseif ($wp_query->is_day) #wp
		$output .= "$tab\t<h1>" . apply_filters('thesis_archive_intro_headline', get_the_time('l, F j, Y')) . "</h1>\n"; #wp
	elseif ($wp_query->is_month) #wp
		$output .= "$tab\t<h1>" . apply_filters('thesis_archive_intro_headline', get_the_time('F Y')) . "</h1>\n"; #wp
	elseif ($wp_query->is_year) #wp
		$output .= "$tab\t<h1>" . apply_filters('thesis_archive_intro_headline', get_the_time('Y')) . "</h1>\n"; #wp
	elseif ($wp_query->is_search) #wp
		$output .= "$tab\t<h1>" . __('Search:', 'thesis') . ' ' . apply_filters('thesis_archive_intro_headline', esc_html($wp_query->query_vars['s'])) . "</h1>\n"; #wp

	$output .= "$tab</div>\n";
	echo apply_filters('thesis_archive_intro', $output);
}
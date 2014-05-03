<?php
/**
 * Outputs the Thesis Custom File Editor
 *
 * @package Thesis
 * @since 1.6
 */
class thesis_custom_editor {
	
	public function get_custom_files() {
		$files = array();
		$ext = implode('|', array('css', 'html', 'htm', 'js', 'php'));
		$contents = scandir(THESIS_CUSTOM);
		$not = array('.', '..', 'layout.css');
		foreach ($contents as $file) {
			if (in_array($file, $not) || is_dir($file) || preg_match('/\.(jpg|jpeg|gif|png|ico|tiff|bmp)/i', $file)) continue;
			elseif (is_multisite() && ! is_super_admin() && current_user_can('edit_theme_options')) {
				$files = array('custom.css');
				$this->custom_css_only = true;
			}
			elseif (current_user_can('edit_themes') && preg_match("/\.($ext)$/i", $file))
				$files[] = $file;
		}
		$this->files = $files; // files that can be edited
		$this->basename = basename(THESIS_CUSTOM);
	}

	public function find_errors() {
		$error = '';
		
		if (! is_writable(THESIS_CUSTOM . '/custom.css')) // this is the main poppa, so we def wanna warn about this!
			$error .= "<p><strong>" . esc_html__('Attention!', 'thesis') . '</strong> ' . sprintf(__('Your <code>/%s/%s</code> file is not writable by the server, and in order to modify the file via the admin panel, Thesis needs to be able to write to this file. All you have to do is set this file&#8217;s permissions to 666, and you&#8217;ll be good to go.', 'thesis'), $this->basename, esc_html($this->current_file)) . '</p>';
			
		if ($this->current_file !== 'custom.css' && (! @file_exists($this->current_path) || ! is_writable($this->current_path)))
			$error .= "<p><strong>" . esc_html__('Attention!', 'thesis') . '</strong> ' . esc_html__('The file you are attempting does not appear to exist or is not editable.', 'thesis') . '</p>';
		
		$this->errors = ! empty($error) ? true : false;
		
		echo ! empty($error) ? "<div class=\"warning\">\n\t$error\n</div>\n" : '';
	}
	
	public function get_contents() {
		if (! $this->errors || (! @file_exists($this->current_path) && ! is_writable($this->current_path)))
			$this->current_contents = file_get_contents($this->current_path);
		else
			$this->current_contents = '';
	}

	public function save_file() {
		global $thesis_site;
		if (!current_user_can('edit_theme_options') || (is_multisite() && ! is_super_admin() && preg_match('/\.(php|js|html|htm)/i', $_POST['file'])))
			wp_die(__('Easy there, homey. You don&#8217;t have admin privileges to access theme options.', 'thesis'));
		
		$editor = new thesis_custom_editor;
		$editor->get_custom_files();

		if (isset($_POST['custom_file_submit'])) {
			check_admin_referer('thesis-custom-file', '_wpnonce-thesis-custom-file');
			$contents = stripslashes_deep($_POST['newcontent']); // Get new custom content
			$file = $_POST['file']; // Which file?
			if (!in_array($file, $editor->files)) // Is the file allowed? If not, get outta here!
				wp_die(__('You have attempted to modify an ineligible file. Only files within the Thesis <code>/custom</code> folder may be modified via this interface. Thank you.', 'thesis'));
				
			$write = file_put_contents(THESIS_CUSTOM . "/$file", $contents, LOCK_EX);
			
			if ($write)
				$updated = '&updated=true'; // Display updated message
			
			if (! empty($thesis_site->custom{'design_mode'}) && ! (bool) $thesis_site->custom{'design_mode'})
				thesis_generate_css();
		}
		elseif (isset($_POST['custom_file_jump'])) {
			check_admin_referer('thesis-custom-file-jump', '_wpnonce-thesis-custom-file-jump');
			$file = $_POST['custom_files'];
			if (!in_array($file, $editor->files))
				wp_die(__('You have attempted to modify an ineligible file. Only files within the Thesis <code>/custom</code> folder may be modified via this interface. Thank you.', 'thesis'));
			$updated = '';
		}

		wp_redirect(admin_url("admin.php?page=thesis-file-editor$updated&file=$file"));
		exit;
	}

	public function options_page() {
		global $thesis_site;
		
		echo "<div id=\"thesis_options\" class=\"wrap" . (get_bloginfo('text_direction') == 'rtl' ? ' rtl' : '') . "\">\n";
		thesis_version_indicator();
		thesis_options_title(__('Thesis Custom File Editor', 'thesis'), false);
		thesis_options_nav();
		thesis_options_status_check(1, true);
	
		if (version_compare($thesis_site->version, thesis_version()) < 0) {
?>
	<form id="upgrade_needed" action="<?php echo admin_url('admin-post.php?action=thesis_upgrade'); ?>" method="post">
		<h3><?php esc_html_e('Oooh, Exciting!', 'thesis'); ?></h3>
		<p><?php esc_html_e('It\'s time to upgrade your Thesis, which means there\'s new awesomeness in your immediate future. Click the button below to fast-track your way to the awesomeness!', 'thesis'); ?></p>
		<p><input type="submit" class="upgrade_button" id="teh_upgrade" name="upgrade" value="<?php _e('Upgrade Thesis', 'thesis'); ?>" /></p>
	</form>
<?php
	}
	elseif (@file_exists(THESIS_CUSTOM)) {
		$editor = new thesis_custom_editor;
		$editor->custom_css_only = false;
		$editor->get_custom_files();
		$editor->current_file = ! empty($_GET['file']) && in_array($_GET['file'], $editor->files) ? $_GET['file'] : 'custom.css';
		$editor->current_path = THESIS_CUSTOM . "/{$editor->current_file}";
		
		$editor->find_errors(); // echos the errors
		$editor->get_contents(); // sets up $this->current_contents

		$out = "<div class=\"one_col\">\n";
		if (current_user_can('edit_themes')) {
			 $out .= "\t<form method=\"post\" id=\"file-jump\" name=\"file-jump\" action=\"" . admin_url('admin-post.php?action=thesis_file_editor') . "\">\n" .
					"\t\t<h3>" . sprintf(__('Currently editing: <code>%s</code>', 'thesis'), esc_html("/{$editor->basename}/{$editor->current_file}")) . "</h3>\n";
			$out .= "\t\t<p>\n\t\t\t<select id=\"custom_files\" name=\"custom_files\">\n" .
					"\t\t\t\t<option value=\"" . esc_html($editor->current_file) . "\">" . esc_html($editor->current_file) . "</option>\n";
			foreach ($editor->files as $f)
				if ($f != $editor->current_file) $out .= "\t\t\t\t\t<option value=\"" . esc_html($f) . "\">" . esc_html($f) . "</option>\n";
			$out .= "\t\t\t</select>\n" . wp_nonce_field('thesis-custom-file-jump', '_wpnonce-thesis-custom-file-jump', true, false) .
					"<input type=\"submit\" id=\"custom_file_jump\" name=\"custom_file_jump\" value=\"" . __('Edit selected file', 'thesis') . "\" />\n\t\t</p>";
						
			if (strpos($editor->current_file, '.php'))
				$out .= "\t\t\t<p class=\"alert\">" . __('<strong>Note:</strong> If you make a mistake in your code while modifying a <acronym title="PHP: Hypertext Preprocessor">PHP</acronym> file, saving this page <em>may</em> result your site becoming temporarily unusable. Prior to editing such files, be sure to have access to the file via <acronym title="File Transfer Protocol">FTP</acronym> or other means so that you can correct the error.', 'thesis') . "</p>\n";
			$out .= "\t</form>\n";
		}		
		
		$out .= "<form class=\"file_editor\" method=\"post\" id=\"template\" name=\"template\" action=\"" . admin_url('admin-post.php?action=thesis_file_editor') . "\">\n"
			 .	"<input type=\"hidden\" id=\"file\" name=\"file\" value=\"" . esc_html($editor->current_file) . "\" />\n"
			 .	"<p><textarea id=\"newcontent\" name=\"newcontent\" rows=\"25\" cols=\"50\" class=\"large-text\">" . esc_textarea($editor->current_contents) . "</textarea></p>\n"
			 . "<p>\n" . wp_nonce_field('thesis-custom-file', '_wpnonce-thesis-custom-file', true, false)
			 . "<input type=\"submit\" class=\"save_button\" id=\"custom_file_submit\" name=\"custom_file_submit\" value=\"" . thesis_save_button_text(true) . "\" />\n"
			 . "<input class=\"color\" type=\"text\" id=\"handy-color-picker\" name=\"handy-color-picker\" value=\"ffffff\" maxlength=\"6\" />\n"
			 . "<label class=\"inline\" for=\"handy-color-picker\">" . __('quick color reference', 'thesis') . "</label>\n"
			 . "</p>\n</form>\n</div>\n";
	}
	else
		$out = "<div class=\"warning\">\n\t<p><strong>" . __('Attention!', 'thesis') . '</strong> ' . __('In order to edit your custom files, you&#8217;ll need to change the name of your <code>custom-sample</code> folder to <code>custom</code>.', 'thesis') . "</p>\n</div>\n";
	echo $out . "</div>\n";
	}
}
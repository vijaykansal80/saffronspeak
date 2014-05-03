<?php

$thesis_favicon = new thesis_favicon;

class thesis_favicon {
	function thesis_favicon() {
		$saved_favicon = get_option('thesis_favicon'); #wp
		if (!empty($saved_favicon))
			$this->favicon = $saved_favicon;
	}

	function __construct() {
		global $pagenow;
		$saved_favicon = get_option('thesis_favicon'); #wp
		if (!empty($saved_favicon))
			$this->favicon = $saved_favicon;
		if ($pagenow == 'admin.php' && $_GET['page'] == 'thesis-favicon') {
			add_filter('plupload_init', array(&$this, 'plupload_filter'));
			add_action('admin_init', array(&$this, 'process'));
			if (! empty($_GET['removed']) && $_GET['removed'] === 'true')
				$this->removed = true;
		}
		if (! empty($_POST['thesis_favicon']) && $_POST['thesis_favicon'] == 'ajaxd')
			$this->handle_ajax();
	}

	
	public function plupload_filter($options) {
		$options['url'] = admin_url('admin.php?page=thesis-favicon');
		$options['multipart_params']['post_id'] = 0;
		$options['multipart_params']['_wpnonce'] = wp_create_nonce(md5('thesis-favicon-plupload'));
		$options['multipart_params']['thesis_favicon'] = 'ajaxd';
		return $options;
	}
	
	function process() {
		if (isset($_POST['upload'])) {
			check_admin_referer('thesis-favicon-upload', '_wpnonce-thesis-favicon-upload'); #wp
			$overrides = array('test_form' => false);
			$file = wp_handle_upload($_FILES['import'], $overrides); #wp

			if (isset($file['error']))
				wp_die($file['error'], __('Favicon Upload Error', 'thesis')); #wp
			if ($file['type'] == 'image/x-icon' || $file['type'] == 'image/png') {
				$this->url = $file['url'];
				$this->save($file['file']);
			}
			else
				$this->error = true; 
		}
		elseif (! empty($_GET['remove'])) {
			check_admin_referer('thesis-remove-favicon'); #wp
			unset($this->favicon);
			delete_option('thesis_favicon'); #wp
			wp_cache_flush();
			wp_redirect(admin_url('admin.php?page=thesis-favicon&removed=true'));
			exit();
		}
	}
	
	public function handle_ajax() {
		define('DOING_AJAX', true);
		@header('X-Content-Type-Options: nosniff');
		add_action('admin_init', array(&$this, 'ajax_upload'));
	}
	
	public function ajax_upload() {
		if (! empty($_FILES) && $_FILES['async-upload']['size'] > 0 && wp_verify_nonce($_POST['_wpnonce'], md5('thesis-favicon-plupload')) && ($_FILES['async-upload']['type'] == 'image/png' || $_FILES['async-upload']['type'] == 'image/x-icon') && preg_match('/\.(ico|png)/i', $_FILES['async-upload']['name'])) {
			$upload = wp_handle_upload($_FILES['async-upload'], array('test_form' => false));
			if (! empty($upload['error']))
				wp_die($upload['error']);
			if ($upload['type'] == 'image/x-icon' || $upload['type'] == 'image/png') {
				update_option('thesis_favicon', esc_url_raw($upload['url']));
				wp_cache_flush();
			}
				
			echo "<p style=\"margin:9px 0;text-align:center;\"><img src=\"" . esc_url($upload['url']) . "\" /> " . esc_html__('Ravishing success! Your new favicon was uploaded and is ready to rock.', 'thesis') . "</p>";
			echo "<script type=\"text/javascript\">
				jQuery(function($){
					$('#favicon img').attr('src', '" . esc_url($upload['url']) . "');
					$('.progress').fadeOut(200);
				});
			</script>";
			die();
		}
		die('Hmm. Something went wrong.');
	}

	function save($image) {
		if (!$image) return;
		$this->favicon = esc_url_raw($this->url); #wp
		update_option('thesis_favicon', $this->favicon); #wp
		wp_cache_flush();
		$this->updated = true;
	}
	
	public function scripts() {
		global $wp_version;
		if (version_compare($wp_version, '3.3', '>=')) {
			wp_enqueue_script('plupload-handlers');
			wp_enqueue_script('image-edit');
			wp_enqueue_script('set-post-thumbnail' );
			wp_enqueue_style('imgareaselect');
		}
	}

	function options_page() {
		global $wp_version;
		
		$this->scripts(); // queues the scripts we need for plupload
		
		$rtl = (get_bloginfo('text_direction') == 'rtl') ? ' rtl' : ''; #wp
		echo "<div id=\"thesis_options\" class=\"wrap$rtl\">\n";
		thesis_version_indicator();
		thesis_options_title(__('Thesis Favicon Uploader', 'thesis'), false);
		thesis_options_nav();
		
		if (! empty($this->updated))
			echo "<div id=\"updated\" class=\"updated fade\">\n\t<p>" . __('Favicon updated!', 'thesis') . ' <a href="' . get_bloginfo('url') . '/">' . __('Check out your site &rarr;', 'thesis') . "</a></p>\n</div>\n"; #wp
		elseif (! empty($this->removed))
			echo "<div id=\"updated\" class=\"updated fade\">\n\t<p>" . __('Favicon removed!', 'thesis') . ' <a href="' . get_bloginfo('url') . '/">' . __('Check out your site &rarr;', 'thesis') . "</a></p>\n</div>\n"; #wp
		elseif (! empty($this->error))
			echo "<div class=\"warning\"><p>" . __('<strong>Whoops!</strong> Your favicon was not saved because you attempted to upload an improper file type. Thesis will accept favicons with a <code>.ico</code> or <code>.png</code> extension.', 'thesis') . "</p></div>\n";
		
		if (is_multisite() && ! current_user_can('unfiltered_upload'))
			echo "<div class=\"warning\"><p>" . __('Because you are using WordPress Multisite, you may only use <strong>.png</strong> images for your favicon. Images ending in <strong>.ico</strong> will result in an error. Please contact your system administrator if you have any questions.', 'thesis') . "</p></div>\n";
		
		echo "\t<div class=\"one_col\">\n\t\t<div class=\"control_area\">\n";
		if (! empty($this->favicon))
					echo "<p id=\"favicon\">\n\t<img src=\"" . esc_url($this->favicon) . "\" width=\"16\" height=\"16\" alt=\"favicon preview\" title=\"favicon preview\" />\n\t" . __('&larr; That&#8217;s your favicon.', 'thesis') . " <a href=\"" . wp_nonce_url(admin_url('admin.php?page=thesis-favicon&remove=true'), 'thesis-remove-favicon') . "\" title=\"" . __('Click here to remove favicon', 'thesis') . "\">" . __('Click here to remove it.', 'thesis') . "</a>\n</p>\n"; #wp
		
		if (version_compare($wp_version, '3.3', '>=')) {
			echo "<form id=\"thesis-favicon-form\" enctype=\"multipart/form-data\" method=\"post\" action=\"" . admin_url("admin.php?page=thesis-favicon") . "\">";
			media_upload_form();
			echo "<script type=\"text/javascript\">
			jQuery(function($){
				updateMediaForm();
				post_id = 0;
				shortform = 1;
			});
			jQuery('.drag-drop-info').text('Drop favicon here');
			jQuery('#plupload-browse-button').attr('value', 'Select favicon');
			jQuery('#plupload-upload-ui .upload-flash-bypass').text('');
			jQuery('#plupload-upload-ui .upload-flash-bypass').prepend('You are using the snazzy new uploader. Problems? Try the <a href=\"#\">browser uploader</a> instead.');
			jQuery('.upload-flash-bypass a').click(function(){
				jQuery('#drag-drop-area').hide();
				jQuery('.hide-old-form').show();
				jQuery(this).parent().hide();
			});
			</script>";
			echo "</form>\n<div id=\"media-items\" class=\"hide-if-no-js\"></div>";
			$hide_old = ' class="hide-old-form" style="display:none;"';
			$snazzy = '<p class="display-snazzy">' . __('Switch over to the <a href="#">snazzy new uploader</a>.', 'thesis') . '</p>';
		}
		else
			$hide_old = $snazzy = '';
?>
			<form<?php echo $hide_old; ?> enctype="multipart/form-data" id="upload-form" method="post" action="<?php echo admin_url('admin.php?page=thesis-favicon'); ?>">
				<p class="remove_bottom_margin">
					<label for="upload"><?php _e('Choose a <code>.ico</code> or <code>.png</code> image file with a square aspect ratio from your computer:', 'thesis'); ?></label>
					<input type="file" class="text" id="upload" name="import" />
					<?php wp_nonce_field('thesis-favicon-upload', '_wpnonce-thesis-favicon-upload'); ?>
					<input type="submit" class="ui_button positive" name="upload" value="<?php esc_attr_e('Upload', 'thesis'); ?>" />
				</p>
				<?php echo $snazzy; ?>
				<script type="text/javascript">
				jQuery('.display-snazzy a').click(function(){
					jQuery('.hide-old-form').hide();
					jQuery('#drag-drop-area').show();
					jQuery('.upload-flash-bypass').show();
				});
				</script>
			</form>
<?php
		echo "\t\t</div>\n\t</div>\n</div>\n";
	}
}
<?php

$thesis_header = new thesis_header_image;

class thesis_header_image {	
	function thesis_header_image() {
		$saved_header = maybe_unserialize(get_option('thesis_header')); #wp
		if (!empty($saved_header) && is_array($saved_header))
			$this->header = $saved_header;
	}

	function __construct() {
		global $pagenow;
		if ($pagenow == 'admin.php' && (! empty($_GET['page']) && $_GET['page'] == 'thesis-header-image')) {
			if (! empty($_POST['thesis_header_image']) && $_POST['thesis_header_image'] == 'ajaxd')
				$this->handle_ajax();
			add_filter('plupload_init', array(&$this, 'plupload_filter'));
			add_action('admin_init', array(&$this, 'process'), 9);
			if (! empty($_GET['removed']) && $_GET['removed'] === 'true')
				$this->removed = true;
			$this->do_crop = function_exists('imagecreatefromstring') ? true : false;
		}
		$saved_header = maybe_unserialize(get_option('thesis_header')); #wp
		if (!empty($saved_header) && is_array($saved_header))
			$this->header = $saved_header;
	}
	
	public function plupload_filter($options) {
		$options['url'] = admin_url('admin.php?page=thesis-header-image');
		$options['multipart_params']['post_id'] = 0;
		$options['multipart_params']['_wpnonce'] = wp_create_nonce(md5('thesis-header-image-plupload'));
		$options['multipart_params']['thesis_header_image'] = 'ajaxd';
		return $options;
	}

	function process() {
		$css = new Thesis_CSS;
		$css->baselines();
		$css->widths();
		$this->optimal_width = $css->widths['container'] - ($css->base['page_padding'] * 2);
		
		if (isset($_POST['upload']) || (! empty($_POST['thesis_header_image']) && $_POST['thesis_header_image'] == 'ajaxd')) {
			if (isset($_POST['upload']))
				check_admin_referer('thesis-header-upload', '_wpnonce-thesis-header-upload'); #wp
			elseif (! wp_verify_nonce($_POST['_wpnonce'], md5('thesis-header-image-plupload')))
				die('No permission to do this.');
				
			$overrides = array('test_form' => false);
			$file = defined('DOING_AJAX') ? wp_handle_upload($_FILES['async-upload'], $overrides) : wp_handle_upload($_FILES['import'], $overrides); #wp
			$this->file = $file;
			
			if (! defined('DOING_AJAX') && !empty($file['error']))
				wp_die($file['error'], __('Image Upload Error', 'thesis')); #wp
			elseif ((defined('DOING_AJAX') && DOING_AJAX) && ! empty($file['error']))
				die($file['error']);
				
			if ($file['type'] == 'image/jpeg' || $file['type'] == 'image/pjpeg' || $file['type'] == 'image/png' || $file['type'] == 'image/gif') {
				$this->url = $file['url'];
				$image = $file['file'];
				list($this->width, $this->height) = @getimagesize($image);

				if ($this->width <= $this->optimal_width)
					$this->save($image);
				elseif ($this->width > $this->optimal_width) {
					if (apply_filters('thesis_crop_header', true) && $this->do_crop) { #filter
						$this->ratio = $this->width / $this->optimal_width;
						$cropped = wp_crop_image($image, 0, 0, $this->width, $this->height, $this->optimal_width, $this->height / $this->ratio, false, str_replace(basename($image), 'cropped-' . basename($image), $image)); #wp
						if (! defined('DOING_AJAX') && is_wp_error($cropped)) #wp
							wp_die(__('Your image could not be processed. Please go back and try again.', 'thesis'), __('Image Processing Error', 'thesis')); #wp
						elseif (defined('DOING_AJAX') && is_wp_error($cropped))
							die(__LINE__);
						
						$this->url = str_replace(basename($this->url), basename($cropped), $this->url);
						$this->width = round($this->width / $this->ratio);
						$this->height = round($this->height / $this->ratio);
						$this->save($cropped);
						@unlink($image);
					}
					else
						$this->save($image);
				}
			}
			else
				$this->error = true;
		}
		elseif (! empty($_GET['remove'])) {
			check_admin_referer('thesis-remove-header'); #wp
			unset($this->header);
			delete_option('thesis_header'); #wp
			global $thesis_design;
			if (!$thesis_design->display['header']['tagline'] && apply_filters('thesis_header_auto_tagline', true)) { #filter
				$thesis_design->display['header']['tagline'] = true;
				update_option('thesis_design_options', $thesis_design); #wp
			}
			wp_cache_flush();
			thesis_generate_css();
			wp_redirect(admin_url('admin.php?page=thesis-header-image&removed=true'));
			exit;
		}
	}

	function save($image) {
		if (!$image) return;
		global $thesis_design;
		$this->header = array('url' => esc_url_raw($this->url), 'width' => $this->width, 'height' => $this->height); #wp
		update_option('thesis_header', $this->header); #wp
		if ($thesis_design->display['header']['tagline'] && apply_filters('thesis_header_auto_tagline', true)) { #filter
			$thesis_design->display['header']['tagline'] = false;
			update_option('thesis_design_options', $thesis_design); #wp
		}
		wp_cache_flush();
		thesis_generate_css();
		if (defined('DOING_AJAX')) $this->ajax_return();
		else wp_redirect(admin_url('admin.php?page=thesis-header-image&updated=true'));
	}
	
	public function handle_ajax() {
		define('DOING_AJAX', true);
		@header('X-Content-Type-Options: nosniff');
	}
	
	public function ajax_return() {
		$html = sprintf("<div id=\"header_preview\"><img src=\"%s\" width=\"%d\" height=\"%d\" alt=\"header image preview\" title=\"header image preview\" /><a href=\"%s\" title=\"%s\">%s</a></div>", esc_url($this->header['url']), $this->header['width'], $this->header['height'], wp_nonce_url(admin_url('admin.php?page=thesis-header-image&remove=true'), 'thesis-remove-header'), __('Click here to remove this header image', 'thesis'), __('Remove Image', 'thesis'));
		
		$updated = sprintf("<p>%s <a href=\"%s\">%s</a></p>", __('Header image updated!', 'thesis'), site_url(), __('Check out your site &rarr;', 'thesis'));
		
		_e('<p style="margin:9px 0;text-align:center;">Awesome job! Check out your spiffy new header image above.</p><p style="margin:9px 0;text-align:center;">Don\'t want to use that one? Just drop another image in and Thesis will change it right away!</p>', 'thesis');

		echo '<script type="text/javascript">
				jQuery(".media-item").not(":last").hide();
				jQuery("#header_preview").detach();
				jQuery("#thesis_links").after(\'' . $html . '\');
				jQuery(".progress").hide();
				jQuery("#updated").html(\'' . $updated . '\')</script>';
		die();
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
		
		$this->scripts();

		$rtl = (get_bloginfo('text_direction') == 'rtl') ? ' rtl' : ''; #wp
		echo "<div id=\"thesis_options\" class=\"wrap$rtl\">\n";
		thesis_version_indicator();
		thesis_options_title(__('Thesis Header Image', 'thesis'), false);
		thesis_options_nav();

		if (! empty($this->updated))
			echo "<div id=\"updated\" class=\"updated fade\">\n\t<p>" . __('Header image updated!', 'thesis') . ' <a href="' . get_bloginfo('url') . '/">' . __('Check out your site &rarr;', 'thesis') . "</a></p>\n</div>\n"; #wp
		elseif (! empty($this->removed))
			echo "<div id=\"updated\" class=\"updated fade\">\n\t<p>" . __('Header image removed!', 'thesis') . ' <a href="' . get_bloginfo('url') . '/">' . __('Check out your site &rarr;', 'thesis') . "</a></p>\n</div>\n"; #wp
		elseif (! empty($this->error))
			echo "<div class=\"warning\"><p>" . __('<strong>Whoops!</strong> You tried to upload an unrecognized file type. The header image uploader only accepts <code>.jpg</code>, <code>.png</code>, or <code>.gif</code> files.', 'thesis') . "</p></div>";

		if (! empty($this->header))
			echo "<div id=\"header_preview\">\n\t<img src=\"{$this->header['url']}\" width=\"{$this->header['width']}\" height=\"{$this->header['height']}\" alt=\"header image preview\" title=\"header image preview\" />\n\t<a href=\"" . wp_nonce_url(admin_url('admin.php?page=thesis-header-image&remove=true'), 'thesis-remove-header') . "\" title=\"" . __('Click here to remove this header image', 'thesis') . "\">" . __('Remove Image', 'thesis') . "</a>\n</div>\n";
			
		echo "<div class=\"one_col\"><div class=\"control_area\"><p>" . sprintf(__('Based on your <a href="%1$s">current layout settings</a>, the optimal header image width is <strong>%2$d pixels</strong>.', 'thesis'), admin_url('admin.php?page=thesis-design-options#layout-constructor'), $this->optimal_width);
		
		if ($this->do_crop)
			_e(' If your image is wider than this, don&#8217;t worry&#8212;Thesis will automatically resize it for you!', 'thesis');
		else 
			echo " <span style=\"color: #c00;\">" . esc_html__('Unfortunately, your server configuration won\'t allow us to crop the image for you. So be sure to crop it to the right size!', 'thesis') . "</span>";
		echo "</p>";	
		if (version_compare($wp_version, '3.3', '>=')) {
			echo "<form id=\"thesis-header-form\" enctype=\"multipart/form-data\" method=\"post\" action=\"" . admin_url("admin.php?page=thesis-header-image") . "\">";
			media_upload_form();
			echo "<script type=\"text/javascript\">
			jQuery(function($){
				updateMediaForm();
				post_id = 0;
				shortform = 1;
			});
			jQuery('.drag-drop-info').text('Drop header image here');
			jQuery('#plupload-browse-button').attr('value', 'Select header image');
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
			<form <?php echo $hide_old; ?> enctype="multipart/form-data" id="upload-form" method="post" action="<?php echo admin_url('admin.php?page=thesis-header-image'); ?>">
				<p class="remove_bottom_margin">
					<label for="upload"><?php _e('Choose an image from your computer:', 'thesis'); ?></label>
					<input type="file" class="text" id="upload" name="import" />
					<?php wp_nonce_field('thesis-header-upload', '_wpnonce-thesis-header-upload') ?>
					<input type="submit" class="ui_button positive" name="upload" value="<?php esc_attr_e('Upload', 'thesis'); ?>" />
				</p>
				<? echo $snazzy; ?>
				<script type="text/javascript">
				jQuery('.display-snazzy a').click(function(){
					jQuery('.hide-old-form').hide();
					jQuery('#drag-drop-area').show();
					jQuery('.upload-flash-bypass').show();
				});
				</script>
			</form>
		</div>
	</div>
<?php
		echo "</div>\n";
	}
}
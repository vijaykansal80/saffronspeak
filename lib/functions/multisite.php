<?php

/*
*	There are a number of things that we had to do for MS support. One of which is make custom folders for each install that runs Thesis.
*
*	Another thing we had to do was alter the version of timthumb we ship. Before you cry about timthumb, our version does NOT, I repeat NOT,
*	contain the security flaws that have been so popluar. So chill out and have a beer.
*
*	If you need assistance with multisite, please contact mattonomics@gmail.com. Be ready to provide your DIYthemes forum login details or you
*	won't be helped. Gotta keep it legit ;)
*
*	- Matt G
*/

function thesis_multisite_structure() {
	global $blog_id;
	
	$structure = array( // the structure of any given custom folder
		'dir' => array(
			'cache', 'images', 'rotator' // note that I'm not gonna move the original images from /custom
		),
		'file' => array(
			'custom.css', 'custom_functions.php', 'layout.css'
		),
		'images' => array(
			'sample-1.jpg', 'sample-2.jpg', 'sample-3.jpg', 'sample-4.jpg', 'sample-5.jpg'
		)
	);
	$errors = array();
	
	if ($blog_id >= 1 && @is_writable(dirname(THESIS_CUSTOM))) { // not sure why blog_id would be <= 0, but may as well check
		$custom = THESIS_CUSTOM;
		if (! @file_exists($custom)) { // check to see if the folder exists already
			if (@mkdir($custom, 0755)) { // makes the site specific custom folder, 755
				foreach ($structure['dir'] as $directory) {
					if (! @mkdir($custom . "/$directory", 0755)) // make the directories, 755
						$errors['mkdir'][] = $directory;
				}
				foreach ($structure['file'] as $file) {
					if ($file == 'custom.css')
						$contents = "/*\nFile:\t\t\tcustom.css\nDescription:\t\tCustom styles for Thesis\nMore Info:\t\thttp://diythemes.com/thesis/rtfm/custom-css/\n*/\n";
					elseif ($file == 'custom_functions.php')
						$contents = "<?php\n/* By taking advantage of hooks, filters, and the Custom Loop API, you can make Thesis\n* do ANYTHING you want. For more information, please see the following articles from\n* the Thesis User’s Guide or visit the members-only Thesis Support Forums:\n* \n* Hooks: http://diythemes.com/thesis/rtfm/customizing-with-hooks/\n* Filters: http://diythemes.com/thesis/rtfm/customizing-with-filters/\n* Custom Loop API: http://diythemes.com/thesis/rtfm/custom-loop-api/\n\n---:[ place your custom code below this line ]:---*/\n";
					elseif ($file = 'layout.css')
						$contents = '';
					if (file_put_contents("$custom/$file", $contents) === false)
						$errors[] = "Unable to create $file";
					elseif ($file == 'layout.css') {
						try {
							chmod($custom . "/$file", 0666); // blocked on some hosts
						}
						catch(Exception $e) {
							$errors[] = $e->getMessage();
						}
						if (@is_writable("$custom/$file"))
							thesis_generate_css();
						else $errors[] = "Could not write $file because it is not writable.";
					}
				}
				foreach ($structure['images'] as $image) {
					if (@file_exists(TEMPLATEPATH . "/custom/rotator/$image")) {
						if (@file_exists("$custom/rotator")) {
							if (! copy(TEMPLATEPATH . "/custom/rotator/$image", "$custom/rotator/$image"))
								$errors[] = "Failed to copy $image";
						}
						else $errors[] = "Could not find $custom/rotator";
					}
					else $errors[] = "Could not find " . TEMPLATEPATH . "/custom/rotator/$image";
				}
			}
		}
	}
	else {
		$errors[] = 'The Thesis directory is not writable. ' . dirname(THESIS_CUSTOM);
	}
	return ! empty($errors) ? $errors : false;
}
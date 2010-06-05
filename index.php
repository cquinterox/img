<?php
function getImageList() {

    // Set image directory
    $image_directory = 'images/';
    
    // Open the directory
    $dir_handle = opendir($image_directory); 

    // If we can open the directory
    if($dir_handle) {

         // First make sure there is a 'thumbs' folder, if not make it
         if(!is_dir($image_directory . '/thumbs')){
            mkdir(realpath($image_directory) . '/thumbs', 0755);
        }
         // Second sure there is a 'resized' folder, if not make it
         if(!is_dir($image_directory . '/resized')){
            mkdir(realpath($image_directory) . '/resized', 0755);
        }

        // Now loop through directory assigning each entry to $single_file
        while($single_file = readdir($dir_handle)) 
        {
            // Get the extension of each file
            $extension = strtolower(substr($single_file, (strlen($single_file) - 4), strlen($single_file)));

            // Now continue by looping through the files in the folder in search of the JPEG extension
            if ($extension == '.jpg' || $extension == 'jpeg') {

                // If a thumbnail for an image does not exist, make it
                if (!file_exists($image_directory . 'thumbs/thumb_' . $single_file)) {
                    make_thumb($image_directory . $single_file, $image_directory . 'thumbs/thumb_' . $single_file);
                }

              // If a resized image does not exist, also make it
                if (!file_exists($image_directory . 'resized/resized_' . $single_file)) {
                    make_resize($image_directory . $single_file, $image_directory . 'resized/resized_' . $single_file);
                }
                // Then link the thumbnail and resized image in the webpage
                print wrap($image_directory . 'thumbs/thumb_' . $single_file, $image_directory . 'resized/resized_' . $single_file);
            }
        }

        // Finally free up some memory
        unset($image_directory);
        unset($single_file);
        closedir($dir_handle);
    }
    else {
        // In case we can't open the image directory
        print 'Error opening directory image directory.';
    }
}
function wrap($thumb, $image) {
    return "\t<li><a href=\"$image\"><img src=\"$thumb\" alt=\"" . basename($image) . "\" title=\"" . basename($image) . "\"></img></a></li>\n";
}
function make_thumb($img_source, $img_save_location) {
    // Get the original image sizes
	$old_size = getimagesize($img_source);

    // Save original image sizes into variables
	$width = $old_size[0];
	$height = $old_size[1];

    // Declare our shifting variables for the thumbnail
    $w_off_center = 0;
    $h_off_center = 0;

    // Set thumb height/width size
    $new_size = 100;

    // If image is wider 
	if($width >= $height) {
        // $w_off_center helps set image focus shift
		$w_off_center = round(($width - $height) / 2 , 0);
        // Used to keep proportion
		$width = $height;

    // If image is taller
	} else {
        // $h_off_center helps set image focus shift
		$h_off_center = round(($height - $width) / 2, 0);
        // Used to keep proportion
		$height = $width;
	}

    // Creates a black frame of the image size
	$new_img = ImageCreatetruecolor($new_size,$new_size);
    // Save the old image
	$old_img = imagecreatefromjpeg($img_source);
    // Put all the variables together to make the new image
	imagecopyresampled($new_img,$old_img,0,0,$w_off_center,$h_off_center,$new_size,$new_size,$width,$height);
    // Save the image
	imagejpeg($new_img, $img_save_location, 80);
}
function make_resize($img_source, $img_save_location) {
    // Save the old image sizes
	$old_size = getimagesize($img_source);

    // Save old sizes into variables
	$width = $old_size[0];
	$height = $old_size[1];

    // Set new height/width size
    $new_width = 0;
    $new_height = 0;

    // Max size
    $max_size = 600;

    // Scaling it down some
	if($width >= $height && $width > $max_size) {
        $new_width = $max_size;
        $new_height = round(($height * $new_width) / $width, 0);
    // If image is taller
	} elseif ($height > $width && $height > $max_size){
        $new_height =  $max_size;
        $new_width = round(($width * $new_height) / $height, 0);
	} else {
        $new_width = $width;
        $new_height = $height;
    }
    // Creates a black frame of the image size
	$new_img = ImageCreatetruecolor($new_width,$new_height);
    // Save the old image
	$old_img = imagecreatefromjpeg($img_source);
    // Put all the variables together to make the new image
	imagecopyresampled($new_img,$old_img,0,0,0,0,$new_width,$new_height,$width,$height);
    // Save the image
	imagejpeg($new_img, $img_save_location, 80);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/xhtml; charset=UTF-8" />
    <meta name="author" content=".img - By Quinterox.com" />
	<meta name="description" content="An .img picture gallery. Download it free at http://quinterox.com." />
    <link type="text/css" rel="stylesheet" href="jquery.lightbox-0.5.css" />
    <link type="text/css" rel="stylesheet" href="img_app.css" />
    <script type="text/javascript" src="jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="jquery.lightbox-0.5.js"></script>
    <script type="text/javascript">
    	$(function() { $('#ul_images > li a').lightBox({fixedNavigation:true});	});
    </script>
    <title>.img - Picture Gallery by Quinterox.com</title>
</head>
<body>
<!--
     * .img 1.1 J.
     * http://quinterox.com/
     *
     * Copyright (c) 2009 Cesar Quinteros
     * This software is free to use and change and is not guaranteed to work
     *
     * Date: 06-07-2009
     * Revision: 1
-->
	<div id="header"><span class="first-word">Gallery:</span> Random Musical Pictures</div>
    <div id="container">
    	&nbsp;
    	<ul id="ul_images">
<?php getImageList(); ?>
    	</ul>
    	&nbsp;
    </div><!-- /container -->
    <div id="footer">
    	<a href="http://www.quinterox.com/content/mini-apps/phpic-free-image-gallery/" target="_blank">.img - Picture Gallery by Quinterox.com | Download Free</a>
    </div>
</body>
</html>
<?php
function get_svg_content($filename)
{
	$svg_directory = get_stylesheet_directory() . '/dist/images/';
	$file_path = $svg_directory . $filename . '.svg';

	if (file_exists($file_path)) {
		$svg_content = file_get_contents($file_path);
		return $svg_content;
	} else {
		return false;
	}
}
?>
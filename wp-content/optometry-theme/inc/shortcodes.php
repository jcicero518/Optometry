<?php

add_shortcode( 'column_split', function( $atts, $content, $filter ) {
	static $shortcodes = 1;

	$output = '';
	if ($shortcodes === 1) {
		$output .= '<div class="column_split_container cf">';
	}

	$default_pairs = [
		'column_width' => 'half'
	];
	$args = shortcode_atts( $default_pairs, $atts, 'column_split');

	switch ($args['column_width']) {
		case 'half':
			$output .= '<div class="column_split_half" style="width:50%;float:left">';
			$output .= apply_filters('the_content', $content);
			$output .= '</div>';
			break;
	}

	$shortcodes++;

	if ( ($args['column_width'] == 'half') && ($shortcodes === 3) ) {
		$output .= '</div>';
	}

	return $output;

});
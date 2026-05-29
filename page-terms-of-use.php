<?php
/**
 * Template Name: Terms of Use
 * Description: Legal page layout for the site terms of use.
 *
 * @package RHINO
 */

get_header();

if ( function_exists( 'rhino_render_legal_page' ) ) {
	rhino_render_legal_page(
		array(
			'type'          => 'terms',
			'eyebrow'       => __( 'Legal', 'rhino' ),
			'default_title' => __( 'Terms of Use', 'rhino' ),
		)
	);
}

get_footer();

<?php
/**
 * Template Name: Privacy Policy
 * Description: Legal page layout for the site privacy policy.
 *
 * @package RHINO
 */

get_header();

if ( function_exists( 'rhino_render_legal_page' ) ) {
	rhino_render_legal_page(
		array(
			'type'          => 'privacy',
			'eyebrow'       => __( 'Legal', 'rhino' ),
			'default_title' => __( 'Privacy Policy', 'rhino' ),
		)
	);
}

get_footer();

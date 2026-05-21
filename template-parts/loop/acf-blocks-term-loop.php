<?php
/**
 * ACF Flexible Content loop for taxonomy terms.
 *
 * @package RHINO
 */

$term = get_query_var( 'rhino_acf_term' );

if ( ! $term instanceof WP_Term ) {
	$term = get_queried_object();
}

if ( ! $term instanceof WP_Term ) {
	return;
}

$contexts = array(
	$term,
	$term->taxonomy . '_' . $term->term_id,
);

$field_name = 'blocks';
$has_rows   = false;

foreach ( $contexts as $context ) {
	if ( have_rows( $field_name, $context ) ) {
		$has_rows = true;

		while ( have_rows( $field_name, $context ) ) {
			the_row();

			$layout = get_row_layout();

			// Category hero is rendered in template-parts/hero/service-category.php.
			if ( 'hero' === $layout ) {
				continue;
			}

			// Contact always comes from homepage (see rhino_render_homepage_contact_section).
			if ( 'contact' === $layout ) {
				continue;
			}

			get_template_part( 'template-parts/acf-blocks/' . $layout );
		}

		break;
	}
}

if ( ! $has_rows ) {
	return;
}

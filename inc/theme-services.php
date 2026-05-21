<?php
/**
 * Services post type & service-category taxonomy helpers.
 *
 * @package RHINO
 */

/**
 * Resolve service category taxonomy slug.
 *
 * @return string
 */
function rhino_service_category_taxonomy() {
	$candidates = array( 'service-category', 'service_category' );

	foreach ( $candidates as $taxonomy ) {
		if ( taxonomy_exists( $taxonomy ) ) {
			return $taxonomy;
		}
	}

	$post_types = array( 'services', 'service' );

	foreach ( $post_types as $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}

		$taxonomies = get_object_taxonomies( $post_type, 'names' );

		if ( empty( $taxonomies ) ) {
			continue;
		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				return $taxonomy;
			}
		}

		return $taxonomies[0];
	}

	return 'service-category';
}

/**
 * Get service category terms for Our Services block.
 *
 * @return WP_Term[]
 */
function rhino_get_service_category_terms() {
	$taxonomy = rhino_service_category_taxonomy();

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	usort(
		$terms,
		function ( $a, $b ) {
			return (int) $b->count <=> (int) $a->count;
		}
	);

	return array_slice( $terms, 0, 4 );
}

/**
 * Get ACF field value for a service category term.
 *
 * @param WP_Term|int $term  Term object or term ID.
 * @param string      $field ACF field name.
 * @return mixed
 */
function rhino_get_service_category_field( $term, $field ) {
	if ( ! function_exists( 'get_field' ) || ! $field ) {
		return null;
	}

	$term_id = $term instanceof WP_Term ? $term->term_id : (int) $term;
	$taxonomy = $term instanceof WP_Term ? $term->taxonomy : rhino_service_category_taxonomy();

	$value = get_field( $field, $term );

	if ( function_exists( 'rhino_acf_value_is_empty' ) && rhino_acf_value_is_empty( $value ) ) {
		$value = get_field( $field, $taxonomy . '_' . $term_id );
	}

	return $value;
}

/**
 * Get image URL for service category term.
 *
 * @param WP_Term|int $term Term object or term ID.
 * @return string
 */
function rhino_get_service_category_image_url( $term ) {
	$image = rhino_get_service_category_field( $term, 'category_image' );

	return function_exists( 'rhino_acf_image_url' ) ? rhino_acf_image_url( $image ) : '';
}

/**
 * Get taxonomy term description (WordPress native field).
 *
 * @param WP_Term $term Term object.
 * @return string
 */
function rhino_get_service_category_description( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	$description = term_description( $term->term_id, $term->taxonomy );

	if ( ! is_string( $description ) || '' === trim( $description ) ) {
		$description = $term->description ?? '';
	}

	return is_string( $description ) ? trim( $description ) : '';
}

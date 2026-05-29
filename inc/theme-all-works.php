<?php
/**
 * All Works section helpers.
 *
 * @package RHINO
 */

/**
 * First word of a category name (before the first space).
 *
 * @param string|WP_Term $term_or_name Term object or raw name.
 * @return string
 */
function rhino_get_term_short_label( $term_or_name ) {
	if ( $term_or_name instanceof WP_Term ) {
		$name = $term_or_name->name;
	} elseif ( is_string( $term_or_name ) ) {
		$name = $term_or_name;
	} else {
		return '';
	}

	$name = trim( $name );

	if ( '' === $name ) {
		return '';
	}

	$parts = preg_split( '/\s+/', $name, 2 );

	return $parts[0] ?? $name;
}

/**
 * Primary service category for a post.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function rhino_get_service_post_primary_term( $post_id ) {
	$post_id = (int) $post_id;

	if ( ! $post_id || ! function_exists( 'rhino_service_category_taxonomy' ) ) {
		return null;
	}

	$taxonomy = rhino_service_category_taxonomy();

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return null;
	}

	$terms = wp_get_post_terms( $post_id, $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}

	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term ) {
			return $term;
		}
	}

	return null;
}

/**
 * Build services for the All Works section.
 *
 * @param int|null $term_id Service category term ID, or null for all categories.
 * @return WP_Post[]
 */
function rhino_build_all_works_posts( $term_id = null ) {
	$post_type = function_exists( 'rhino_get_services_post_type' )
		? rhino_get_services_post_type()
		: 'services';

	if ( ! post_type_exists( $post_type ) ) {
		return array();
	}

	$query_args = array(
		'post_type'              => $post_type,
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'suppress_filters'       => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	if ( $term_id && function_exists( 'rhino_service_category_taxonomy' ) ) {
		$taxonomy = rhino_service_category_taxonomy();

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => (int) $term_id,
			),
		);
	}

	$post_ids = get_posts( $query_args );
	$posts    = array();
	$seen     = array();

	foreach ( $post_ids as $post_id ) {
		$post_id = (int) $post_id;

		if ( ! $post_id || isset( $seen[ $post_id ] ) ) {
			continue;
		}

		if ( function_exists( 'rhino_service_visible_in_category_work' )
			&& ! rhino_service_visible_in_category_work( $post_id ) ) {
			continue;
		}

		$image_after = function_exists( 'rhino_acf_image_url' )
			? rhino_acf_image_url( rhino_get_service_post_field( $post_id, 'image_after' ) )
			: '';

		if ( ! $image_after ) {
			continue;
		}

		$service_post = get_post( $post_id );

		if ( $service_post instanceof WP_Post ) {
			$posts[]           = $service_post;
			$seen[ $post_id ] = true;
		}
	}

	return $posts;
}

/**
 * Panels keyed by "all" or term ID string.
 *
 * @return array<string, WP_Post[]>
 */
function rhino_get_all_works_panels() {
	$panels = array(
		'all' => rhino_build_all_works_posts( null ),
	);

	$terms = function_exists( 'rhino_get_category_list_terms' )
		? rhino_get_category_list_terms()
		: array();

	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$posts = rhino_build_all_works_posts( $term->term_id );

		if ( ! empty( $posts ) ) {
			$panels[ (string) $term->term_id ] = $posts;
		}
	}

	return array_filter(
		$panels,
		static function ( $posts ) {
			return ! empty( $posts );
		}
	);
}

/**
 * Filter options for the All Works section.
 *
 * @return array<int, array{ id: string, label: string }>
 */
function rhino_get_all_works_filter_options() {
	$options = array(
		array(
			'id'    => 'all',
			'label' => __( 'All', 'rhino' ),
		),
	);

	$terms = function_exists( 'rhino_get_category_list_terms' )
		? rhino_get_category_list_terms()
		: array();

	$panels = rhino_get_all_works_panels();

	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term || empty( $term->name ) ) {
			continue;
		}

		$key = (string) $term->term_id;

		if ( empty( $panels[ $key ] ) ) {
			continue;
		}

		$options[] = array(
			'id'    => $key,
			'label' => rhino_get_term_short_label( $term ),
		);
	}

	return $options;
}

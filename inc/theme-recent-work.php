<?php
/**
 * Recent Work section helpers.
 *
 * @package RHINO
 */

/**
 * Resolve services post type slug.
 *
 * @return string
 */
function rhino_get_services_post_type() {
	$candidates = array( 'services', 'service' );

	foreach ( $candidates as $post_type ) {
		if ( post_type_exists( $post_type ) ) {
			return $post_type;
		}
	}

	return 'services';
}

/**
 * Whether an ACF true/false (or similar) value is enabled.
 *
 * @param mixed $value Field value.
 * @return bool
 */
function rhino_acf_is_true( $value ) {
	if ( true === $value || 1 === $value ) {
		return true;
	}

	if ( is_string( $value ) ) {
		$value = strtolower( trim( $value ) );

		return in_array( $value, array( '1', 'true', 'yes', 'on' ), true );
	}

	return false;
}

/**
 * Get service field for a post (supports top-level and "services" group).
 * Uses post meta only so ACF flexible loops on the page are not disrupted.
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field name.
 * @return mixed
 */
function rhino_get_service_post_field( $post_id, $field ) {
	$post_id = (int) $post_id;

	if ( ! $post_id ) {
		return null;
	}

	$value = get_post_meta( $post_id, $field, true );

	if ( is_string( $value ) && '' === $value ) {
		$value = null;
	}

	if ( null !== $value && false !== $value && '' !== $value ) {
		return $value;
	}

	$group = get_post_meta( $post_id, 'services', true );

	if ( is_array( $group ) && array_key_exists( $field, $group ) ) {
		return $group[ $field ];
	}

	return $value;
}

/**
 * Build list of services for recent work (uncached).
 *
 * @return WP_Post[]
 */
function rhino_build_recent_work_posts() {
	$post_type = rhino_get_services_post_type();

	if ( ! post_type_exists( $post_type ) ) {
		return array();
	}

	$post_ids = get_posts(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'suppress_filters'       => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$posts = array();

	foreach ( $post_ids as $post_id ) {
		$post_id = (int) $post_id;

		if ( ! $post_id ) {
			continue;
		}

		$show_on_home = rhino_get_service_post_field( $post_id, 'show_on_home_page' );

		if ( ! rhino_acf_is_true( $show_on_home ) ) {
			continue;
		}

		$image_before = function_exists( 'rhino_acf_image_url' )
			? rhino_acf_image_url( rhino_get_service_post_field( $post_id, 'image_before' ) )
			: '';
		$image_after  = function_exists( 'rhino_acf_image_url' )
			? rhino_acf_image_url( rhino_get_service_post_field( $post_id, 'image_after' ) )
			: '';

		if ( ! $image_before || ! $image_after ) {
			continue;
		}

		$service_post = get_post( $post_id );

		if ( $service_post instanceof WP_Post ) {
			$posts[] = $service_post;
		}
	}

	return $posts;
}

/**
 * Prime static cache before the flexible loop runs.
 */
function rhino_prepare_recent_work_posts_cache() {
	rhino_get_recent_work_posts();
}

/**
 * Get services marked for homepage recent work slider.
 *
 * @return WP_Post[]
 */
function rhino_get_recent_work_posts() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$cache = rhino_build_recent_work_posts();

	return $cache;
}

/**
 * Read a value from the recent category work ACF group (supports recent_work_* fallbacks).
 *
 * @param array|null $section Group field value.
 * @param string     $key     Primary field name (recent_category_work_*).
 * @return mixed
 */
function rhino_get_recent_category_work_section_value( $section, $key ) {
	if ( ! is_array( $section ) ) {
		return null;
	}

	$keys = array( $key );

	if ( 0 === strpos( $key, 'recent_category_work_' ) ) {
		$keys[] = 'recent_work_' . substr( $key, strlen( 'recent_category_work_' ) );
	}

	foreach ( $keys as $field_key ) {
		if ( array_key_exists( $field_key, $section ) ) {
			$value = $section[ $field_key ];

			if ( null !== $value && '' !== $value && array() !== $value ) {
				return $value;
			}
		}
	}

	return null;
}

/**
 * Whether a service should appear in the category recent work slider.
 *
 * @param int $post_id Service post ID.
 * @return bool
 */
function rhino_service_visible_in_category_work( $post_id ) {
	$post_id = (int) $post_id;

	if ( ! $post_id ) {
		return false;
	}

	$show_on_category = rhino_get_service_post_field( $post_id, 'show_on_category_page' );

	if ( rhino_acf_is_true( $show_on_category ) ) {
		return true;
	}

	// Explicitly hidden on category pages.
	if ( false === $show_on_category || 0 === $show_on_category || '0' === $show_on_category ) {
		return false;
	}

	// Field not set: include all services in this category (images checked separately).
	return true;
}

/**
 * Build list of services for category recent work (uncached).
 *
 * @param WP_Term $term Service category term.
 * @return WP_Post[]
 */
function rhino_build_recent_category_work_posts( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$post_type = rhino_get_services_post_type();

	if ( ! post_type_exists( $post_type ) ) {
		return array();
	}

	$post_ids = get_posts(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'suppress_filters'       => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy' => $term->taxonomy,
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		)
	);

	$posts = array();

	foreach ( $post_ids as $post_id ) {
		$post_id = (int) $post_id;

		if ( ! $post_id ) {
			continue;
		}

		if ( ! rhino_service_visible_in_category_work( $post_id ) ) {
			continue;
		}

		$image_before = function_exists( 'rhino_acf_image_url' )
			? rhino_acf_image_url( rhino_get_service_post_field( $post_id, 'image_before' ) )
			: '';
		$image_after  = function_exists( 'rhino_acf_image_url' )
			? rhino_acf_image_url( rhino_get_service_post_field( $post_id, 'image_after' ) )
			: '';

		if ( ! $image_before || ! $image_after ) {
			continue;
		}

		$service_post = get_post( $post_id );

		if ( $service_post instanceof WP_Post ) {
			$posts[] = $service_post;
		}

		if ( count( $posts ) >= 12 ) {
			break;
		}
	}

	return $posts;
}

/**
 * Get services marked for category recent work slider.
 *
 * @param WP_Term|null $term Term object. Defaults to queried term or term loop context.
 * @return WP_Post[]
 */
function rhino_get_recent_category_work_posts( $term = null ) {
	static $cache = array();

	if ( ! $term instanceof WP_Term ) {
		$term = get_query_var( 'rhino_acf_term' );

		if ( ! $term instanceof WP_Term ) {
			$term = get_queried_object();
		}
	}

	if ( ! $term instanceof WP_Term ) {
		return array();
	}

	$cache_key = $term->taxonomy . '_' . $term->term_id;

	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$cache[ $cache_key ] = rhino_build_recent_category_work_posts( $term );

	return $cache[ $cache_key ];
}

<?php
/**
 * Reviews section helpers.
 *
 * @package RHINO
 */

/**
 * Resolve reviews post type slug.
 *
 * @return string
 */
function rhino_get_reviews_post_type() {
	$candidates = array( 'reviews', 'review' );

	foreach ( $candidates as $post_type ) {
		if ( post_type_exists( $post_type ) ) {
			return $post_type;
		}
	}

	return 'reviews';
}

/**
 * Get review field for a post (supports top-level and grouped ACF fields).
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field name.
 * @return mixed
 */
function rhino_get_review_post_field( $post_id, $field ) {
	$post_id = (int) $post_id;

	if ( ! $post_id ) {
		return null;
	}

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $field, $post_id );

		if ( null !== $value && false !== $value && '' !== $value ) {
			return $value;
		}
	}

	$value = get_post_meta( $post_id, $field, true );

	if ( is_string( $value ) && '' === $value ) {
		$value = null;
	}

	if ( null !== $value && false !== $value && '' !== $value ) {
		return $value;
	}

	$group = get_post_meta( $post_id, 'reviews', true );

	if ( is_array( $group ) && array_key_exists( $field, $group ) ) {
		return $group[ $field ];
	}

	return $value;
}

/**
 * Get published review posts for the reviews slider.
 *
 * @return WP_Post[]
 */
function rhino_get_reviews_posts() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$post_type = rhino_get_reviews_post_type();

	if ( ! post_type_exists( $post_type ) ) {
		$cache = array();
		return $cache;
	}

	$cache = get_posts(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'suppress_filters'       => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	return $cache;
}

/**
 * Render star icons for a rating value.
 *
 * @param float $rating   Rating value.
 * @param int   $max      Maximum stars.
 * @param bool  $use_ceil Round filled count up.
 */
function rhino_render_rating_stars( $rating, $max = 5, $use_ceil = false ) {
	$rating = (float) $rating;
	$max    = max( 1, (int) $max );
	$filled = $use_ceil ? (int) ceil( $rating ) : (int) floor( $rating );
	$filled = max( 0, min( $max, $filled ) );
	$empty  = $max - $filled;

	$star_url  = get_template_directory_uri() . '/assets/images/Star.svg';
	$empty_url = get_template_directory_uri() . '/assets/images/Star-empty.svg';

	for ( $i = 0; $i < $filled; $i++ ) {
		echo '<img class="reviews-section__star" src="' . esc_url( $star_url ) . '" alt="" width="24" height="24" loading="lazy" decoding="async" />';
	}

	for ( $i = 0; $i < $empty; $i++ ) {
		echo '<img class="reviews-section__star" src="' . esc_url( $empty_url ) . '" alt="" width="24" height="24" loading="lazy" decoding="async" />';
	}
}

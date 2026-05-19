<?php
/**
 * Reviews section helpers.
 *
 * @package RHINO
 */

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

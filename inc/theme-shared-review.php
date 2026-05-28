<?php
/**
 * Shared review form (front-end submission to Reviews CPT).
 *
 * @package RHINO
 */

/**
 * Service area cities shown on the site (Where We Work).
 *
 * @return string[]
 */
function rhino_get_service_area_cities() {
	return array(
		'Raleigh',
		'Durham',
		'Cary',
		'Chapel Hill',
		'Apex',
	);
}

/**
 * Default city when geolocation is unavailable.
 *
 * @return string
 */
function rhino_get_default_service_area_city() {
	return 'Raleigh';
}

/**
 * Normalize detected city to a service-area label when possible.
 *
 * @param string $city Raw city from geolocation or form.
 * @return string
 */
function rhino_resolve_review_city( $city ) {
	$city = trim( (string) $city );

	if ( '' === $city ) {
		return rhino_get_default_service_area_city();
	}

	$city_lower = strtolower( $city );

	foreach ( rhino_get_service_area_cities() as $service_city ) {
		$service_lower = strtolower( $service_city );

		if ( $city_lower === $service_lower ) {
			return $service_city;
		}

		if ( false !== strpos( $city_lower, $service_lower ) ) {
			return $service_city;
		}
	}

	$region_map = array(
		'wake'         => 'Raleigh',
		'wake county'  => 'Raleigh',
		'durham'       => 'Durham',
		'orange'       => 'Chapel Hill',
		'orange county' => 'Chapel Hill',
		'chatham'      => 'Apex',
		'johnston'     => 'Raleigh',
	);

	foreach ( $region_map as $needle => $mapped_city ) {
		if ( false !== strpos( $city_lower, $needle ) ) {
			return $mapped_city;
		}
	}

	return $city;
}

/**
 * Localize script data for shared review AJAX.
 */
function rhino_shared_review_localize_script() {
	wp_localize_script(
		'rhino-main-js',
		'rhinoSharedReview',
		array(
			'ajaxUrl'               => admin_url( 'admin-ajax.php' ),
			'action'                => 'rhino_submit_shared_review',
			'nonce'                 => wp_create_nonce( 'rhino_shared_review' ),
			'serviceAreaCities'     => rhino_get_service_area_cities(),
			'defaultServiceAreaCity' => rhino_get_default_service_area_city(),
			'i18n'                  => array(
				'error'         => __( 'Something went wrong. Please try again.', 'rhino' ),
				'requiredField' => __( 'This field is required.', 'rhino' ),
				'invalidEmail'  => __( 'Please enter a valid email address.', 'rhino' ),
				'invalidRating' => __( 'Please select a star rating.', 'rhino' ),
				'sending'       => __( 'Sending…', 'rhino' ),
				'timeout'       => __( 'Request timed out. Please check your connection and try again.', 'rhino' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'rhino_shared_review_localize_script', 100 );

/**
 * Save review meta fields on a post.
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field name.
 * @param mixed  $value   Value.
 */
function rhino_save_review_post_field( $post_id, $field, $value ) {
	$post_id = (int) $post_id;

	if ( ! $post_id || '' === $field ) {
		return;
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( $field, $value, $post_id );
	}

	update_post_meta( $post_id, $field, $value );

	$group = get_post_meta( $post_id, 'reviews', true );

	if ( ! is_array( $group ) ) {
		$group = array();
	}

	$group[ $field ] = $value;
	update_post_meta( $post_id, 'reviews', $group );
}

/**
 * Default author for front-end review submissions (guests cannot create posts by default).
 *
 * @return int
 */
function rhino_get_shared_review_author_id() {
	$author_id = (int) get_option( 'rhino_shared_review_author_id', 1 );

	if ( $author_id && get_userdata( $author_id ) ) {
		return $author_id;
	}

	$admins = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => 'ID',
		)
	);

	return ! empty( $admins[0] ) ? (int) $admins[0] : 0;
}

/**
 * Allow creating a draft review during the shared review AJAX request.
 *
 * @param array   $allcaps All capabilities.
 * @param array   $caps    Required capabilities.
 * @param array   $args    Capability arguments.
 * @param WP_User $user    User object.
 * @return array
 */
function rhino_shared_review_grant_insert_caps( $allcaps, $caps, $args, $user ) {
	if ( empty( $GLOBALS['rhino_shared_review_insert'] ) ) {
		return $allcaps;
	}

	foreach ( (array) $caps as $cap ) {
		$allcaps[ $cap ] = true;
	}

	return $allcaps;
}

/**
 * Map meta capabilities for guest review submission.
 *
 * @param array  $caps    Required capabilities.
 * @param string $cap     Capability name.
 * @param int    $user_id User ID.
 * @param array  $args    Extra arguments.
 * @return array
 */
function rhino_shared_review_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if ( empty( $GLOBALS['rhino_shared_review_insert'] ) ) {
		return $caps;
	}

	$post_type = function_exists( 'rhino_get_reviews_post_type' ) ? rhino_get_reviews_post_type() : 'reviews';

	if ( in_array( $cap, array( 'edit_post', 'publish_post', 'delete_post' ), true ) ) {
		return array( 'exist' );
	}

	if ( 'create_posts' === $cap || 'edit_posts' === $cap ) {
		return array( 'exist' );
	}

	$pto = get_post_type_object( $post_type );

	if ( $pto && ! empty( $pto->cap->create_posts ) && $cap === $pto->cap->create_posts ) {
		return array( 'exist' );
	}

	return $caps;
}

/**
 * Create a draft review post from submitted form data.
 *
 * @param array $data Sanitized submission data.
 * @return int|WP_Error Post ID or error.
 */
function rhino_create_draft_review_post( $data ) {
	$post_type = function_exists( 'rhino_get_reviews_post_type' ) ? rhino_get_reviews_post_type() : 'reviews';

	if ( ! post_type_exists( $post_type ) ) {
		return new WP_Error( 'rhino_reviews_missing', __( 'Reviews post type is not available.', 'rhino' ) );
	}

	$author_id = rhino_get_shared_review_author_id();

	if ( ! $author_id ) {
		return new WP_Error( 'rhino_review_author_missing', __( 'Could not save your review.', 'rhino' ) );
	}

	$GLOBALS['rhino_shared_review_insert'] = true;
	add_filter( 'user_has_cap', 'rhino_shared_review_grant_insert_caps', 10, 4 );
	add_filter( 'map_meta_cap', 'rhino_shared_review_map_meta_cap', 10, 4 );

	$post_id = wp_insert_post(
		array(
			'post_type'    => $post_type,
			'post_status'  => 'draft',
			'post_title'   => $data['full_name'],
			'post_content' => $data['message'],
			'post_author'  => $author_id,
		),
		true
	);

	remove_filter( 'user_has_cap', 'rhino_shared_review_grant_insert_caps', 10 );
	remove_filter( 'map_meta_cap', 'rhino_shared_review_map_meta_cap', 10 );
	unset( $GLOBALS['rhino_shared_review_insert'] );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return is_wp_error( $post_id )
			? $post_id
			: new WP_Error( 'rhino_review_create_failed', __( 'Could not save your review.', 'rhino' ) );
	}

	rhino_save_review_post_field( $post_id, 'rating', $data['rating'] );
	rhino_save_review_post_field( $post_id, 'city', $data['city'] );
	rhino_save_review_post_field( $post_id, 'email', $data['email'] );

	return $post_id;
}

/**
 * Get visitor IP address.
 *
 * @return string
 */
function rhino_get_visitor_ip() {
	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
	}

	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$forwarded = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		return trim( $forwarded[0] );
	}

	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}

	return '';
}

/**
 * Detect regional center city from visitor IP (ipwho.is).
 *
 * @return string
 */
function rhino_detect_city_from_ip() {
	$ip = rhino_get_visitor_ip();

	if ( ! $ip || in_array( $ip, array( '127.0.0.1', '::1' ), true ) ) {
		return '';
	}

	$cache_key = 'rhino_review_city_' . md5( $ip );
	$cached    = get_transient( $cache_key );

	if ( is_string( $cached ) ) {
		return $cached;
	}

	$response = wp_remote_get(
		'https://ipwho.is/' . rawurlencode( $ip ),
		array(
			'timeout' => 2,
		)
	);

	if ( is_wp_error( $response ) ) {
		set_transient( $cache_key, '', 10 * MINUTE_IN_SECONDS );
		return '';
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $body ) || empty( $body['success'] ) ) {
		set_transient( $cache_key, '', 10 * MINUTE_IN_SECONDS );
		return '';
	}

	$city = ! empty( $body['city'] ) ? sanitize_text_field( $body['city'] ) : '';

	if ( '' === $city && ! empty( $body['region'] ) ) {
		$city = sanitize_text_field( $body['region'] );
	}

	set_transient( $cache_key, $city, DAY_IN_SECONDS );

	return $city;
}

/**
 * AJAX: submit shared review form.
 */
function rhino_ajax_submit_shared_review() {
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'rhino_shared_review' ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Security check failed. Please refresh the page and try again.', 'rhino' ) ),
			403
		);
	}

	if ( ! empty( $_POST['company'] ) ) { // Honeypot.
		wp_send_json_error(
			array( 'message' => __( 'Something went wrong. Please try again.', 'rhino' ) ),
			400
		);
	}

	$full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
	$message   = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$rating = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;
	$city   = isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '';

	if ( '' === $city ) {
		$city = rhino_detect_city_from_ip();
	}

	$city = rhino_resolve_review_city( $city );

	if ( '' === $full_name || '' === $email || '' === $message || $rating < 1 || $rating > 5 ) {
		wp_send_json_error(
			array( 'message' => __( 'Please fill in all required fields and select a rating.', 'rhino' ) ),
			422
		);
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Please enter a valid email address.', 'rhino' ) ),
			422
		);
	}

	$result = rhino_create_draft_review_post(
		array(
			'full_name' => $full_name,
			'email'     => $email,
			'message'   => $message,
			'rating'    => $rating,
			'city'      => $city,
		)
	);

	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array( 'message' => $result->get_error_message() ),
			500
		);
	}

	wp_send_json_success(
		array(
			'postId'  => (int) $result,
			'message' => __( 'Thank you! Your review has been submitted and is pending approval.', 'rhino' ),
		)
	);
}
add_action( 'wp_ajax_rhino_submit_shared_review', 'rhino_ajax_submit_shared_review' );
add_action( 'wp_ajax_nopriv_rhino_submit_shared_review', 'rhino_ajax_submit_shared_review' );

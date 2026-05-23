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
 * Whether the current request is a service category taxonomy archive.
 *
 * @return bool
 */
function rhino_is_service_category_archive() {
	if ( is_tax( array( 'service-category', 'service_category' ) ) ) {
		return true;
	}

	$taxonomy = rhino_service_category_taxonomy();

	if ( $taxonomy && is_tax( $taxonomy ) ) {
		return true;
	}

	$term = get_queried_object();

	return $term instanceof WP_Term
		&& in_array( $term->taxonomy, array( 'service-category', 'service_category' ), true );
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
 * Get all service category terms for the category list block.
 *
 * @return WP_Term[]
 */
function rhino_get_category_list_terms() {
	$taxonomy = rhino_service_category_taxonomy();

	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	return $terms;
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

/**
 * Get Services page URL.
 *
 * @return string
 */
function rhino_get_services_page_url() {
	$page = get_page_by_path( 'services' );

	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	return home_url( '/services/' );
}

/**
 * Front page ID used for shared homepage ACF blocks.
 *
 * @return int
 */
function rhino_get_homepage_id() {
	return (int) get_option( 'page_on_front' );
}

/**
 * Get one flexible layout row from homepage blocks.
 *
 * @param string $layout Layout name (e.g. hero, contact).
 * @return array|null Full flexible row data.
 */
function rhino_get_homepage_flexible_layout( $layout ) {
	if ( ! function_exists( 'get_field' ) || ! $layout ) {
		return null;
	}

	$page_id = rhino_get_homepage_id();

	if ( ! $page_id ) {
		return null;
	}

	$blocks = get_field( 'blocks', $page_id );

	if ( ! empty( $blocks ) && is_array( $blocks ) ) {
		foreach ( $blocks as $block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}

			if ( $layout === ( $block['acf_fc_layout'] ?? '' ) ) {
				return $block;
			}
		}
	}

	if ( function_exists( 'have_rows' ) && have_rows( 'blocks', $page_id ) ) {
		while ( have_rows( 'blocks', $page_id ) ) {
			the_row();

			if ( $layout === get_row_layout() ) {
				$row = get_row( true );

				if ( ! empty( $row ) && is_array( $row ) ) {
					return $row;
				}
			}
		}
	}

	return null;
}

/**
 * Get homepage hero section data from ACF blocks.
 *
 * @return array|null
 */
function rhino_get_homepage_hero_section() {
	$block = rhino_get_homepage_flexible_layout( 'hero' );

	if ( empty( $block['hero_section'] ) || ! is_array( $block['hero_section'] ) ) {
		return null;
	}

	return $block['hero_section'];
}

/**
 * Get homepage contact section data from ACF blocks.
 *
 * @return array|null
 */
function rhino_get_homepage_contact_section() {
	$block = rhino_get_homepage_flexible_layout( 'contact' );

	if ( empty( $block['contact_section'] ) || ! is_array( $block['contact_section'] ) ) {
		return null;
	}

	return $block['contact_section'];
}

/**
 * Render homepage contact block (used on service category archives).
 */
function rhino_render_homepage_contact_section() {
	$block = rhino_get_homepage_flexible_layout( 'contact' );

	if ( empty( $block['contact_section'] ) || ! is_array( $block['contact_section'] ) ) {
		return;
	}

	global $rhino_prefetched_contact;

	$rhino_prefetched_contact = array(
		'section' => $block['contact_section'],
		'options' => $block['options'] ?? null,
	);

	get_template_part( 'template-parts/acf-blocks/contact' );

	unset( $rhino_prefetched_contact );
}

/**
 * Get CTA button from homepage hero section.
 *
 * @return array|null
 */
function rhino_get_homepage_hero_button() {
	$hero = rhino_get_homepage_hero_section();

	if ( empty( $hero ) || ! function_exists( 'rhino_acf_link' ) ) {
		return null;
	}

	return rhino_acf_link( $hero['hero_button'] ?? null );
}

/**
 * Get background watermark text from homepage hero (optional).
 *
 * @return string
 */
function rhino_get_homepage_hero_bg_text() {
	$hero = rhino_get_homepage_hero_section();

	return $hero ? trim( (string) ( $hero['hero_text_bg'] ?? '' ) ) : '';
}

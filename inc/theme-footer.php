<?php
/**
 * Footer helpers (ACF Footer Options).
 *
 * @package RHINO
 */

/**
 * Get ACF field from Footer Options page.
 *
 * @param string $field Field name.
 * @return mixed
 */
function rhino_get_footer_field( $field ) {
	$contexts = array( 'footer-options', 'acf-options-footer', 'option' );

	foreach ( $contexts as $context ) {
		if ( ! function_exists( 'get_field' ) ) {
			break;
		}

		$value = get_field( $field, $context );

		if ( ! rhino_acf_value_is_empty( $value ) ) {
			return $value;
		}
	}

	return null;
}

/**
 * Get footer text with optional HTML (WYSIWYG).
 *
 * @param string $field Field name.
 * @return string
 */
function rhino_footer_html( $field ) {
	$value = rhino_get_footer_field( $field );

	if ( rhino_acf_value_is_empty( $value ) ) {
		return '';
	}

	return is_string( $value ) ? $value : '';
}

/**
 * Render footer logo.
 */
function rhino_footer_logo() {
	$logo = rhino_get_footer_field( 'footer_logo' );
	$url  = rhino_acf_image_url( $logo );

	if ( ! $url ) {
		return;
	}

	$alt = is_array( $logo ) ? (string) ( $logo['alt'] ?? '' ) : '';

	printf(
		'<a class="site-footer__logo-link" href="%1$s" rel="home"><img class="site-footer__logo" src="%2$s" alt="%3$s" width="320" height="73" decoding="async"></a>',
		esc_url( home_url( '/' ) ),
		esc_url( $url ),
		esc_attr( $alt ?: get_bloginfo( 'name' ) )
	);
}

/**
 * Check if contact block should be shown.
 *
 * @return bool
 */
function rhino_footer_has_contact() {
	$phone = rhino_footer_contact_text( 'footer_phone' );
	$email = rhino_footer_contact_text( 'footer_email' );

	return '' !== $phone || '' !== $email;
}

/**
 * Get plain contact text from footer field.
 *
 * @param string $field Field name.
 * @return string
 */
function rhino_footer_contact_text( $field ) {
	$value = rhino_get_footer_field( $field );

	if ( is_string( $value ) || is_numeric( $value ) ) {
		return trim( (string) $value );
	}

	if ( is_array( $value ) ) {
		return rhino_acf_text( $value );
	}

	return '';
}

/**
 * Render footer contact block.
 */
function rhino_footer_contact() {
	if ( ! rhino_footer_has_contact() ) {
		return;
	}

	$phone    = rhino_footer_contact_text( 'footer_phone' );
	$email    = rhino_footer_contact_text( 'footer_email' );
	$schedule = rhino_footer_contact_text( 'footer_schedule' );
	$email_href = $email ? sanitize_email( $email ) : '';

	echo '<div class="site-footer__contact">';

	printf(
		'<p class="site-footer__contact-title">%s</p>',
		esc_html__( 'Contact', 'rhino' )
	);

	if ( $phone ) {
		printf(
			'<a class="site-footer__contact-item site-footer__contact-phone" href="%1$s">%2$s</a>',
			esc_url( rhino_format_phone_href( $phone ) ?: '#' ),
			esc_html( $phone )
		);
	}

	if ( $email && $email_href ) {
		printf(
			'<a class="site-footer__contact-item site-footer__contact-email" href="mailto:%1$s">%2$s</a>',
			esc_attr( $email_href ),
			esc_html( $email )
		);
	}

	if ( $schedule ) {
		printf(
			'<p class="site-footer__contact-item site-footer__contact-schedule">%s</p>',
			esc_html( $schedule )
		);
	}

	echo '</div>';
}

/**
 * Render footer address.
 */
function rhino_footer_address() {
	$address = rhino_footer_contact_text( 'footer_adress' );

	if ( '' === $address ) {
		return;
	}

	printf(
		'<p class="site-footer__address">%s</p>',
		esc_html( $address )
	);
}

/**
 * Render footer social links repeater.
 */
function rhino_footer_social() {
	if ( ! function_exists( 'have_rows' ) ) {
		return;
	}

	$contexts = array( 'footer-options', 'acf-options-footer', 'option' );
	$context  = null;

	foreach ( $contexts as $option_context ) {
		if ( have_rows( 'footer_social_links', $option_context ) ) {
			$context = $option_context;
			break;
		}
	}

	if ( ! $context ) {
		return;
	}

	echo '<ul class="site-footer__social">';

	while ( have_rows( 'footer_social_links', $context ) ) {
		the_row();

		$link = get_sub_field( 'link' );
		$icon = get_sub_field( 'icon' );

		$url    = '';
		$target = '_blank';

		if ( is_array( $link ) ) {
			$url    = $link['url'] ?? '';
			$target = ! empty( $link['target'] ) ? $link['target'] : '_blank';
		} elseif ( is_string( $link ) ) {
			$url = $link;
		}

		$icon_url = rhino_acf_image_url( $icon );

		if ( ! $url || ! $icon_url ) {
			continue;
		}

		printf(
			'<li class="site-footer__social-item"><a class="site-footer__social-link" href="%1$s" target="%2$s" rel="noopener noreferrer" aria-label="%3$s"><span class="site-footer__social-icon" style="--social-icon-url: url(\'%4$s\')" aria-hidden="true"></span></a></li>',
			esc_url( $url ),
			esc_attr( $target ),
			esc_attr__( 'Social link', 'rhino' ),
			esc_url( $icon_url )
		);
	}

	echo '</ul>';
}

/**
 * Render footer bottom text (WYSIWYG).
 */
function rhino_footer_bottom_text() {
	$text = rhino_footer_html( 'footer_bottom_text' );

	if ( '' === $text ) {
		return;
	}

	printf(
		'<div class="site-footer__bottom-text">%s</div>',
		wp_kses_post( $text )
	);
}

/**
 * Render footer navigation menu.
 */
function rhino_footer_nav_menu() {
	wp_nav_menu(
		array(
			'theme_location' => 'Main-footer-menu',
			'menu_class'     => 'site-footer__menu',
			'container'      => false,
			'fallback_cb'    => false,
			'depth'          => 1,
		)
	);
}

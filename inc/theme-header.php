<?php
/**
 * Header helpers (ACF Header Options).
 *
 * @package RHINO
 */

/**
 * Check if ACF value is empty.
 *
 * @param mixed $value Field value.
 * @return bool
 */
function rhino_acf_value_is_empty( $value ) {
	if ( null === $value || false === $value || '' === $value ) {
		return true;
	}

	if ( is_array( $value ) ) {
		return empty( array_filter( $value ) );
	}

	return false;
}

/**
 * Get ACF field from Header Options page.
 *
 * @param string $field Field name.
 * @return mixed
 */
function rhino_get_header_field( $field ) {
	$contexts = array( 'header-options', 'acf-options-header', 'option' );

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
 * Get image URL from ACF image field (array, ID, or URL string).
 *
 * @param mixed $value ACF image value.
 * @return string
 */
function rhino_acf_image_url( $value ) {
	if ( is_array( $value ) ) {
		if ( ! empty( $value['url'] ) ) {
			return (string) $value['url'];
		}

		if ( ! empty( $value['ID'] ) ) {
			$url = wp_get_attachment_image_url( (int) $value['ID'], 'full' );
			return $url ? $url : '';
		}
	}

	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, 'full' );
		return $url ? $url : '';
	}

	return is_string( $value ) ? $value : '';
}

/**
 * Get plain text from ACF text or group field.
 *
 * @param mixed $value ACF value.
 * @return string
 */
function rhino_acf_text( $value ) {
	if ( is_string( $value ) || is_numeric( $value ) ) {
		return trim( (string) $value );
	}

	if ( ! is_array( $value ) ) {
		return '';
	}

	$keys = array( 'phone', 'number', 'tel', 'email', 'value', 'label', 'title', 'text' );

	foreach ( $keys as $key ) {
		if ( ! empty( $value[ $key ] ) && ( is_string( $value[ $key ] ) || is_numeric( $value[ $key ] ) ) ) {
			return trim( (string) $value[ $key ] );
		}
	}

	return '';
}

/**
 * Get phone display text from ACF text, group, or link field.
 *
 * @param mixed $value ACF value.
 * @return string
 */
function rhino_acf_phone_text( $value ) {
	$text = rhino_acf_text( $value );

	if ( '' !== $text ) {
		return $text;
	}

	if ( ! is_array( $value ) ) {
		return '';
	}

	if ( ! empty( $value['url'] ) && is_string( $value['url'] ) && 0 === strpos( $value['url'], 'tel:' ) ) {
		return trim( str_replace( 'tel:', '', $value['url'] ) );
	}

	return '';
}

/**
 * Normalize ACF link or button group to url/title/target.
 *
 * @param mixed $value ACF value.
 * @return array|null
 */
function rhino_acf_link( $value ) {
	if ( is_string( $value ) && '' !== trim( $value ) ) {
		return array(
			'url'    => trim( $value ),
			'title'  => '',
			'target' => '_self',
		);
	}

	if ( ! is_array( $value ) ) {
		return null;
	}

	if ( ! empty( $value['url'] ) ) {
		return array(
			'url'    => (string) $value['url'],
			'title'  => (string) ( $value['title'] ?? $value['text'] ?? '' ),
			'target' => ! empty( $value['target'] ) ? (string) $value['target'] : '_self',
		);
	}

	$url_keys   = array( 'button_url', 'link', 'url' );
	$title_keys = array( 'button_text', 'button_label', 'title', 'text', 'label' );

	$url   = '';
	$title = '';

	foreach ( $url_keys as $key ) {
		if ( ! empty( $value[ $key ] ) && is_string( $value[ $key ] ) ) {
			$url = $value[ $key ];
			break;
		}
	}

	foreach ( $title_keys as $key ) {
		if ( ! empty( $value[ $key ] ) && is_string( $value[ $key ] ) ) {
			$title = $value[ $key ];
			break;
		}
	}

	if ( ! $url && ! $title ) {
		return null;
	}

	return array(
		'url'    => $url,
		'title'  => $title,
		'target' => ! empty( $value['target'] ) ? (string) $value['target'] : '_self',
	);
}

/**
 * Format phone number for tel: link.
 *
 * @param string $phone Raw phone string.
 * @return string
 */
function rhino_format_phone_href( $phone ) {
	$phone  = rhino_acf_text( $phone );
	$digits = preg_replace( '/\D+/', '', $phone );

	if ( 10 === strlen( $digits ) ) {
		$digits = '1' . $digits;
	}

	return $digits ? 'tel:+' . ltrim( $digits, '+' ) : '';
}

/**
 * Render header logo markup.
 */
function rhino_header_logo() {
	$logo = rhino_get_header_field( 'header_logo' );
	$url  = rhino_acf_image_url( $logo );

	if ( ! $url ) {
		return;
	}

	$alt  = is_array( $logo ) ? (string) ( $logo['alt'] ?? '' ) : '';
	$home = esc_url( home_url( '/' ) );

	printf(
		'<a class="site-header__logo-link" href="%1$s" rel="home"><img class="site-header__logo" src="%2$s" alt="%3$s" width="250" height="51" decoding="async"></a>',
		$home,
		esc_url( $url ),
		esc_attr( $alt ?: get_bloginfo( 'name' ) )
	);
}

/**
 * Render primary navigation.
 *
 * @param array $args Optional wp_nav_menu arguments.
 */
function rhino_header_nav_menu( $args = array() ) {
	$defaults = array(
		'theme_location' => 'Main-menu',
		'menu_id'        => 'primary-menu',
		'menu_class'     => 'site-header__menu',
		'container'      => false,
		'fallback_cb'    => false,
		'depth'          => 2,
	);

	if ( class_exists( 'Rhino_Nav_Walker' ) ) {
		$defaults['walker'] = new Rhino_Nav_Walker();
	}

	wp_nav_menu(
		wp_parse_args( $args, $defaults )
	);
}

/**
 * Get email from ACF text or group field.
 *
 * @param mixed $value ACF value.
 * @return string
 */
function rhino_acf_email( $value ) {
	$text = rhino_acf_text( $value );

	if ( '' === $text ) {
		return '';
	}

	$sanitized = sanitize_email( $text );

	return is_email( $sanitized ) ? $sanitized : $text;
}

/**
 * Render phone block.
 *
 * @param bool $show_icon Whether to show the phone icon.
 */
function rhino_header_phone( $show_icon = true ) {
	$phone_raw  = rhino_get_header_field( 'header_phone' );
	$phone      = rhino_acf_phone_text( $phone_raw );
	$phone_icon = rhino_get_header_field( 'header_phone_icon' );

	if ( '' === $phone ) {
		return;
	}

	$icon_url = $show_icon ? rhino_acf_image_url( $phone_icon ) : '';
	$href     = rhino_format_phone_href( $phone );
	$class    = 'site-header__phone';

	if ( ! $show_icon ) {
		$class .= ' site-header__phone--no-icon';
	}

	printf(
		'<a class="%1$s" href="%2$s">',
		esc_attr( $class ),
		esc_url( $href ?: '#' )
	);

	if ( $icon_url ) {
		printf(
			'<span class="site-header__phone-icon" style="--phone-icon-url: url(\'%s\')" aria-hidden="true"></span>',
			esc_url( $icon_url )
		);
	}

	printf(
		'<span class="site-header__phone-text">%s</span></a>',
		esc_html( $phone )
	);
}

/**
 * Render email block (text only, no icon).
 */
function rhino_header_email() {
	$email = rhino_acf_email( rhino_get_header_field( 'header_email' ) );

	if ( '' === $email ) {
		return;
	}

	printf(
		'<a class="site-header__email" href="mailto:%1$s">%2$s</a>',
		esc_attr( $email ),
		esc_html( $email )
	);
}

/**
 * Render CTA button from ACF link or group field.
 *
 * @param string $extra_class Additional CSS class(es).
 */
function rhino_header_button( $extra_class = '' ) {
	$button = rhino_acf_link( rhino_get_header_field( 'header_button' ) );

	if ( empty( $button ) ) {
		return;
	}

	$url    = $button['url'] ?? '';
	$title  = $button['title'] ?? '';
	$target = $button['target'] ?? '_self';

	if ( ! $url || ! $title ) {
		return;
	}

	$class = 'site-header__button';

	if ( $extra_class ) {
		$class .= ' ' . $extra_class;
	}

	printf(
		'<a class="%1$s" href="%2$s" target="%3$s"%4$s><span class="site-header__button-text">%5$s</span><span class="site-header__button-icon" aria-hidden="true"></span></a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $target ),
		'_blank' === $target ? ' rel="noopener noreferrer"' : '',
		esc_html( $title )
	);
}

/**
 * Render mobile CTA button from ACF header_button_mobile field.
 */
function rhino_header_button_mobile() {
	$button = rhino_acf_link( rhino_get_header_field( 'header_button_mobile' ) );

	if ( empty( $button ) ) {
		$button = rhino_acf_link( rhino_get_header_field( 'header_button' ) );
	}

	if ( empty( $button ) ) {
		return;
	}

	$url    = $button['url'] ?? '';
	$title  = $button['title'] ?? '';
	$target = $button['target'] ?? '_self';

	if ( ! $url || ! $title ) {
		return;
	}

	printf(
		'<a class="site-header__button site-header__button--mobile" href="%1$s" target="%2$s"%3$s><span class="site-header__button-text">%4$s</span><span class="site-header__button-icon site-header__button-icon--rotate" aria-hidden="true"></span></a>',
		esc_url( $url ),
		esc_attr( $target ),
		'_blank' === $target ? ' rel="noopener noreferrer"' : '',
		esc_html( $title )
	);
}

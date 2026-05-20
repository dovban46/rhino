<?php
/**
 * Contact Form 7 tweaks for RHINO theme.
 *
 * @package RHINO
 */

/**
 * Disable CF7 auto-paragraphs and <br> insertion in form markup.
 *
 * @param bool $autop Whether to use autop.
 * @return bool
 */
function rhino_wpcf7_disable_autop( $autop ) {
	return false;
}
add_filter( 'wpcf7_autop_or_not', 'rhino_wpcf7_disable_autop' );

/**
 * Replace CF7 submit <input> with <button> so icon markup can be used.
 *
 * @param string $content Form HTML.
 * @return string
 */
function rhino_wpcf7_submit_button_icon( $content ) {
	if ( false === strpos( $content, 'rhino-cf7-form' ) ) {
		return $content;
	}

	$content = preg_replace_callback(
		'/<input\b([^>]*\brhino-cf7-form__submit\b[^>]*)>/i',
		'rhino_wpcf7_replace_submit_input',
		$content
	);

	return $content;
}
add_filter( 'wpcf7_form_elements', 'rhino_wpcf7_submit_button_icon' );

/**
 * Callback: build button HTML from submit input.
 *
 * @param array $matches Regex matches.
 * @return string
 */
function rhino_wpcf7_replace_submit_input( $matches ) {
	$attrs = $matches[1];

	if ( ! preg_match( '/\btype=(["\'])submit\1/i', $attrs ) ) {
		return $matches[0];
	}

	$value = __( 'SEND REQUEST', 'rhino' );

	if ( preg_match( '/\bvalue=(["\'])(.*?)\1/i', $attrs, $value_match ) ) {
		$value = $value_match[2];
		$attrs   = preg_replace( '/\s*value=(["\'])(.*?)\1/i', '', $attrs );
	}

	$attrs = preg_replace( '/\s*type=(["\'])submit\1/i', '', $attrs );

	return sprintf(
		'<button type="submit"%s><span class="rhino-cf7-form__submit-text">%s</span><span class="rhino-cf7-form__submit-icon" aria-hidden="true"></span></button>',
		$attrs,
		esc_html( $value )
	);
}

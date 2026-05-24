<?php
/**
 * ACF Block: Portfolio Banner
 *
 * @package RHINO
 */

$section = get_sub_field( 'portfolio_baner_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['portfolio_baner_top_text'] ?? '' ) );
$title    = $section['portfolio_baner_title'] ?? '';
$button   = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $section['portfolio_baner_button'] ?? null ) : null;

if ( ! $top_text && ! $title && empty( $button['url'] ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'portfolio-baner-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-portfolio-baner>
	<div class="portfolio-baner-section__inner">
		<?php if ( $top_text ) : ?>
			<p class="portfolio-baner-section__top-text"><?php echo esc_html( $top_text ); ?></p>
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<h2 class="portfolio-baner-section__title"><?php echo wp_kses_post( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $button['url'] ) ) : ?>
			<a
				class="portfolio-baner-section__button"
				href="<?php echo esc_url( $button['url'] ); ?>"
				target="<?php echo esc_attr( $button['target'] ); ?>"
				<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
			>
				<?php if ( ! empty( $button['title'] ) ) : ?>
					<span class="portfolio-baner-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
				<?php endif; ?>
				<span class="portfolio-baner-section__button-icon" aria-hidden="true"></span>
			</a>
		<?php endif; ?>
	</div>
</section>

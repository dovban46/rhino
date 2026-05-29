<?php
/**
 * ACF Block: Hero
 *
 * @package RHINO
 */

$hero = get_sub_field( 'hero_section' );

if ( empty( $hero ) || ! is_array( $hero ) ) {
	return;
}

$bg_image    = $hero['hero_image_bg'] ?? null;
$text_bg     = trim( (string) ( $hero['hero_text_bg'] ?? '' ) );
$top_text    = trim( (string) ( $hero['hero_top_text'] ?? '' ) );
$title       = $hero['hero_title'] ?? '';
$bottom_text = $hero['hero_bottom_text'] ?? '';
$button      = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $hero['hero_button'] ?? null ) : null;

$bg_url = function_exists( 'rhino_acf_image_url' ) ? rhino_acf_image_url( $bg_image ) : '';

if ( $bg_url && function_exists( 'rhino_preload_hero_background' ) ) {
	rhino_preload_hero_background( $bg_url );
}

if ( ! $bg_url && ! $title && ! $top_text && ! $bottom_text && ! $button ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes = 'hero-section rhino-hero hero-section--play-on-load' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $bg_url ) : ?>
		<img
			class="hero-section__bg-image"
			src="<?php echo esc_url( $bg_url ); ?>"
			alt=""
			decoding="async"
			fetchpriority="high"
		/>
	<?php endif; ?>

	<span class="hero-section__overlay" aria-hidden="true"></span>

	<?php if ( $text_bg ) : ?>
		<div class="hero-section__bg-text" aria-hidden="true"><?php echo esc_html( $text_bg ); ?></div>
	<?php endif; ?>

	<div class="hero-section__container">
		<div class="hero-section__content">
			<?php if ( $top_text ) : ?>
				<div class="hero-section__top hero-section__reveal">
					<span class="hero-section__top-line" aria-hidden="true"></span>
					<span class="hero-section__top-text"><?php echo esc_html( $top_text ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $title ) : ?>
				<div class="hero-section__title-wrap hero-section__reveal--title">
					<h1 class="hero-section__title"><?php echo wp_kses_post( $title ); ?></h1>
				</div>
			<?php endif; ?>

			<?php if ( $bottom_text ) : ?>
				<div class="hero-section__bottom-text hero-section__reveal"><?php echo wp_kses_post( $bottom_text ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $button['url'] ) && ! empty( $button['title'] ) ) : ?>
				<a
					class="hero-section__button hero-section__reveal"
					href="<?php echo esc_url( $button['url'] ); ?>"
					target="<?php echo esc_attr( $button['target'] ); ?>"
					<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
				>
					<span class="hero-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
					<span class="hero-section__button-icon" aria-hidden="true"></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

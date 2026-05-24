<?php
/**
 * ACF Block: Our Story
 *
 * @package RHINO
 */

$section = get_sub_field( 'our_story_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['our_story_top_text'] ?? '' ) );
$title    = $section['our_story_title'] ?? '';
$text     = $section['our_story_text'] ?? '';

if ( ! $top_text && ! $title && ! $text ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'our-story-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-our-story>
	<div class="our-story-section__inner">
		<div class="our-story-section__layout">
			<div class="our-story-section__head">
				<?php if ( $top_text ) : ?>
					<div class="our-story-section__top">
						<span class="our-story-section__top-line" aria-hidden="true"></span>
						<span class="our-story-section__top-text"><?php echo esc_html( $top_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="our-story-section__title"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>
			</div>

			<?php if ( $text ) : ?>
				<div class="our-story-section__text"><?php echo wp_kses_post( $text ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>

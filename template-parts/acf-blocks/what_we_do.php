<?php
/**
 * ACF Block: What We Do
 *
 * @package RHINO
 */

$section = get_sub_field( 'what_we_do_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['what_we_do_top_text'] ?? '' ) );
$title    = $section['what_we_do_title'] ?? '';
$text     = $section['what_we_do_text'] ?? '';
$items    = $section['what_we_do_items'] ?? array();

if ( ! $top_text && ! $title && ! $text && empty( $items ) ) {
	return;
}

$block       = get_acf_block_options();
$section_id  = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes     = 'what-we-do-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
$icon_url    = get_template_directory_uri() . '/assets/images/checkmark.svg';
$has_content = $top_text || $title || $text;
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-what-we-do>
	<div class="what-we-do-section__inner">
		<div class="what-we-do-section__layout">
			<?php if ( $has_content ) : ?>
				<div class="what-we-do-section__content">
					<?php if ( $top_text ) : ?>
						<div class="what-we-do-section__top">
							<span class="what-we-do-section__top-line" aria-hidden="true"></span>
							<span class="what-we-do-section__top-text"><?php echo esc_html( $top_text ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<h2 class="what-we-do-section__title"><?php echo wp_kses_post( $title ); ?></h2>
					<?php endif; ?>

					<?php if ( $text ) : ?>
						<div class="what-we-do-section__text"><?php echo wp_kses_post( $text ); ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $items ) && is_array( $items ) ) : ?>
				<ul class="what-we-do-section__list">
					<?php
					$index = 0;

					foreach ( $items as $item ) :
						if ( ! is_array( $item ) ) {
							continue;
						}

						$label = trim( (string) ( $item['item_label'] ?? '' ) );

						if ( ! $label ) {
							continue;
						}

						++$index;
						?>
						<li class="what-we-do-section__item what-we-do-section__reveal">
							<div class="what-we-do-section__item-row">
								<span class="what-we-do-section__index" aria-hidden="true">
									/ <?php echo esc_html( str_pad( (string) $index, 2, '0', STR_PAD_LEFT ) ); ?>
								</span>
								<img
									class="what-we-do-section__icon"
									src="<?php echo esc_url( $icon_url ); ?>"
									alt=""
									width="24"
									height="24"
									loading="lazy"
									decoding="async"
								/>
								<span class="what-we-do-section__label"><?php echo esc_html( $label ); ?></span>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
/**
 * ACF Block: Why Choose
 *
 * @package RHINO
 */

$section = get_sub_field( 'why_choose_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['why_choose_top_text'] ?? '' ) );
$title    = $section['why_choose_title'] ?? '';
$items    = $section['why_choose_items'] ?? array();

if ( ! $top_text && ! $title && empty( $items ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'why-choose-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="why-choose-section__inner">
		<?php if ( $top_text ) : ?>
			<div class="why-choose-section__top">
				<span class="why-choose-section__top-line" aria-hidden="true"></span>
				<span class="why-choose-section__top-text"><?php echo esc_html( $top_text ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<h2 class="why-choose-section__title"><?php echo wp_kses_post( $title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $items ) && is_array( $items ) ) : ?>
			<ul class="why-choose-section__grid">
				<?php
				foreach ( $items as $item ) :
					if ( ! is_array( $item ) ) {
						continue;
					}

					$icon_url = function_exists( 'rhino_acf_image_url' ) ? rhino_acf_image_url( $item['item_icon'] ?? null ) : '';
					$item_title = trim( (string) ( $item['item_title'] ?? '' ) );
					$item_text  = $item['item_text'] ?? '';

					if ( ! $icon_url && ! $item_title && ! $item_text ) {
						continue;
					}
					?>
					<li class="why-choose-section__item why-choose-section__reveal">
						<?php if ( $icon_url ) : ?>
							<div class="why-choose-section__icon-wrap">
								<img
									class="why-choose-section__icon"
									src="<?php echo esc_url( $icon_url ); ?>"
									alt=""
									width="35"
									height="35"
									loading="lazy"
									decoding="async"
								/>
							</div>
						<?php endif; ?>

						<?php if ( $item_title ) : ?>
							<h3 class="why-choose-section__item-title"><?php echo esc_html( $item_title ); ?></h3>
						<?php endif; ?>

						<?php if ( $item_text ) : ?>
							<div class="why-choose-section__item-text"><?php echo wp_kses_post( $item_text ); ?></div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>

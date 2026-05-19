<?php
/**
 * ACF Block: Stats
 *
 * @package RHINO
 */

$stats = get_sub_field( 'stats_section' );

if ( empty( $stats ) || ! is_array( $stats ) ) {
	return;
}

$items = $stats['stats_items'] ?? array();

if ( empty( $items ) || ! is_array( $items ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'stats-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
$total      = count( $items );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="stats-section__inner">
		<ul class="stats-section__list">
			<?php
			foreach ( $items as $index => $item ) :
				if ( ! is_array( $item ) ) {
					continue;
				}

				$number       = $item['number'] ?? null;
				$prefix       = trim( (string) ( $item['prefix'] ?? '' ) );
				$prefix_image = function_exists( 'rhino_acf_image_url' ) ? rhino_acf_image_url( $item['prefix_image'] ?? null ) : '';
				$label        = trim( (string) ( $item['text'] ?? '' ) );
				$is_last      = ( $index + 1 ) === $total;

				if ( '' === $number && '' === $prefix && '' === $prefix_image && '' === $label ) {
					continue;
				}

				$has_number = '' !== $number && null !== $number;
				$has_prefix = '' !== $prefix;
				$has_image  = '' !== $prefix_image;

				$item_classes = 'stats-section__item';
				if ( $is_last ) {
					$item_classes .= ' stats-section__item--accent';
				}

				$count_value = $has_number ? (string) $number : '';
				?>
				<li class="<?php echo esc_attr( $item_classes ); ?>">
					<div class="stats-section__item-inner">
						<?php if ( $has_number || $has_prefix || $has_image ) : ?>
							<div class="stats-section__value">
								<?php if ( $has_number ) : ?>
									<span
										class="stats-section__number"
										data-count="<?php echo esc_attr( $count_value ); ?>"
									>0</span>
								<?php endif; ?>

								<?php if ( $has_prefix ) : ?>
									<span class="stats-section__prefix"><?php echo esc_html( $prefix ); ?></span>
								<?php endif; ?>

								<?php if ( $has_image ) : ?>
									<img
										class="stats-section__prefix-image"
										src="<?php echo esc_url( $prefix_image ); ?>"
										alt=""
										width="45"
										height="45"
										loading="lazy"
										decoding="async"
									/>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $label ) : ?>
							<p class="stats-section__label"><?php echo esc_html( $label ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

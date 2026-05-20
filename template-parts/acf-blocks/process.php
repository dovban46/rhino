<?php
/**
 * ACF Block: Process
 *
 * @package RHINO
 */

$section = get_sub_field( 'process_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['process_top_text'] ?? '' ) );
$title    = $section['process_title'] ?? '';
$text     = $section['process_text'] ?? '';
$items    = $section['process_items'] ?? array();

if ( ! $top_text && ! $title && ! $text && empty( $items ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'process-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="process-section__inner">
		<?php if ( $top_text ) : ?>
			<div class="process-section__top">
				<span class="process-section__top-line" aria-hidden="true"></span>
				<span class="process-section__top-text"><?php echo esc_html( $top_text ); ?></span>
			</div>
		<?php endif; ?>

		<?php if ( $title || $text ) : ?>
			<div class="process-section__intro">
				<?php if ( $title ) : ?>
					<h2 class="process-section__title"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $text ) : ?>
					<div class="process-section__text"><?php echo wp_kses_post( $text ); ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $items ) && is_array( $items ) ) : ?>
			<ul class="process-section__grid">
				<?php
				$index = 0;
				foreach ( $items as $item ) :
					if ( ! is_array( $item ) ) {
						continue;
					}

					$item_title = trim( (string) ( $item['item_title'] ?? '' ) );
					$item_text  = $item['item_text'] ?? '';

					if ( ! $item_title && ! $item_text ) {
						continue;
					}

					++$index;
					?>
					<li class="process-section__item process-section__reveal">
						<span class="process-section__number"><?php echo esc_html( sprintf( '%02d', $index ) ); ?></span>

						<?php if ( $item_title ) : ?>
							<h3 class="process-section__item-title"><?php echo esc_html( $item_title ); ?></h3>
						<?php endif; ?>

						<?php if ( $item_text ) : ?>
							<div class="process-section__item-text"><?php echo wp_kses_post( $item_text ); ?></div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>

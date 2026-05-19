<?php
/**
 * ACF Block: Run Line
 *
 * @package RHINO
 */

$run_line = get_sub_field( 'run_line_section' );

if ( empty( $run_line ) || ! is_array( $run_line ) ) {
	return;
}

$items = $run_line['run_line_items'] ?? array();

if ( empty( $items ) || ! is_array( $items ) ) {
	return;
}

$labels = array();

foreach ( $items as $item ) {
	if ( ! is_array( $item ) ) {
		continue;
	}

	$label = trim( (string) ( $item['label'] ?? '' ) );

	if ( '' !== $label ) {
		$labels[] = $label;
	}
}

if ( empty( $labels ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'run-line-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="run-line-section__inner">
		<div class="run-line-section__viewport">
			<div class="run-line-section__track">
				<div class="run-line-section__group">
					<?php foreach ( $labels as $label ) : ?>
						<span class="run-line-section__item">
							<span class="run-line-section__label"><?php echo esc_html( $label ); ?></span>
							<span class="run-line-section__separator" aria-hidden="true"></span>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
/**
 * Recent category work card partial.
 *
 * @package RHINO
 *
 * @var WP_Post $service_post Service post.
 * @var int     $index        1-based slide index.
 */

if ( empty( $args['service_post'] ) || ! $args['service_post'] instanceof WP_Post ) {
	return;
}

$service_post = $args['service_post'];
$index        = isset( $args['index'] ) ? (int) $args['index'] : 0;

if ( $index < 1 ) {
	return;
}

$image_before = rhino_acf_image_url( rhino_get_service_post_field( $service_post->ID, 'image_before' ) );
$image_after  = rhino_acf_image_url( rhino_get_service_post_field( $service_post->ID, 'image_after' ) );
$text         = rhino_get_service_post_field( $service_post->ID, 'text' );

if ( is_array( $text ) ) {
	$text = '';
}

$text = trim( (string) $text );
$num  = sprintf( '/ %02d', $index );
?>
<article
	class="recent-category-work-section__card recent-category-work-section__card--clickable"
	data-recent-category-work-card
	data-after-image="<?php echo esc_url( $image_after ); ?>"
	data-slide-index="<?php echo esc_attr( (string) ( $index - 1 ) ); ?>"
>
	<div class="recent-category-work-section__compare" data-recent-category-work-compare style="--compare-position: 50%;">
		<div class="recent-category-work-section__compare-media">
			<div class="recent-category-work-section__compare-labels">
				<span class="recent-category-work-section__compare-label recent-category-work-section__compare-label--before"><?php esc_html_e( 'BEFORE', 'rhino' ); ?></span>
				<span class="recent-category-work-section__compare-label recent-category-work-section__compare-label--after"><?php esc_html_e( 'AFTER', 'rhino' ); ?></span>
			</div>
			<img
				class="recent-category-work-section__compare-image recent-category-work-section__compare-image--after"
				src="<?php echo esc_url( $image_after ); ?>"
				alt=""
				width="364"
				height="352"
				loading="lazy"
				decoding="async"
			/>

			<div class="recent-category-work-section__compare-before-clip">
				<img
					class="recent-category-work-section__compare-image recent-category-work-section__compare-image--before"
					src="<?php echo esc_url( $image_before ); ?>"
					alt=""
					width="364"
					height="352"
					loading="lazy"
					decoding="async"
				/>
			</div>

			<div class="recent-category-work-section__compare-divider" data-recent-category-work-divider>
				<button
					type="button"
					class="recent-category-work-section__compare-handle"
					data-recent-category-work-handle
					aria-label="<?php esc_attr_e( 'Drag to compare before and after', 'rhino' ); ?>"
				>
					<span class="recent-category-work-section__compare-handle-arrows" aria-hidden="true">
						<span class="recent-category-work-section__compare-handle-arrow recent-category-work-section__compare-handle-arrow--left"></span>
						<span class="recent-category-work-section__compare-handle-arrow recent-category-work-section__compare-handle-arrow--right"></span>
					</span>
				</button>
			</div>
		</div>
	</div>

	<div class="recent-category-work-section__card-body">
		<span class="recent-category-work-section__card-num"><?php echo esc_html( $num ); ?></span>

		<?php if ( $text ) : ?>
			<p class="recent-category-work-section__card-text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>
</article>

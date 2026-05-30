<?php
/**
 * All Works card partial.
 *
 * @package RHINO
 *
 * @var WP_Post  $service_post Service post.
 * @var int      $index        1-based slide index.
 * @var WP_Term|null $term     Category term for label.
 */

if ( empty( $args['service_post'] ) || ! $args['service_post'] instanceof WP_Post ) {
	return;
}

$service_post = $args['service_post'];
$index        = isset( $args['index'] ) ? (int) $args['index'] : 0;
$term         = $args['term'] ?? null;

if ( $index < 1 ) {
	return;
}

if ( ! $term instanceof WP_Term ) {
	$term = function_exists( 'rhino_get_service_post_primary_term' )
		? rhino_get_service_post_primary_term( $service_post->ID )
		: null;
}

$image_after = function_exists( 'rhino_acf_image_url' )
	? rhino_acf_image_url( rhino_get_service_post_field( $service_post->ID, 'image_after' ) )
	: '';
$text        = rhino_get_service_post_field( $service_post->ID, 'text' );

if ( is_array( $text ) ) {
	$text = '';
}

$text = trim( (string) $text );

if ( ! $image_after ) {
	return;
}

$category_label = $term instanceof WP_Term
	? rhino_get_term_short_label( $term )
	: '';
?>
<article
	class="all-works-section__card all-works-section__card--clickable"
	data-all-works-card
	data-after-image="<?php echo esc_url( $image_after ); ?>"
	data-slide-index="<?php echo esc_attr( (string) ( $index - 1 ) ); ?>"
>
	<div
		class="all-works-section__media"
		role="button"
		tabindex="0"
		aria-label="<?php echo esc_attr( $category_label ? sprintf( /* translators: %s: project category */ __( 'Open %s project gallery', 'rhino' ), $category_label ) : __( 'Open project gallery', 'rhino' ) ); ?>"
	>
		<img
			class="all-works-section__image"
			src="<?php echo esc_url( $image_after ); ?>"
			alt=""
			width="364"
			height="352"
			loading="lazy"
			decoding="async"
		/>
	</div>

	<div class="all-works-section__card-body">
		<?php if ( $category_label ) : ?>
			<span class="all-works-section__card-category"><?php echo esc_html( $category_label ); ?></span>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="all-works-section__card-text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>
</article>

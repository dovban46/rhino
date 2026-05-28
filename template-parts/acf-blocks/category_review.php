<?php
/**
 * ACF Block: Category Review
 *
 * @package RHINO
 */

$section = get_sub_field( 'category_review_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text    = trim( (string) ( $section['category_review_top_text'] ?? '' ) );
$rating_raw  = $section['category_review_rating'] ?? 0;
$text        = $section['category_review_text'] ?? '';
$name        = trim( (string) ( $section['category_review_name'] ?? '' ) );
$description = trim( (string) ( $section['category_review_description'] ?? '' ) );

$rating = (int) round( (float) $rating_raw );
$rating = max( 0, min( 5, $rating ) );

$has_text        = is_string( $text ) && trim( wp_strip_all_tags( $text ) ) !== '';
$has_meta        = $name || $description;
$has_rating      = $rating > 0;
$has_top_text    = (bool) $top_text;
$has_review_body = $has_text || $has_meta;

if ( ! $has_top_text && ! $has_rating && ! $has_review_body ) {
	return;
}

$star_filled_url = get_template_directory_uri() . '/assets/images/Star.svg';
$star_empty_url  = get_template_directory_uri() . '/assets/images/Star-empty.svg';

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'category-review-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-category-review>
	<div class="category-review-section__inner">
		<div class="category-review-section__layout">
			<?php if ( $has_top_text || $has_rating ) : ?>
				<div class="category-review-section__aside">
					<?php if ( $has_top_text ) : ?>
						<div class="category-review-section__top category-review-section__reveal">
							<span class="category-review-section__top-line" aria-hidden="true"></span>
							<span class="category-review-section__top-text"><?php echo esc_html( $top_text ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( $has_rating ) : ?>
						<ul
							class="category-review-section__stars category-review-section__reveal"
							role="img"
							aria-label="<?php echo esc_attr( sprintf( __( 'Rating: %1$d out of 5', 'rhino' ), $rating ) ); ?>"
						>
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
								<li class="category-review-section__star">
									<img
										class="category-review-section__star-icon"
										src="<?php echo esc_url( $i <= $rating ? $star_filled_url : $star_empty_url ); ?>"
										alt=""
										width="24"
										height="24"
										decoding="async"
									/>
								</li>
							<?php endfor; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $has_review_body ) : ?>
				<div class="category-review-section__content">
					<?php if ( $has_text ) : ?>
						<div class="category-review-section__quote category-review-section__reveal">
							<?php echo wp_kses_post( $text ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $has_meta ) : ?>
						<div class="category-review-section__meta category-review-section__reveal">
							<?php if ( $name ) : ?>
								<span class="category-review-section__name"><?php echo esc_html( $name ); ?></span>
							<?php endif; ?>

							<?php if ( $description ) : ?>
								<span class="category-review-section__description"><?php echo esc_html( $description ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

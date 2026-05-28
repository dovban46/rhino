<?php
/**
 * ACF Block: Reviews
 *
 * @package RHINO
 */

$section = get_sub_field( 'reviews_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text          = trim( (string) ( $section['reviews_top_text'] ?? '' ) );
$title             = $section['reviews_title'] ?? '';
$button            = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $section['reviews_button'] ?? null ) : null;
$rating            = $section['reviews_rating'] ?? null;
$text_under_rating = trim( (string) ( $section['reviews_text_under_rating'] ?? '' ) );
$review_posts = function_exists( 'rhino_get_reviews_posts' ) ? rhino_get_reviews_posts() : array();

$has_rating = '' !== $rating && null !== $rating;
$has_items  = ! empty( $review_posts );

if ( ! $top_text && ! $title && empty( $button['url'] ) && ! $has_rating && ! $has_items ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'reviews-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
$rating_val = $has_rating ? (string) $rating : '';
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="reviews-section__inner">
		<div class="reviews-section__header">
			<div class="reviews-section__header-left">
				<?php if ( $top_text ) : ?>
					<div class="reviews-section__top">
						<span class="reviews-section__top-line" aria-hidden="true"></span>
						<span class="reviews-section__top-text"><?php echo esc_html( $top_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="reviews-section__title"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>
			</div>

			<div class="reviews-section__header-right">
				<?php if ( ! empty( $button['url'] ) ) : ?>
					<a
						class="reviews-section__button"
						href="<?php echo esc_url( $button['url'] ); ?>"
						target="<?php echo esc_attr( $button['target'] ); ?>"
						<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
					>
						<?php if ( ! empty( $button['title'] ) ) : ?>
							<span class="reviews-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
						<?php endif; ?>
						<span class="reviews-section__button-icon" aria-hidden="true"></span>
					</a>
				<?php endif; ?>

				<?php if ( $has_rating || $text_under_rating ) : ?>
					<div class="reviews-section__rating-block">
						<?php if ( $has_rating ) : ?>
							<span class="reviews-section__rating" data-count="<?php echo esc_attr( $rating_val ); ?>">0</span>
							<div class="reviews-section__stars" aria-hidden="true">
								<?php rhino_render_rating_stars( $rating, 5, true ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $text_under_rating ) : ?>
							<p class="reviews-section__rating-text"><?php echo esc_html( $text_under_rating ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $has_items ) : ?>
			<div class="reviews-section__slider-area">
				<div class="reviews-section__slider-wrap">
					<div class="reviews-section__slider swiper">
						<div class="swiper-wrapper">
						<?php
						foreach ( $review_posts as $review_post ) :
							if ( ! $review_post instanceof WP_Post ) {
								continue;
							}

							$item_name = trim( get_the_title( $review_post ) );
							$item_text = trim( (string) $review_post->post_content );
							$item_city = trim( (string) ( function_exists( 'rhino_get_review_post_field' )
								? rhino_get_review_post_field( $review_post->ID, 'city' )
								: '' ) );
							$item_rating = function_exists( 'rhino_get_review_post_field' )
								? rhino_get_review_post_field( $review_post->ID, 'rating' )
								: null;

							$has_item_rating = null !== $item_rating && '' !== $item_rating;

							if ( ! $has_item_rating && ! $item_text && ! $item_name && ! $item_city ) {
								continue;
							}
							?>
							<div class="swiper-slide">
								<article class="reviews-section__card">
									<?php if ( $has_item_rating ) : ?>
										<div class="reviews-section__card-stars" aria-hidden="true">
											<?php rhino_render_rating_stars( $item_rating, 5, false ); ?>
										</div>
									<?php endif; ?>

									<div class="reviews-section__card-body">
										<?php if ( $item_text ) : ?>
											<div class="reviews-section__card-text"><?php echo wp_kses_post( $item_text ); ?></div>
										<?php endif; ?>

										<div class="reviews-section__card-footer">
											<span class="reviews-section__card-divider" aria-hidden="true"></span>

											<?php if ( $item_name ) : ?>
												<p class="reviews-section__card-name"><?php echo esc_html( $item_name ); ?></p>
											<?php endif; ?>

											<?php if ( $item_city ) : ?>
												<p class="reviews-section__card-city"><?php echo esc_html( $item_city ); ?></p>
											<?php endif; ?>
										</div>
									</div>
								</article>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="reviews-section__nav-row">
					<button class="reviews-section__nav reviews-section__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Previous review', 'rhino' ); ?>">
						<span class="reviews-section__nav-icon" aria-hidden="true"></span>
					</button>
					<button class="reviews-section__nav reviews-section__nav--next" type="button" aria-label="<?php esc_attr_e( 'Next review', 'rhino' ); ?>">
						<span class="reviews-section__nav-icon" aria-hidden="true"></span>
					</button>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

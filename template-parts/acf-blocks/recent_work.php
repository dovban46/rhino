<?php
/**
 * ACF Block: Recent Work
 *
 * @package RHINO
 */

$section = get_sub_field( 'recent_work_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['recent_work_top_text'] ?? '' ) );
$title    = $section['recent_work_title'] ?? '';
$button   = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $section['recent_work_button'] ?? null ) : null;

$recent_work_posts = function_exists( 'rhino_get_recent_work_posts' ) ? rhino_get_recent_work_posts() : array();

if ( empty( $recent_work_posts ) ) {
	return;
}

$has_button = ! empty( $button['url'] );
$has_header = $top_text || $title || $has_button;

if ( ! $has_header ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'recent-work-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
$slide_count = count( $recent_work_posts );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-recent-work data-slide-count="<?php echo esc_attr( (string) $slide_count ); ?>">
	<div class="recent-work-section__inner">
		<div class="recent-work-section__header recent-work-section__reveal">
			<div class="recent-work-section__header-left">
				<?php if ( $top_text ) : ?>
					<div class="recent-work-section__top">
						<span class="recent-work-section__top-line" aria-hidden="true"></span>
						<span class="recent-work-section__top-text"><?php echo esc_html( $top_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="recent-work-section__title"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>
			</div>

			<?php if ( $has_button ) : ?>
				<a
					class="recent-work-section__button recent-work-section__button--header"
					href="<?php echo esc_url( $button['url'] ); ?>"
					target="<?php echo esc_attr( $button['target'] ); ?>"
					<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
				>
					<?php if ( ! empty( $button['title'] ) ) : ?>
						<span class="recent-work-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
					<?php endif; ?>
					<span class="recent-work-section__button-icon" aria-hidden="true"></span>
				</a>
			<?php endif; ?>
		</div>

		<div class="recent-work-section__slider-area recent-work-section__reveal">
			<div class="swiper recent-work-section__slider">
				<div class="swiper-wrapper">
					<?php
					$index = 0;
					foreach ( $recent_work_posts as $service_post ) :
						++$index;

						$image_before = rhino_acf_image_url( rhino_get_service_post_field( $service_post->ID, 'image_before' ) );
						$image_after  = rhino_acf_image_url( rhino_get_service_post_field( $service_post->ID, 'image_after' ) );
						$text         = rhino_get_service_post_field( $service_post->ID, 'text' );

						if ( is_array( $text ) ) {
							$text = '';
						}

						$text = trim( (string) $text );
						$num  = sprintf( '/ %02d', $index );
						?>
						<div class="swiper-slide">
							<article
								class="recent-work-section__card recent-work-section__card--clickable"
								data-recent-work-card
								data-after-image="<?php echo esc_url( $image_after ); ?>"
								data-slide-index="<?php echo esc_attr( (string) ( $index - 1 ) ); ?>"
							>
								<div class="recent-work-section__compare" data-recent-work-compare style="--compare-position: 50%;">
									<div class="recent-work-section__compare-media">
										<div class="recent-work-section__compare-labels">
											<span class="recent-work-section__compare-label recent-work-section__compare-label--before"><?php esc_html_e( 'BEFORE', 'rhino' ); ?></span>
											<span class="recent-work-section__compare-label recent-work-section__compare-label--after"><?php esc_html_e( 'AFTER', 'rhino' ); ?></span>
										</div>
										<img
											class="recent-work-section__compare-image recent-work-section__compare-image--after"
											src="<?php echo esc_url( $image_after ); ?>"
											alt=""
											width="364"
											height="352"
											loading="lazy"
											decoding="async"
										/>

										<div class="recent-work-section__compare-before-clip">
											<img
												class="recent-work-section__compare-image recent-work-section__compare-image--before"
												src="<?php echo esc_url( $image_before ); ?>"
												alt=""
												width="364"
												height="352"
												loading="lazy"
												decoding="async"
											/>
										</div>

										<div class="recent-work-section__compare-divider" data-recent-work-divider>
											<button
												type="button"
												class="recent-work-section__compare-handle"
												data-recent-work-handle
												aria-label="<?php esc_attr_e( 'Drag to compare before and after', 'rhino' ); ?>"
											>
												<span class="recent-work-section__compare-handle-arrows" aria-hidden="true">
													<span class="recent-work-section__compare-handle-arrow recent-work-section__compare-handle-arrow--left"></span>
													<span class="recent-work-section__compare-handle-arrow recent-work-section__compare-handle-arrow--right"></span>
												</span>
											</button>
										</div>
									</div>
								</div>

								<div class="recent-work-section__card-body">
									<span class="recent-work-section__card-num"><?php echo esc_html( $num ); ?></span>

									<?php if ( $text ) : ?>
										<p class="recent-work-section__card-text"><?php echo esc_html( $text ); ?></p>
									<?php endif; ?>
								</div>
							</article>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="recent-work-section__progress">
				<div
					class="recent-work-section__progress-track"
					data-recent-work-pagination
					role="slider"
					aria-label="<?php esc_attr_e( 'Recent work slides', 'rhino' ); ?>"
					aria-valuemin="1"
					aria-valuemax="<?php echo esc_attr( (string) $slide_count ); ?>"
					aria-valuenow="1"
					tabindex="0"
				>
					<div class="recent-work-section__progress-fill" data-recent-work-progress></div>
				</div>
			</div>

			<?php if ( $has_button ) : ?>
				<a
					class="recent-work-section__button recent-work-section__button--footer"
					href="<?php echo esc_url( $button['url'] ); ?>"
					target="<?php echo esc_attr( $button['target'] ); ?>"
					<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
				>
					<?php if ( ! empty( $button['title'] ) ) : ?>
						<span class="recent-work-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
					<?php endif; ?>
					<span class="recent-work-section__button-icon" aria-hidden="true"></span>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="recent-work-lightbox" data-recent-work-lightbox aria-hidden="true">
		<div class="recent-work-lightbox__backdrop" data-recent-work-lightbox-close tabindex="-1"></div>

		<div
			class="recent-work-lightbox__dialog"
			role="dialog"
			aria-modal="true"
			aria-label="<?php esc_attr_e( 'Recent work gallery', 'rhino' ); ?>"
		>
			<div class="recent-work-lightbox__row">
				<button
					type="button"
					class="recent-work-lightbox__nav recent-work-lightbox__nav--prev"
					data-recent-work-lightbox-prev
					aria-label="<?php esc_attr_e( 'Previous project', 'rhino' ); ?>"
				>
					<span class="recent-work-lightbox__nav-icon" aria-hidden="true"></span>
				</button>

				<div class="recent-work-lightbox__viewport" data-recent-work-lightbox-viewport>
					<figure class="recent-work-lightbox__figure">
						<img class="recent-work-lightbox__image" src="" alt="" decoding="async" />
					</figure>
				</div>

				<button
					type="button"
					class="recent-work-lightbox__nav recent-work-lightbox__nav--next"
					data-recent-work-lightbox-next
					aria-label="<?php esc_attr_e( 'Next project', 'rhino' ); ?>"
				>
					<span class="recent-work-lightbox__nav-icon" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</div>
</section>

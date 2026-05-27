<?php
/**
 * ACF Block: Recent Category Work
 *
 * @package RHINO
 */

$section = get_sub_field( 'recent_category_work_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim(
	(string) ( function_exists( 'rhino_get_recent_category_work_section_value' )
		? rhino_get_recent_category_work_section_value( $section, 'recent_category_work_top_text' )
		: ( $section['recent_category_work_top_text'] ?? $section['recent_work_top_text'] ?? '' ) )
);
$title = function_exists( 'rhino_get_recent_category_work_section_value' )
	? rhino_get_recent_category_work_section_value( $section, 'recent_category_work_title' )
	: ( $section['recent_category_work_title'] ?? $section['recent_work_title'] ?? '' );
$button_raw = function_exists( 'rhino_get_recent_category_work_section_value' )
	? rhino_get_recent_category_work_section_value( $section, 'recent_category_work_button' )
	: ( $section['recent_category_work_button'] ?? $section['recent_work_button'] ?? null );
$button     = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $button_raw ) : null;

$term = get_query_var( 'rhino_acf_term' );

if ( ! $term instanceof WP_Term ) {
	$term = get_queried_object();
}

$recent_category_work_posts = function_exists( 'rhino_get_recent_category_work_posts' )
	? rhino_get_recent_category_work_posts( $term instanceof WP_Term ? $term : null )
	: array();

if ( empty( $recent_category_work_posts ) ) {
	return;
}

$has_button = ! empty( $button['url'] );
$has_header = $top_text || $title || $has_button;

if ( ! $has_header ) {
	return;
}

$block       = get_acf_block_options();
$section_id  = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes     = 'recent-category-work-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
$slide_count                    = count( $recent_category_work_posts );
$recent_category_work_pages     = array_chunk( $recent_category_work_posts, 6 );
$recent_category_work_page_count = count( $recent_category_work_pages );
$card_index                     = 0;
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-recent-category-work data-slide-count="<?php echo esc_attr( (string) $slide_count ); ?>" data-page-count="<?php echo esc_attr( (string) $recent_category_work_page_count ); ?>">
	<div class="recent-category-work-section__inner">
		<div class="recent-category-work-section__header recent-category-work-section__reveal">
			<div class="recent-category-work-section__header-left">
				<?php if ( $top_text ) : ?>
					<div class="recent-category-work-section__top">
						<span class="recent-category-work-section__top-line" aria-hidden="true"></span>
						<span class="recent-category-work-section__top-text"><?php echo esc_html( $top_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="recent-category-work-section__title"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $has_button ) : ?>
					<a
						class="recent-category-work-section__button recent-category-work-section__button--header"
						href="<?php echo esc_url( $button['url'] ); ?>"
						target="<?php echo esc_attr( $button['target'] ); ?>"
						<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
					>
						<?php if ( ! empty( $button['title'] ) ) : ?>
							<span class="recent-category-work-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
						<?php endif; ?>
						<span class="recent-category-work-section__button-icon" aria-hidden="true"></span>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<div class="recent-category-work-section__slider-area recent-category-work-section__reveal">
			<div class="swiper recent-category-work-section__slider recent-category-work-section__slider--desktop" data-category-work-slider="desktop">
				<div class="swiper-wrapper">
					<?php foreach ( $recent_category_work_pages as $page_posts ) : ?>
						<div class="swiper-slide">
							<div class="recent-category-work-section__grid">
								<?php
								foreach ( $page_posts as $service_post ) {
									++$card_index;
									get_template_part(
										'template-parts/acf-blocks/partials/recent-category-work',
										'card',
										array(
											'service_post' => $service_post,
											'index'        => $card_index,
										)
									);
								}
								?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="swiper recent-category-work-section__slider recent-category-work-section__slider--mobile" data-category-work-slider="mobile">
				<div class="swiper-wrapper">
					<?php
					$card_index = 0;
					foreach ( $recent_category_work_posts as $service_post ) :
						++$card_index;
						?>
						<div class="swiper-slide">
							<?php
							get_template_part(
								'template-parts/acf-blocks/partials/recent-category-work',
								'card',
								array(
									'service_post' => $service_post,
									'index'        => $card_index,
								)
							);
							?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="recent-category-work-section__progress">
				<div
					class="recent-category-work-section__progress-track"
					data-recent-category-work-pagination
					role="slider"
					aria-label="<?php esc_attr_e( 'Recent category work slides', 'rhino' ); ?>"
					aria-valuemin="1"
					aria-valuemax="<?php echo esc_attr( (string) $slide_count ); ?>"
					aria-valuenow="1"
					tabindex="0"
				>
					<div class="recent-category-work-section__progress-fill" data-recent-category-work-progress></div>
				</div>
			</div>

			<div class="recent-category-work-section__nav-row">
				<button class="recent-category-work-section__nav recent-category-work-section__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Previous projects', 'rhino' ); ?>">
					<span class="recent-category-work-section__nav-icon" aria-hidden="true"></span>
				</button>
				<button class="recent-category-work-section__nav recent-category-work-section__nav--next" type="button" aria-label="<?php esc_attr_e( 'Next projects', 'rhino' ); ?>">
					<span class="recent-category-work-section__nav-icon" aria-hidden="true"></span>
				</button>
			</div>

			<?php if ( $has_button ) : ?>
				<a
					class="recent-category-work-section__button recent-category-work-section__button--footer"
					href="<?php echo esc_url( $button['url'] ); ?>"
					target="<?php echo esc_attr( $button['target'] ); ?>"
					<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
				>
					<?php if ( ! empty( $button['title'] ) ) : ?>
						<span class="recent-category-work-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
					<?php endif; ?>
					<span class="recent-category-work-section__button-icon" aria-hidden="true"></span>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="recent-work-lightbox" data-recent-category-work-lightbox aria-hidden="true">
		<div class="recent-work-lightbox__backdrop" data-recent-category-work-lightbox-close tabindex="-1"></div>

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
					data-recent-category-work-lightbox-prev
					aria-label="<?php esc_attr_e( 'Previous project', 'rhino' ); ?>"
				>
					<span class="recent-work-lightbox__nav-icon" aria-hidden="true"></span>
				</button>

				<div class="recent-work-lightbox__viewport" data-recent-category-work-lightbox-viewport>
					<figure class="recent-work-lightbox__figure">
						<img class="recent-work-lightbox__image" src="" alt="" decoding="async" />
					</figure>
				</div>

				<button
					type="button"
					class="recent-work-lightbox__nav recent-work-lightbox__nav--next"
					data-recent-category-work-lightbox-next
					aria-label="<?php esc_attr_e( 'Next project', 'rhino' ); ?>"
				>
					<span class="recent-work-lightbox__nav-icon" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</div>
</section>

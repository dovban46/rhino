<?php
/**
 * ACF Block: Our Services
 *
 * @package RHINO
 */

$section = get_sub_field( 'our_services_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['our_services_top_text'] ?? '' ) );
$title    = $section['our_services_title'] ?? '';
$text     = $section['our_services_text'] ?? '';
$button   = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $section['our_services_button'] ?? null ) : null;

$categories = function_exists( 'rhino_get_service_category_terms' )
	? rhino_get_service_category_terms()
	: array();

$has_button = ! empty( $button['url'] );

if ( ! $top_text && ! $title && ! $text && empty( $categories ) && ! $has_button ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'our-services-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="our-services-section__inner">
		<?php if ( $top_text || $title || $text ) : ?>
			<div class="our-services-section__header our-services-section__reveal">
				<div class="our-services-section__header-left">
					<?php if ( $top_text ) : ?>
						<div class="our-services-section__top">
							<span class="our-services-section__top-line" aria-hidden="true"></span>
							<span class="our-services-section__top-text"><?php echo esc_html( $top_text ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<h2 class="our-services-section__title"><?php echo wp_kses_post( $title ); ?></h2>
					<?php endif; ?>
				</div>

				<?php if ( $text ) : ?>
					<div class="our-services-section__header-right">
						<div class="our-services-section__text"><?php echo wp_kses_post( $text ); ?></div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $categories ) ) : ?>
			<ul class="our-services-section__grid">
				<?php
				foreach ( $categories as $term ) :
					if ( ! $term instanceof WP_Term ) {
						continue;
					}

					$term_link   = get_term_link( $term );
					$term_url    = ! is_wp_error( $term_link ) ? $term_link : '';
					$image_url   = function_exists( 'rhino_get_service_category_image_url' )
						? rhino_get_service_category_image_url( $term )
						: '';
					$subtitles   = function_exists( 'rhino_get_service_category_field' )
						? rhino_get_service_category_field( $term, 'category_subtitles' )
						: '';
					$description = function_exists( 'rhino_get_service_category_description' )
						? rhino_get_service_category_description( $term )
						: '';

					$subtitles = is_string( $subtitles ) ? trim( $subtitles ) : '';

					if ( empty( $term->name ) ) {
						continue;
					}
					?>
					<li class="our-services-section__item our-services-section__reveal">
						<article class="our-services-section__card">
							<?php if ( $image_url ) : ?>
								<div class="our-services-section__card-media" aria-hidden="true">
									<img
										class="our-services-section__card-image"
										src="<?php echo esc_url( $image_url ); ?>"
										alt=""
										loading="lazy"
										decoding="async"
									/>
								</div>
							<?php endif; ?>

							<?php if ( $term_url ) : ?>
								<a
									class="our-services-section__card-action"
									href="<?php echo esc_url( $term_url ); ?>"
									aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'rhino' ), $term->name ) ); ?>"
								>
									<span class="our-services-section__card-action-icon" aria-hidden="true"></span>
								</a>
							<?php endif; ?>

							<div class="our-services-section__card-content">
								<?php if ( $subtitles ) : ?>
									<span class="our-services-section__card-subtitle"><?php echo esc_html( $subtitles ); ?></span>
								<?php endif; ?>

								<?php if ( $term->name ) : ?>
									<h3 class="our-services-section__card-title">
										<?php if ( $term_url ) : ?>
											<a class="our-services-section__card-title-link" href="<?php echo esc_url( $term_url ); ?>">
												<?php echo esc_html( $term->name ); ?>
											</a>
										<?php else : ?>
											<?php echo esc_html( $term->name ); ?>
										<?php endif; ?>
									</h3>
								<?php endif; ?>

								<?php if ( $description ) : ?>
									<div class="our-services-section__card-text"><?php echo wp_kses_post( $description ); ?></div>
								<?php endif; ?>
							</div>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $has_button ) : ?>
			<div class="our-services-section__footer our-services-section__reveal">
				<a
					class="our-services-section__button"
					href="<?php echo esc_url( $button['url'] ); ?>"
					target="<?php echo esc_attr( $button['target'] ); ?>"
					<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
				>
					<?php if ( ! empty( $button['title'] ) ) : ?>
						<span class="our-services-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
					<?php endif; ?>
					<span class="our-services-section__button-icon" aria-hidden="true"></span>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

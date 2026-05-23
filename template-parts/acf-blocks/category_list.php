<?php
/**
 * ACF Block: Category List
 *
 * Outputs all service-category terms (no block fields).
 *
 * @package RHINO
 */

$categories = function_exists( 'rhino_get_category_list_terms' )
	? rhino_get_category_list_terms()
	: array();

if ( empty( $categories ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'category-list-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section
	class="<?php echo esc_attr( $classes ); ?>"
	<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-category-list
	data-stack-peek="160"
	data-stack-peek-mobile="120"
>
	<div class="category-list-section__inner">
		<ul class="category-list-section__list">
			<?php
			$index = 0;

			foreach ( $categories as $term ) :
				if ( ! $term instanceof WP_Term || empty( $term->name ) ) {
					continue;
				}

				++$index;

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
				$number    = sprintf( '/ %02d', $index );
				$title     = function_exists( 'mb_convert_case' )
					? mb_convert_case( $term->name, MB_CASE_TITLE, 'UTF-8' )
					: $term->name;
				?>
				<li
					class="category-list-section__item"
					data-category-list-item
					data-item-index="<?php echo esc_attr( (string) ( $index - 1 ) ); ?>"
				>
					<article class="category-list-section__card category-list-section__card-reveal">
						<div class="category-list-section__card-top">
							<span class="category-list-section__index" aria-hidden="true"><?php echo esc_html( $number ); ?></span>

							<?php if ( $term_url ) : ?>
								<a class="category-list-section__read-more" href="<?php echo esc_url( $term_url ); ?>">
									<span class="category-list-section__read-more-text"><?php esc_html_e( 'Read more', 'rhino' ); ?></span>
									<span class="category-list-section__read-more-icon" aria-hidden="true"></span>
								</a>
							<?php endif; ?>
						</div>

						<div class="category-list-section__head">
							<?php if ( $term_url ) : ?>
								<h3 class="category-list-section__title">
									<a class="category-list-section__title-link" href="<?php echo esc_url( $term_url ); ?>">
										<?php echo esc_html( $title ); ?>
									</a>
								</h3>
							<?php else : ?>
								<h3 class="category-list-section__title"><?php echo esc_html( $title ); ?></h3>
							<?php endif; ?>

							<?php if ( $subtitles ) : ?>
								<p class="category-list-section__subtitles"><?php echo esc_html( $subtitles ); ?></p>
							<?php endif; ?>
						</div>

						<?php if ( $description || $image_url ) : ?>
							<div class="category-list-section__aside">
								<?php if ( $description ) : ?>
									<div class="category-list-section__description"><?php echo wp_kses_post( $description ); ?></div>
								<?php endif; ?>

								<?php if ( $image_url && $term_url ) : ?>
									<a
										class="category-list-section__media"
										href="<?php echo esc_url( $term_url ); ?>"
										aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'rhino' ), $title ) ); ?>"
									>
										<img
											class="category-list-section__image"
											src="<?php echo esc_url( $image_url ); ?>"
											alt=""
											loading="lazy"
											decoding="async"
										/>
									</a>
								<?php elseif ( $image_url ) : ?>
									<div class="category-list-section__media">
										<img
											class="category-list-section__image"
											src="<?php echo esc_url( $image_url ); ?>"
											alt=""
											loading="lazy"
											decoding="async"
										/>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</article>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

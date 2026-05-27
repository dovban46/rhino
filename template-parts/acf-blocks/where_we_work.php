<?php
/**
 * ACF Block: Where We Work
 *
 * @package RHINO
 */

global $rhino_prefetched_where_we_work;

$prefetched = isset( $rhino_prefetched_where_we_work ) && is_array( $rhino_prefetched_where_we_work )
	? $rhino_prefetched_where_we_work
	: null;

$section = ! empty( $prefetched['section'] ) && is_array( $prefetched['section'] )
	? $prefetched['section']
	: get_sub_field( 'where_we_work_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['where_we_work_top_text'] ?? '' ) );
$title    = $section['where_we_work_title'] ?? '';
$text     = $section['where_we_work_text'] ?? '';
$image    = $section['where_we_work_image'] ?? null;
$items    = $section['where_we_work_items'] ?? array();
$links    = $section['where_we_work_links'] ?? array();

$image_url = function_exists( 'rhino_acf_image_url' ) ? rhino_acf_image_url( $image ) : '';

$has_items = ! empty( $items ) && is_array( $items );
$has_links = ! empty( $links ) && is_array( $links );

if ( ! $top_text && ! $title && ! $text && ! $image_url && ! $has_items && ! $has_links ) {
	return;
}

$block      = get_acf_block_options( $prefetched['options'] ?? null );
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'where-we-work-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-where-we-work>
	<div class="where-we-work-section__inner">
		<div class="where-we-work-section__layout">
			<div class="where-we-work-section__content">
				<?php if ( $top_text || $title ) : ?>
					<div class="where-we-work-section__head where-we-work-section__reveal">
						<div class="where-we-work-section__top<?php echo $top_text ? '' : ' where-we-work-section__top--empty'; ?>">
							<?php if ( $top_text ) : ?>
								<span class="where-we-work-section__top-line" aria-hidden="true"></span>
								<span class="where-we-work-section__top-text"><?php echo esc_html( $top_text ); ?></span>
							<?php endif; ?>
						</div>

						<?php if ( $title ) : ?>
							<h2 class="where-we-work-section__title"><?php echo wp_kses_post( $title ); ?></h2>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $text ) : ?>
					<div class="where-we-work-section__text where-we-work-section__reveal"><?php echo wp_kses_post( $text ); ?></div>
				<?php endif; ?>

				<?php if ( $has_items ) : ?>
					<ul class="where-we-work-section__items">
						<?php
						foreach ( $items as $item ) :
							if ( ! is_array( $item ) ) {
								continue;
							}

							$label = trim( (string) ( $item['item_label'] ?? '' ) );

							if ( ! $label ) {
								continue;
							}
							?>
							<li class="where-we-work-section__item where-we-work-section__reveal">
								<span class="where-we-work-section__bullet" aria-hidden="true"></span>
								<span class="where-we-work-section__label"><?php echo esc_html( $label ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $has_links ) : ?>
					<ul class="where-we-work-section__links">
						<?php
						foreach ( $links as $link_row ) :
							if ( ! is_array( $link_row ) ) {
								continue;
							}

							$link = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $link_row['link_item'] ?? null ) : null;

							if ( empty( $link['url'] ) ) {
								continue;
							}

							$link_url    = $link['url'];
							$link_title  = $link['title'] ?: $link_url;
							$link_target = ! empty( $link['target'] ) ? $link['target'] : '_self';

							if ( function_exists( 'rhino_format_phone_href' ) ) {
								$tel_href = rhino_format_phone_href( $link_url );

								if ( $tel_href ) {
									$link_url = $tel_href;
								}
							}
							?>
							<li class="where-we-work-section__link-item where-we-work-section__reveal">
								<a
									class="where-we-work-section__link"
									href="<?php echo esc_url( $link_url ); ?>"
									target="<?php echo esc_attr( $link_target ); ?>"
									<?php echo '_blank' === $link_target ? 'rel="noopener noreferrer"' : ''; ?>
								>
									<?php echo esc_html( $link_title ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<?php if ( $image_url ) : ?>
				<div class="where-we-work-section__media where-we-work-section__reveal">
					<img
						class="where-we-work-section__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt=""
						loading="lazy"
						decoding="async"
					/>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

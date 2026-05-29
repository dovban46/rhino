<?php
/**
 * All Works slider panel partial.
 *
 * @package RHINO
 *
 * @var string   $panel_id Panel key (all or term ID).
 * @var WP_Post[] $posts    Service posts.
 * @var bool     $is_active Whether this panel is visible initially.
 */

$panel_id  = isset( $args['panel_id'] ) ? (string) $args['panel_id'] : 'all';
$posts     = isset( $args['posts'] ) && is_array( $args['posts'] ) ? $args['posts'] : array();
$is_active = ! empty( $args['is_active'] );

if ( empty( $posts ) ) {
	return;
}

$slide_count  = count( $posts );
$pages        = array_chunk( $posts, 6 );
$page_count   = count( $pages );
$card_index   = 0;
$panel_suffix = 'all' === $panel_id ? 'all' : $panel_id;
?>

<div
	class="all-works-section__panel<?php echo $is_active ? ' is-active' : ''; ?>"
	data-all-works-panel="<?php echo esc_attr( $panel_id ); ?>"
	<?php echo $is_active ? '' : ' hidden'; ?>
	data-slide-count="<?php echo esc_attr( (string) $slide_count ); ?>"
	data-page-count="<?php echo esc_attr( (string) $page_count ); ?>"
>
	<div class="all-works-section__slider-area all-works-section__reveal">
		<div class="swiper all-works-section__slider all-works-section__slider--desktop" data-all-works-slider="desktop-<?php echo esc_attr( $panel_suffix ); ?>">
			<div class="swiper-wrapper">
				<?php foreach ( $pages as $page_posts ) : ?>
					<div class="swiper-slide">
						<div class="all-works-section__grid">
							<?php
							foreach ( $page_posts as $service_post ) {
								++$card_index;
								get_template_part(
									'template-parts/acf-blocks/partials/all-works',
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

		<div class="swiper all-works-section__slider all-works-section__slider--mobile" data-all-works-slider="mobile-<?php echo esc_attr( $panel_suffix ); ?>">
			<div class="swiper-wrapper">
				<?php
				$card_index = 0;
				foreach ( $posts as $service_post ) :
					++$card_index;
					?>
					<div class="swiper-slide">
						<?php
						get_template_part(
							'template-parts/acf-blocks/partials/all-works',
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

		<div class="all-works-section__progress">
			<div
				class="all-works-section__progress-track"
				data-all-works-pagination
				role="slider"
				aria-label="<?php esc_attr_e( 'All works slides', 'rhino' ); ?>"
				aria-valuemin="1"
				aria-valuemax="<?php echo esc_attr( (string) $slide_count ); ?>"
				aria-valuenow="1"
				tabindex="0"
			>
				<div class="all-works-section__progress-fill" data-all-works-progress></div>
			</div>
		</div>

		<div class="all-works-section__nav-row">
			<button class="all-works-section__nav all-works-section__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Previous projects', 'rhino' ); ?>">
				<span class="all-works-section__nav-icon" aria-hidden="true"></span>
			</button>
			<button class="all-works-section__nav all-works-section__nav--next" type="button" aria-label="<?php esc_attr_e( 'Next projects', 'rhino' ); ?>">
				<span class="all-works-section__nav-icon" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</div>

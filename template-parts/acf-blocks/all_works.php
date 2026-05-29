<?php
/**
 * ACF Block: All Works
 *
 * Lists all service categories and posts (no block fields).
 *
 * @package RHINO
 */

$panels = function_exists( 'rhino_get_all_works_panels' )
	? rhino_get_all_works_panels()
	: array();

$filter_options = function_exists( 'rhino_get_all_works_filter_options' )
	? rhino_get_all_works_filter_options()
	: array();

if ( empty( $panels ) || empty( $filter_options ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'all-works-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
$default_id = isset( $panels['all'] ) ? 'all' : array_key_first( $panels );
?>

<section
	class="<?php echo esc_attr( $classes ); ?>"
	<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-all-works
	data-active-panel="<?php echo esc_attr( (string) $default_id ); ?>"
>
	<div class="all-works-section__filter all-works-section__reveal">
		<div class="all-works-section__filter-bar">
			<div class="all-works-section__filter-inner">
				<span class="all-works-section__filter-label"><?php esc_html_e( 'TYPE :', 'rhino' ); ?></span>

				<div class="all-works-section__filter-buttons" role="tablist" aria-label="<?php esc_attr_e( 'Project type', 'rhino' ); ?>">
					<?php foreach ( $filter_options as $option ) : ?>
						<?php
						$option_id    = $option['id'] ?? '';
						$option_label = $option['label'] ?? '';
						$is_active    = $option_id === $default_id;
						$has_panel    = isset( $panels[ $option_id ] );

						if ( ! $option_id || ! $option_label || ! $has_panel ) {
							continue;
						}
						?>
						<button
							type="button"
							class="all-works-section__filter-btn<?php echo $is_active ? ' is-active' : ''; ?>"
							role="tab"
							aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
							data-all-works-filter="<?php echo esc_attr( $option_id ); ?>"
						>
							<?php echo esc_html( $option_label ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="all-works-section__filter-select" data-all-works-select>
					<button
						type="button"
						class="all-works-section__filter-select-toggle"
						data-all-works-select-toggle
						aria-expanded="false"
						aria-haspopup="listbox"
					>
						<span class="all-works-section__filter-select-label" data-all-works-select-label>
							<?php
							foreach ( $filter_options as $option ) {
								if ( ( $option['id'] ?? '' ) === $default_id ) {
									echo esc_html( $option['label'] ?? '' );
									break;
								}
							}
							?>
						</span>
						<span class="all-works-section__filter-select-icon" aria-hidden="true"></span>
					</button>

					<ul class="all-works-section__filter-select-menu" data-all-works-select-menu role="listbox" hidden>
						<?php foreach ( $filter_options as $option ) : ?>
							<?php
							$option_id    = $option['id'] ?? '';
							$option_label = $option['label'] ?? '';
							$is_active    = $option_id === $default_id;

							if ( ! $option_id || ! $option_label || empty( $panels[ $option_id ] ) ) {
								continue;
							}
							?>
							<li class="all-works-section__filter-select-item" role="presentation">
								<button
									type="button"
									class="all-works-section__filter-select-option<?php echo $is_active ? ' is-active' : ''; ?>"
									role="option"
									aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
									data-all-works-filter="<?php echo esc_attr( $option_id ); ?>"
								>
									<?php echo esc_html( $option_label ); ?>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="all-works-section__body all-works-section__reveal">
		<div class="all-works-section__inner">
			<div class="all-works-section__panels">
				<?php
				foreach ( $panels as $panel_id => $panel_posts ) {
					get_template_part(
						'template-parts/acf-blocks/partials/all-works',
						'slider',
						array(
							'panel_id'  => $panel_id,
							'posts'     => $panel_posts,
							'is_active' => $panel_id === $default_id,
						)
					);
				}
				?>
			</div>
		</div>
	</div>

	<div class="recent-work-lightbox" data-all-works-lightbox aria-hidden="true">
		<div class="recent-work-lightbox__backdrop" data-all-works-lightbox-close tabindex="-1"></div>

		<div
			class="recent-work-lightbox__dialog"
			role="dialog"
			aria-modal="true"
			aria-label="<?php esc_attr_e( 'Project gallery', 'rhino' ); ?>"
		>
			<div class="recent-work-lightbox__row">
				<button
					type="button"
					class="recent-work-lightbox__nav recent-work-lightbox__nav--prev"
					data-all-works-lightbox-prev
					aria-label="<?php esc_attr_e( 'Previous project', 'rhino' ); ?>"
				>
					<span class="recent-work-lightbox__nav-icon" aria-hidden="true"></span>
				</button>

				<div class="recent-work-lightbox__viewport" data-all-works-lightbox-viewport>
					<figure class="recent-work-lightbox__figure">
						<img class="recent-work-lightbox__image" src="" alt="" decoding="async" />
					</figure>
				</div>

				<button
					type="button"
					class="recent-work-lightbox__nav recent-work-lightbox__nav--next"
					data-all-works-lightbox-next
					aria-label="<?php esc_attr_e( 'Next project', 'rhino' ); ?>"
				>
					<span class="recent-work-lightbox__nav-icon" aria-hidden="true"></span>
				</button>
			</div>
		</div>
	</div>
</section>

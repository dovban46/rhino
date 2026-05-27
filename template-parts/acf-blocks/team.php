<?php
/**
 * ACF Block: Team
 *
 * @package RHINO
 */

$section = get_sub_field( 'team_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['team_top_text'] ?? '' ) );
$title    = $section['team_title'] ?? '';
$members  = $section['team_members'] ?? array();

if ( ! $top_text && ! $title && empty( $members ) ) {
	return;
}

$block      = get_acf_block_options();
$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'team-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-team>
	<div class="team-section__inner">
		<?php if ( $top_text || $title ) : ?>
			<div class="team-section__header team-section__reveal">
				<?php if ( $top_text ) : ?>
					<div class="team-section__top">
						<span class="team-section__top-line" aria-hidden="true"></span>
						<span class="team-section__top-text"><?php echo esc_html( $top_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="team-section__title"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $members ) && is_array( $members ) ) : ?>
			<div class="team-section__slider-area team-section__reveal">
				<div class="team-section__slider swiper">
					<div class="swiper-wrapper">
						<?php foreach ( $members as $member ) : ?>
							<?php
							if ( ! is_array( $member ) ) {
								continue;
							}

							$image    = $member['member_img'] ?? null;
							$image_url = function_exists( 'rhino_acf_image_url' ) ? rhino_acf_image_url( $image ) : '';
							$position = trim( (string) ( $member['member_position'] ?? '' ) );
							$name     = trim( (string) ( $member['member_name'] ?? '' ) );
							$text     = $member['member_text'] ?? '';

							if ( ! $image_url && ! $position && ! $name && ! $text ) {
								continue;
							}
							?>
							<div class="swiper-slide team-section__slide team-section__reveal">
								<article class="team-section__card">
									<?php if ( $image_url ) : ?>
										<div class="team-section__media">
											<img
												class="team-section__image"
												src="<?php echo esc_url( $image_url ); ?>"
												alt="<?php echo esc_attr( $name ); ?>"
												loading="lazy"
												decoding="async"
											/>
										</div>
									<?php endif; ?>

									<div class="team-section__content">
										<?php if ( $position ) : ?>
											<p class="team-section__position"><?php echo esc_html( $position ); ?></p>
										<?php endif; ?>

										<?php if ( $name ) : ?>
											<h3 class="team-section__name"><?php echo esc_html( $name ); ?></h3>
										<?php endif; ?>

										<?php if ( $text ) : ?>
											<div class="team-section__text"><?php echo wp_kses_post( $text ); ?></div>
										<?php endif; ?>
									</div>
								</article>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="team-section__nav-row">
					<button class="team-section__nav team-section__nav--prev" type="button" aria-label="<?php esc_attr_e( 'Previous team member', 'rhino' ); ?>">
						<span class="team-section__nav-icon" aria-hidden="true"></span>
					</button>
					<button class="team-section__nav team-section__nav--next" type="button" aria-label="<?php esc_attr_e( 'Next team member', 'rhino' ); ?>">
						<span class="team-section__nav-icon" aria-hidden="true"></span>
					</button>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
/**
 * ACF Block: Shared Review
 *
 * ACF layout: shared_review
 * Group: shared_review_section
 *   - shared_review_title (text)
 *
 * @package RHINO
 */

$section = get_sub_field( 'shared_review_section' );

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$title = trim( (string) ( $section['shared_review_title'] ?? '' ) );

if ( ! $title ) {
	return;
}

$block      = get_acf_block_options();
$anchor_id  = ! empty( $block['id'] ) ? $block['id'] : 'shared-review';
$section_id = ' id="' . esc_attr( $anchor_id ) . '"';
$classes    = 'shared-review-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );

$theme_uri      = get_template_directory_uri();
$star_empty_url = $theme_uri . '/assets/images/Review-empty-star.svg';
$star_filled_url = $theme_uri . '/assets/images/Star.svg';
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-shared-review>
	<div class="shared-review-section__inner">
		<div class="shared-review-section__layout">
			<div class="shared-review-section__intro shared-review-section__reveal">
				<h2 class="shared-review-section__title"><?php echo wp_kses_post( $title ); ?></h2>
			</div>

			<div class="shared-review-section__form-area">
				<form
					class="shared-review-section__form rhino-shared-review-form shared-review-section__reveal"
					data-shared-review-form
					action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
					method="post"
					novalidate
				>
					<div class="shared-review-section__form-box shared-review-section__reveal">
						<div class="rhino-cf7-form">
							<div class="rhino-cf7-form__row rhino-cf7-form__row--2">
								<div class="rhino-cf7-form__field">
									<label class="rhino-cf7-form__label" for="shared-review-full-name">
										<span class="rhino-cf7-form__label-text"><?php esc_html_e( 'Full name', 'rhino' ); ?></span>
										<input
											class="rhino-cf7-form__control"
											type="text"
											id="shared-review-full-name"
											name="full_name"
											autocomplete="name"
											required
										/>
									</label>
								</div>

								<div class="rhino-cf7-form__field">
									<label class="rhino-cf7-form__label" for="shared-review-email">
										<span class="rhino-cf7-form__label-text"><?php esc_html_e( 'Email', 'rhino' ); ?></span>
										<input
											class="rhino-cf7-form__control"
											type="text"
											id="shared-review-email"
											name="email"
											inputmode="email"
											autocomplete="email"
											autocapitalize="none"
											autocorrect="off"
											spellcheck="false"
											required
										/>
									</label>
								</div>
							</div>

							<div class="rhino-cf7-form__row">
								<div class="rhino-cf7-form__field rhino-cf7-form__field--textarea">
									<label class="rhino-cf7-form__label" for="shared-review-message">
										<span class="rhino-cf7-form__label-text"><?php esc_html_e( 'Your message', 'rhino' ); ?></span>
										<textarea
											class="rhino-cf7-form__control"
											id="shared-review-message"
											name="message"
											rows="4"
											required
										></textarea>
									</label>
								</div>
							</div>
						</div>
					</div>

					<div class="shared-review-section__form-footer shared-review-section__reveal">
						<div
							class="shared-review-section__rating"
							data-shared-review-rating
							data-star-empty="<?php echo esc_url( $star_empty_url ); ?>"
							data-star-filled="<?php echo esc_url( $star_filled_url ); ?>"
							role="group"
							aria-label="<?php esc_attr_e( 'Your rating', 'rhino' ); ?>"
						>
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
								<button
									type="button"
									class="shared-review-section__star-btn"
									data-star-value="<?php echo esc_attr( (string) $i ); ?>"
									aria-label="<?php echo esc_attr( sprintf( __( 'Rate %1$d out of 5', 'rhino' ), $i ) ); ?>"
								>
									<img
										class="shared-review-section__star-icon"
										src="<?php echo esc_url( $star_empty_url ); ?>"
										alt=""
										width="44"
										height="43"
										decoding="async"
									/>
								</button>
							<?php endfor; ?>
							<input type="hidden" name="rating" value="" required />
						</div>

						<div class="shared-review-section__submit-wrap">
							<button type="submit" class="shared-review-section__submit">
								<span class="shared-review-section__submit-text"><?php esc_html_e( 'Send', 'rhino' ); ?></span>
								<span class="shared-review-section__submit-icon" aria-hidden="true"></span>
							</button>
						</div>
					</div>

					<div class="shared-review-section__form-meta" aria-hidden="true">
						<input type="hidden" name="city" value="" data-shared-review-city />
						<input type="hidden" name="action" value="rhino_submit_shared_review" />
						<input type="hidden" name="nonce" value="" data-shared-review-nonce />
						<input type="text" id="shared-review-company" name="company" tabindex="-1" autocomplete="off" />
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="contact-section__modal" id="shared-review-success-modal" aria-hidden="true">
		<div class="contact-section__modal-overlay" data-shared-review-modal-close tabindex="-1"></div>
		<div
			class="contact-section__modal-dialog"
			role="dialog"
			aria-modal="true"
			aria-labelledby="shared-review-success-modal-title"
		>
			<button
				type="button"
				class="contact-section__modal-close"
				data-shared-review-modal-close
				aria-label="<?php esc_attr_e( 'Close', 'rhino' ); ?>"
			>
				<img src="<?php echo esc_url( $theme_uri . '/assets/images/close.svg' ); ?>" alt="" width="36" height="36" decoding="async" />
			</button>

			<div class="contact-section__modal-icon-wrap" aria-hidden="true">
				<img
					class="contact-section__modal-icon"
					src="<?php echo esc_url( $theme_uri . '/assets/images/form-done.svg' ); ?>"
					alt=""
					width="68"
					height="68"
					decoding="async"
				/>
			</div>

			<h3 class="contact-section__modal-title" id="shared-review-success-modal-title">
				<?php esc_html_e( 'Thank you for your feedback!', 'rhino' ); ?>
			</h3>

			<p class="contact-section__modal-text">
				<?php esc_html_e( 'Your experience helps us improve. We truly appreciate you choosing our services.', 'rhino' ); ?>
			</p>
		</div>
	</div>
</section>

<?php
/**
 * ACF Block: Contact
 *
 * @package RHINO
 */

global $rhino_prefetched_contact;

if ( ! empty( $rhino_prefetched_contact['section'] ) && is_array( $rhino_prefetched_contact['section'] ) ) {
	$section = $rhino_prefetched_contact['section'];
	$block   = get_acf_block_options( $rhino_prefetched_contact['options'] ?? null );
} else {
	$section = get_sub_field( 'contact_section' );
	$block   = get_acf_block_options();
}

if ( empty( $section ) || ! is_array( $section ) ) {
	return;
}

$top_text = trim( (string) ( $section['contact_top_text'] ?? '' ) );
$title    = $section['contact_title'] ?? '';
$text     = $section['contact_text'] ?? '';
$items    = $section['contact_items'] ?? array();
$form     = trim( (string) ( $section['contact_form'] ?? '' ) );

if ( ! $top_text && ! $title && ! $text && empty( $items ) && ! $form ) {
	return;
}

$section_id = $block['id'] ? ' id="' . esc_attr( $block['id'] ) . '"' : '';
$classes    = 'contact-section' . ( $block['class'] ? ' ' . esc_attr( trim( $block['class'] ) ) : '' );

$theme_uri = get_template_directory_uri();
?>

<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $section_id; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="contact-section__inner">
		<div class="contact-section__layout">
			<div class="contact-section__content">
				<?php if ( $top_text ) : ?>
					<div class="contact-section__top contact-section__reveal">
						<span class="contact-section__top-line" aria-hidden="true"></span>
						<span class="contact-section__top-text"><?php echo esc_html( $top_text ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $title ) : ?>
					<h2 class="contact-section__title contact-section__reveal"><?php echo wp_kses_post( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $text ) : ?>
					<div class="contact-section__text contact-section__reveal"><?php echo wp_kses_post( $text ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $items ) && is_array( $items ) ) : ?>
					<div class="contact-section__items-wrap">
						<ul class="contact-section__items">
							<?php
							$item_index = 0;
							foreach ( $items as $item ) :
								if ( ! is_array( $item ) ) {
									continue;
								}

								$item_label = trim( (string) ( $item['item_text'] ?? '' ) );
								$link       = function_exists( 'rhino_acf_link' ) ? rhino_acf_link( $item['item_link'] ?? null ) : null;

								if ( ! $item_label && empty( $link['url'] ) ) {
									continue;
								}

								++$item_index;
								$link_url    = $link['url'] ?? '';
								$link_title  = $link['title'] ?? $link_url;
								$link_target = ! empty( $link['target'] ) ? $link['target'] : '_self';

								if ( $link_url && function_exists( 'rhino_format_phone_href' ) ) {
									$tel_href = rhino_format_phone_href( $link_url );
									if ( $tel_href ) {
										$link_url = $tel_href;
									} elseif ( 0 === strpos( $link_url, 'tel:' ) ) {
										$link_url = $link_url;
									} elseif ( preg_match( '/^[\d\s\(\)\-\+\.]+$/', $link_url ) ) {
										$tel_href = rhino_format_phone_href( $link_url );
										if ( $tel_href ) {
											$link_url = $tel_href;
										}
									}
								}
								?>
								<li class="contact-section__item contact-section__reveal">
									<?php if ( $item_label ) : ?>
										<span class="contact-section__item-label"><?php echo esc_html( $item_label ); ?></span>
									<?php endif; ?>

									<?php if ( $link_url ) : ?>
										<a
											class="contact-section__item-link"
											href="<?php echo esc_url( $link_url ); ?>"
											<?php echo '_blank' === $link_target ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
										><?php echo esc_html( $link_title ); ?></a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $form ) : ?>
				<div class="contact-section__form-wrap contact-section__reveal">
					<div class="contact-section__form" data-rhino-contact-form>
						<?php echo do_shortcode( $form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $form ) : ?>
		<div class="contact-section__modal" id="contact-success-modal" aria-hidden="true">
			<div class="contact-section__modal-overlay" data-contact-modal-close tabindex="-1"></div>
			<div
				class="contact-section__modal-dialog"
				role="dialog"
				aria-modal="true"
				aria-labelledby="contact-success-modal-title"
			>
				<button
					type="button"
					class="contact-section__modal-close"
					data-contact-modal-close
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

				<h3 class="contact-section__modal-title" id="contact-success-modal-title">
					<?php esc_html_e( 'Thank you for reaching out!', 'rhino' ); ?>
				</h3>

				<p class="contact-section__modal-text">
					<?php esc_html_e( 'Your request has been successfully sent. Our team will review your project details and get back to you within one business day to schedule your free estimate.', 'rhino' ); ?>
				</p>
			</div>
		</div>
	<?php endif; ?>
</section>

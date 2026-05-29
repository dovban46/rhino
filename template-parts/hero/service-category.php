<?php
/**
 * Hero: Service category archive
 *
 * @package RHINO
 */

$term = get_queried_object();

if ( ! $term instanceof WP_Term ) {
	return;
}

$title       = $term->name ?? '';
$description = function_exists( 'rhino_get_service_category_description' )
	? rhino_get_service_category_description( $term )
	: '';
$image_url   = function_exists( 'rhino_get_service_category_image_url' )
	? rhino_get_service_category_image_url( $term )
	: '';
$button      = function_exists( 'rhino_get_homepage_hero_button' )
	? rhino_get_homepage_hero_button()
	: null;
$services_url = function_exists( 'rhino_get_services_page_url' )
	? rhino_get_services_page_url()
	: home_url( '/services/' );
$text_bg     = function_exists( 'rhino_get_homepage_hero_bg_text' )
	? rhino_get_homepage_hero_bg_text()
	: '';

if ( ! $title && ! $description && ! $image_url && empty( $button['url'] ) ) {
	return;
}

if ( $image_url && function_exists( 'rhino_preload_hero_background' ) ) {
	rhino_preload_hero_background( $image_url );
}

?>

<section class="hero-section rhino-hero rhino-service-category-hero hero-section--play-on-load">
	<?php if ( $image_url ) : ?>
		<img
			class="hero-section__bg-image"
			src="<?php echo esc_url( $image_url ); ?>"
			alt=""
			decoding="async"
			fetchpriority="high"
		/>
	<?php endif; ?>

	<span class="hero-section__overlay" aria-hidden="true"></span>

	<?php if ( $text_bg ) : ?>
		<div class="hero-section__bg-text" aria-hidden="true"><?php echo esc_html( $text_bg ); ?></div>
	<?php endif; ?>

	<div class="hero-section__container">
		<div class="hero-section__content">
			<a class="hero-section__back hero-section__reveal" href="<?php echo esc_url( $services_url ); ?>">
				<span class="hero-section__back-icon" aria-hidden="true">←</span>
				<span class="hero-section__back-text"><?php esc_html_e( 'All services', 'rhino' ); ?></span>
			</a>

			<?php if ( $title ) : ?>
				<div class="hero-section__title-wrap hero-section__reveal--title">
					<h1 class="hero-section__title"><?php echo esc_html( $title ); ?></h1>
				</div>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<div class="hero-section__bottom-text hero-section__reveal"><?php echo wp_kses_post( $description ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $button['url'] ) && ! empty( $button['title'] ) ) : ?>
				<a
					class="hero-section__button hero-section__reveal"
					href="<?php echo esc_url( $button['url'] ); ?>"
					target="<?php echo esc_attr( $button['target'] ); ?>"
					<?php echo '_blank' === $button['target'] ? 'rel="noopener noreferrer"' : ''; ?>
				>
					<span class="hero-section__button-text"><?php echo esc_html( $button['title'] ); ?></span>
					<span class="hero-section__button-icon" aria-hidden="true"></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

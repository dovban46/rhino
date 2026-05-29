<?php
/**
 * 404 template.
 *
 * @package RHINO
 */

get_header();

$bg_image = get_template_directory_uri() . '/assets/images/404.webp';
$home_url = home_url( '/' );
?>

<main id="primary" class="site-main site-main--error-404">
<section class="error-404" aria-labelledby="error-404-title">
	<div class="error-404__media" aria-hidden="true">
		<img
			class="error-404__bg-image"
			src="<?php echo esc_url( $bg_image ); ?>"
			alt=""
			loading="eager"
			decoding="async"
			fetchpriority="high"
		/>
	</div>
	<div class="error-404__overlay" aria-hidden="true"></div>

	<div class="error-404__inner">
		<p class="error-404__code" aria-hidden="true">404</p>
		<h1 id="error-404-title" class="error-404__title"><?php esc_html_e( 'Sorry, there was an error', 'rhino' ); ?></h1>
		<p class="error-404__text"><?php esc_html_e( 'Please return to the main page', 'rhino' ); ?></p>
		<a class="error-404__button" href="<?php echo esc_url( $home_url ); ?>">
			<span class="error-404__button-text"><?php esc_html_e( 'HOME', 'rhino' ); ?></span>
			<span class="error-404__button-icon" aria-hidden="true"></span>
		</a>
	</div>
</section>
</main>

<?php
get_footer();

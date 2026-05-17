<?php
/**
 * The header for our theme
 *
 * @package RHINO
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">

	<header id="masthead" class="site-header">
		<div class="site-header__container">
			<div class="site-header__inner">
				<div class="site-header__col site-header__col--logo">
					<?php rhino_header_logo(); ?>
				</div>

				<div class="site-header__col site-header__col--nav">
					<nav id="site-navigation" class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'rhino' ); ?>">
						<?php rhino_header_nav_menu(); ?>
					</nav>
				</div>

				<div class="site-header__col site-header__col--actions">
					<?php rhino_header_phone(); ?>
					<?php rhino_header_button(); ?>
				</div>

				<button
					type="button"
					class="site-header__burger"
					aria-controls="site-header-mobile"
					aria-expanded="false"
					aria-label="<?php esc_attr_e( 'Toggle menu', 'rhino' ); ?>"
				>
					<span class="site-header__burger-line"></span>
					<span class="site-header__burger-line"></span>
					<span class="site-header__burger-line"></span>
				</button>
			</div>
		</div>
	</header>

	<div id="site-header-mobile" class="site-header__mobile" aria-hidden="true">
		<div class="site-header__mobile-inner">
			<nav class="site-header__mobile-nav" aria-label="<?php esc_attr_e( 'Mobile Menu', 'rhino' ); ?>">
				<?php
				rhino_header_nav_menu(
					array(
						'menu_id'    => 'primary-menu-mobile',
						'menu_class' => 'site-header__menu site-header__menu--mobile',
					)
				);
				?>
			</nav>

			<div class="site-header__mobile-contact">
				<?php rhino_header_phone( false ); ?>
				<?php rhino_header_email(); ?>
			</div>

			<div class="site-header__mobile-cta">
				<?php rhino_header_button_mobile(); ?>
			</div>
		</div>
	</div>

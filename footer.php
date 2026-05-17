<?php
/**
 * The template for displaying the footer
 *
 * @package RHINO
 */

$footer_text_1 = rhino_footer_html( 'footer_text_1' );
$footer_text_2 = rhino_footer_html( 'footer_text_2' );

?>
	<footer id="colophon" class="site-footer">
		<div class="site-footer__container">
			<div class="site-footer__top">
				<div class="site-footer__left">
					<?php rhino_footer_logo(); ?>

					<?php if ( $footer_text_1 ) : ?>
						<p class="site-footer__text-1"><?php echo wp_kses_post( $footer_text_1 ); ?></p>
					<?php endif; ?>

					<?php if ( $footer_text_2 ) : ?>
						<div class="site-footer__text-2"><?php echo wp_kses_post( $footer_text_2 ); ?></div>
					<?php endif; ?>
				</div>

				<div class="site-footer__right">
					<?php rhino_footer_contact(); ?>
					<?php rhino_footer_social(); ?>
				</div>
			</div>

			<div class="site-footer__divider" aria-hidden="true"></div>

			<div class="site-footer__bottom">
				<?php rhino_footer_bottom_text(); ?>
				<?php rhino_footer_nav_menu(); ?>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

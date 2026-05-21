<?php
/**
 * Taxonomy archive: Service category (underscore slug fallback)
 *
 * @package RHINO
 */

get_header();

get_template_part( 'template-parts/hero/service-category' );

rhino_the_acf_term_loop();

get_footer();

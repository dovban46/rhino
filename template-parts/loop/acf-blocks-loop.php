<?php
if ( have_rows( 'blocks' ) ) {
	while ( have_rows( 'blocks' ) ) {
		the_row();

		get_template_part( 'template-parts/acf-blocks/' . get_row_layout() );
	}
}
?>
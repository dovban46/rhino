<?php
/**
 * Custom navigation walker for header menus.
 *
 * @package RHINO
 */

/**
 * Header nav walker with submenu toggle control.
 */
class Rhino_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Current top-level item ID (for submenu aria-controls).
	 *
	 * @var int
	 */
	private $current_parent_id = 0;

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );
		$id     = $this->current_parent_id ? ' id="site-header-submenu-' . (int) $this->current_parent_id . '"' : '';

		$output .= "\n{$indent}<ul class=\"sub-menu site-header__submenu\"{$id}>\n";
	}

	/**
	 * Starts the element output.
	 *
	 * @param string   $output Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$indent      = $depth ? str_repeat( "\t", $depth ) : '';
		$classes     = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) );
		$has_children = in_array( 'menu-item-has-children', $classes, true );

		if ( 0 === (int) $depth ) {
			$this->current_parent_id = (int) $item->ID;

			$output .= $indent . '<li class="' . esc_attr( $class_names ) . '">';
			$output .= '<div class="site-header__menu-row">';

			$output .= '<a class="site-header__menu-link" href="' . esc_url( $item->url ) . '">';
			$output .= esc_html( $item->title );
			$output .= '</a>';

			if ( $has_children ) {
				$output .= '<button type="button" class="site-header__menu-toggle" aria-expanded="false" aria-controls="site-header-submenu-' . (int) $item->ID . '">';
				$output .= '<span class="site-header__menu-toggle-icon" aria-hidden="true"></span>';
				$output .= '</button>';
				$output .= '<span class="site-header__menu-index" aria-hidden="true"></span>';
			} else {
				$output .= '<span class="site-header__menu-index site-header__menu-index--static" aria-hidden="true"></span>';
			}

			$output .= '</div>';
			return;
		}

		$output .= $indent . '<li class="' . esc_attr( $class_names ) . '">';
		$output .= '<a href="' . esc_url( $item->url ) . '">';
		$output .= esc_html( $item->title );
		$output .= '</a>';
	}

	/**
	 * Ends the element output.
	 *
	 * @param string   $output Used to append additional content.
	 * @param WP_Post  $item   Page data object.
	 * @param int      $depth  Depth of page.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";

		if ( 0 === (int) $depth ) {
			$this->current_parent_id = 0;
		}
	}
}

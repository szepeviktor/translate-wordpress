<?php

namespace WeglotWP\Actions\Front;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Helpers\Helper_Pages_Weglot;

/**
 *
 * @since 2.0
 *
 */
class Front_Menu_Weglot implements Hooks_Interface_Weglot {

	/**
	 * @since 2.5.0
	 */
	public function __construct() {
		$this->option_services      = weglot_get_service( 'Option_Service_Weglot' );
		$this->button_services      = weglot_get_service( 'Button_Service_Weglot' );
		$this->custom_url_services  = weglot_get_service( 'Custom_Url_Service_Weglot' );
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.5.0
	 * @return void
	 */
	public function hooks() {
		if ( ! $this->option_services->get_option( 'allowed' ) ) {
			return;
		}

		// add_filter( 'wp_get_nav_menu_items', [ $this, 'weglot_wp_get_nav_menu_items' ], 20 );
		// add_filter( 'wp_nav_menu_objects', [ $this, 'weglot_wp_nav_menu_objects' ] );

		if ( $this->option_services->get_option( 'is_menu' ) ) {
			add_filter( 'wp_nav_menu_items', [ $this, 'weglot_fallback_menu' ] );
		}
	}

	/**
	 * @since 2.5.0
	 * @param string $items
	 * @return string
	 */
	public function weglot_fallback_menu( $items ) {
		$button = $this->button_services->get_html();
		$items .= $button;

		return $items;
	}

	/**
	 * @since 2.5.0
	 * @param array $items
	 * @return array
	 */
	public function weglot_wp_get_nav_menu_items( $items ) {
		// Prevent customizer
		if ( doing_action( 'customize_register' ) ) {
			return $items;
		}


		$new_items = [];
		$offset    = 0;

		foreach ( $items as $key => $item ) {
			if ( 'weglot-switcher' !== $item->post_name ) {
				$item->menu_order += $offset;
				$new_items[] = $item;
				continue;
			}

			$i = 0;

			$options   = $this->option_services->get_option('menu_switcher');
			$languages = weglot_get_languages_configured();
			// var_dump($languages);
			// die;
			// $args          = array_merge( array( 'raw' => 1 ), $options );
			// $the_languages = $switcher->the_languages( PLL()->links, $args );

			// // parent item for dropdown
			// if ( ! empty( $options['dropdown'] ) ) {
			// 	$item->title      = $this->get_item_title( $this->curlang->flag, $this->curlang->name, $options );
			// 	$item->attr_title = '';
			// 	$item->classes    = array( 'pll-parent-menu-item' );
			// 	$new_items[]      = $item;
			// 	$offset++;
			// }

			foreach ( $languages as $language ) {
				$language_item             = clone $item;
				$language_item->ID         = 'weglot-' . $item->ID . '-' . $language->getIso639();
				$language_item->title      = $this->button_services->get_name_with_language_entry( $language );
				$language_item->attr_title = '';
				$language_item->url        = $this->custom_url_services->get_link_button_with_key_code( $language->getIso639() );
				;
				$language_item->lang       = $language->getIso639(); // Save this for use in nav_menu_link_attributes
				// $language_item->classes    = $lang['classes'];
				$language_item->menu_order += $offset + $i++;
				// if ( ! empty( $options['dropdown'] ) ) {
				// 	$language_item->menu_item_parent = $item->db_id;
				// 	$language_item->db_id            = 0; // to avoid recursion
				// }

				$new_items[] = $language_item;
			}
			$offset += $i - 1;
		}
		return $new_items;
	}

	/**
	 * @since 2.5.0
	 * @param array $items
	 * @return array
	 */
	public function weglot_wp_nav_menu_objects( $items ) {
		$r_ids = $k_ids = array();
		var_dump($items);
		foreach ( $items as $item ) {
			// 	if ( ! empty( $item->classes ) && is_array( $item->classes ) ) {
		// 		if ( in_array( 'current-lang', $item->classes ) ) {
		// 			$item->current = false;
		// 			$item->classes = array_diff( $item->classes, array( 'current-menu-item' ) );
		// 			$r_ids         = array_merge( $r_ids, $this->get_ancestors( $item ) ); // Remove the classes for these ancestors
		// 		} elseif ( in_array( 'current-menu-item', $item->classes ) ) {
		// 			$k_ids = array_merge( $k_ids, $this->get_ancestors( $item ) ); // Keep the classes for these ancestors
		// 		}
		// 	}
		// }

		// $r_ids = array_diff( $r_ids, $k_ids );

		// foreach ( $items as $item ) {
		// 	if ( ! empty( $item->db_id ) && in_array( $item->db_id, $r_ids ) ) {
		// 		$item->classes = array_diff( $item->classes, array( 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent', 'current_page_ancestor' ) );
		// 	}
		}

		return $items;
	}
}


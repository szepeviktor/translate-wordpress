<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use WeglotWP\Models\Mediator_Service_Interface_Weglot;

/**
 * Button services
 *
 * @since 2.0
 */
class Button_Service_Weglot implements Mediator_Service_Interface_Weglot {


	/**
	 * @see Mediator_Service_Interface_Weglot
	 * @since 2.0
	 * @param array $services
	 * @return void
	 */
	public function use_services( $services ) {
		$this->option_services      = $services['Option_Service_Weglot'];
		$this->request_url_services = $services['Request_Url_Service_Weglot'];
		$this->language_services    = $services['Language_Service_Weglot'];
	}

	/**
	 * Use for wp_ses
	 * @since 2.0
	 * @return array
	 */
	public function get_allowed_tags() {
		return [
			'a' => [
				'href'                => [],
				'title'               => [],
				'onclick'             => [],
				'target'              => [],
				'data-wg-notranslate' => [],
				'class'               => [],
			],
			'div' => [
				'class'               => [],
				'data-wg-notranslate' => [],
			],
			'aside' => [
				'onclick'             => [],
				'class'               => [],
				'data-wg-notranslate' => [],
			],
			'ul'    => [
				'class'               => [],
				'data-wg-notranslate' => [],
			],
			'li'    => [
				'class'               => [],
				'data-wg-notranslate' => [],
			],
		];
	}

	/**
	 * Get html button switcher
	 *
	 * @since 2.0
	 * @return string
	 * @param string $add_class
	 */
	public function get_html( $add_class = '' ) {
		$options                          = $this->option_services->get_options();
		$is_fullname                      = $options['is_fullname'];
		$with_name                        = $options['with_name'];
		$is_dropdown                      = $options['is_dropdown'];
		$with_flags                       = $options['with_flags'];
		$type_flags                       = $options['type_flags'];
		$weglot_url                       = $this->request_url_services->get_weglot_url();

		$destination_language             = $options['destination_language'];
		$original_language                = $options['original_language'];
		$current_language                 = $this->request_url_services->get_current_language();

		$flag_class                       = $with_flags ? 'weglot-flags ' : '';
		$flag_class .= '0' === $type_flags ? '' : 'flag-' . $type_flags . ' ';

		$tag                              = $is_dropdown ? 'div' : 'li';
		$list_tag                         = $is_dropdown ? '<ul>' : '';
		$list_tag_close                   = $is_dropdown ? '</ul>' : '';
		$class_aside                      = $is_dropdown ? 'weglot-drop ' : 'weglot-list ';

		$languages = $this->language_services->get_languages_available();

		$button_html = sprintf( '<!--Weglot %s-->', WEGLOT_VERSION );
		$button_html .= sprintf( "<aside data-wg-notranslate='' id='weglot-selector' class='weglot-default country-selector closed %s'>", $class_aside . ' ' . $add_class );

		$button_html .= sprintf(
			'<%s data-wg-notranslate="" class="weglot-current weglot-li %s"><a href="#" onclick="return false;">%s</a></%s>',
			$tag,
			$flag_class . $current_language,
			$languages[ $current_language ]->getEnglishName(),
			$tag
		);

		$button_html .= $list_tag;

		array_unshift( $destination_language, $original_language );

		foreach ( $destination_language as $key => $key_code ) {
			if ( $key_code === $current_language ) {
				continue;
			}

			$button_html .= sprintf( '<li class="weglot-li %s">', $flag_class . $key_code );

			$button_html .= sprintf(
				'<a data-wg-notranslate href="%s">%s</a>',
				$weglot_url->getForLanguage( $key_code ),
				$languages[ $key_code ]->getEnglishName()
			);

			$button_html .= '</li>';
		}

		$button_html .= $list_tag_close;
		$button_html .= '</aside>';

		return apply_filters( 'weglot_button_html', $button_html );
	}
}

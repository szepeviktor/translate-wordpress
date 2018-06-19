<?php

namespace WeglotWP\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WeglotWP\Models\Hooks_Interface_Weglot;
use WeglotWP\Models\Mediator_Service_Interface_Weglot;
use WeglotWP\Helpers\Helper_Pages_Weglot;

/**
 *
 * @since 2.0
 *
 */
class Customize_Menu_Weglot implements Hooks_Interface_Weglot, Mediator_Service_Interface_Weglot {

	/**
	 * @see Mediator_Service_Interface_Weglot
	 *
	 * @param array $services
	 * @return Customize_Menu_Weglot
	 */
	public function use_services( $services ) {
		$this->language_services = $services['Language_Service_Weglot'];
		return $this;
	}

	/**
	 * @see Hooks_Interface_Weglot
	 *
	 * @since 2.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_head-nav-menus.php', [ $this, 'add_nav_menu_meta_boxes' ] );
		add_filter( 'nav_menu_link_attributes', [ $this, 'add_nav_menu_link_attributes' ], 10, 2 );
	}

	/**
	 * @since 2.0
	 * @see nav_menu_link_attributes
	 * @param array $attrs
	 * @param object $item
	 * @return void
	 */
	public function add_nav_menu_link_attributes( $attrs, $item ) {
		if ( strpos( $item->post_name, 'weglot_menu_title-' ) !== false ) {
			$attrs['data-wg-notranslate'] = 'true';
		}

		return $attrs;
	}

	/**
	 * @since 2.0
	 *
	 * @return void
	 */
	public function add_nav_menu_meta_boxes() {
		add_meta_box( 'weglot_nav_link', __( 'Weglot switcher', 'weglot' ), [ $this, 'nav_menu_links' ], 'nav-menus', 'side', 'low' );
	}

	/**
	 * Output menu links.
	 * @since 2.0
	 * @see add_meta_box weglot_nav_link
	 */
	public function nav_menu_links() {
		$languages_configured = $this->language_services->get_languages_configured();

		$languages_configured = apply_filters( 'weglot_custom_nav_menu_items', $languages_configured ); ?>
		<div id="posttype-weglot-languages" class="posttypediv">
			<div id="tabs-panel-weglot-endpoints" class="tabs-panel tabs-panel-active">
				<ul id="weglot-endpoints-checklist" class="categorychecklist form-no-clear">
					<?php
					$i = 1;
		foreach ( $languages_configured as $key => $language ) : //phpcs:ignore
						?>
						<li>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( $language->getEnglishName() ); ?>
							</label>
							<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
							<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="[weglot_menu_title-<?php echo esc_attr( $language->getIso639() ); ?>]" />
							<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="[weglot_menu_current_url-<?php echo esc_attr( $language->getIso639() ); ?>]" />
							<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" />
						</li>
						<?php
						$i++;
		endforeach; //phpcs:ignore ?>
				</ul>
			</div>
			<p class="button-controls">
				<span class="list-controls">
					<a href="<?php echo esc_url( admin_url( 'nav-menus.php?page-tab=all&selectall=1#posttype-weglot-languages' ) ); ?>" class="select-all"><?php esc_html_e( 'Select all', 'weglot' ); ?></a>
				</span>
				<span class="add-to-menu">
					<button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'weglot' ); ?>" name="add-post-type-menu-item" id="submit-posttype-weglot-languages"><?php esc_html_e( 'Add to menu', 'weglot' ); ?></button>
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}
}


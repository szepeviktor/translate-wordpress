<?php

namespace WeglotWP\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Weglot\Util\Text;

/**
 * Dom Checkers
 *
 * @since 2.0
 * @version 2.0.6
 */
class Regex_Checkers_Service_Weglot {

    /**
     * @since 2.3.0
     */
    public function __construct() {
        /*$this->wc_active_services               = weglot_get_service( 'Wc_Active' );
        $this->ninja_active_services            = weglot_get_service( 'Ninja_Active_Weglot' );
        $this->caldera_active_services          = weglot_get_service( 'Caldera_Active' );
        $this->wpforms_active_services          = weglot_get_service( 'WPForms_Active' );*/
        //$this->other_translate_services         = weglot_get_service( 'Other_Translate_Service_Weglot' );
        //$this->translate_json_service           = weglot_get_service( 'Translate_Json_Service' );
        //$this->translate_json_ld_services       = weglot_get_service( 'Translate_Json_Ld' );
        //TODO : check we have only the service we need
    }

	/**
	 * @since 2.0
	 * @return array
	 */
	public function get_regex_checkers() {

	    $checkers = array();
        $thirds      = array_diff( scandir( WEGLOT_DIR . '/src/third' ), [ '..', '.' ] );
        foreach ($thirds as $third) {
            $files = array_diff( scandir(  WEGLOT_DIR . '/src/third/' . $third ), [ '..', '.' ] );

            foreach ($files as $file) {
                if (strpos($file, 'active.php') !== false) {
                    $file = Text::removeFileExtension( $file );
                    $file = str_replace( 'class-', '', $file );
                    $file = implode( '_', array_map( 'ucfirst', explode( '-', $file ) ) );
                    $service = weglot_get_service( $file );
                    if(isset($service)) {
                        $active = $service->is_active();
                        if($active) {
                            $regexDir = WEGLOT_DIR . '/src/third/' . $third . '/regexcheckers/';
                            if(is_dir($regexDir)) {
                                $regexFiles = array_diff( scandir(  WEGLOT_DIR . '/src/third/' . $third . '/regexcheckers/' ), [ '..', '.' ] );

                                $newcheckers   = array_map( function ( $filename ) use ($third) {
                                    // Thanks WPCS :)
                                    $filename = Text::removeFileExtension( $filename );
                                    $filename = str_replace( 'class-', '', $filename );
                                    $filename = implode( '_', array_map( 'ucfirst', explode( '-', $filename ) ) );
                                    return '\\WeglotWP\\Third\\' . implode( '', array_map( 'ucfirst', explode( '-', $third ) ) ) . '\\Regexcheckers\\' . $filename;
                                }, $regexFiles);

                                $checkers = array_merge($checkers, (array) $newcheckers);
                            }
                        }
                    }
                }
            }
        }
		return apply_filters( 'weglot_get_dom_checkers', $checkers );
	}

}

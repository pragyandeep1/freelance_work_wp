<?php

namespace Codestar\Module;

use Codestar\Config;

/**
 * Load languages and get locale for framework.
 *
 * ----------------------------------------------------------------------------
 * This is Codestar framework version 2.0.0, which i rebuild and redesign
 * the framework code and added new functionality to improve it, @see README.md.
 *
 * - new version 2.0.0 @copyright:
 * @package   Codestar
 * @author    mohamdio [jozoor.com]
 * @link      https://github.com/mohamdio/codestar
 * @copyright 2017 mohamdio [jozoor.com]
 * @license   GPL-2.0+
 * @version   2.0.0
 *
 * - old version 1.0.2 @copyright:
 * @package   Codestar_Framework
 * @author    Codestar [codestarlive.com]
 * @link      https://github.com/Codestar/codestar-framework
 * @copyright 2017 Codestar [codestarlive.com]
 * @license   GPL-2.0+
 * @version   1.0.2
 * ----------------------------------------------------------------------------
 *
 * @since 2.0.0
 */
class Language
{

    /**
     * Get language defaults.
     *
     * @since 2.0.0
     * @access public
     * @return mixed
     */
    public static function get_defaults()
    {

        // save language
        $multilang = array();

        // we have any language plugin activated? use it
        if (
            self::is_wpml_activated() ||
            self::is_qtranslate_activated() ||
            self::is_polylang_activated()
        ) {

            // WPML plugin is activated? use it
            if (self::is_wpml_activated()) {

                global $sitepress;
                $multilang['default']   = $sitepress->get_default_language();
                $multilang['current']   = $sitepress->get_current_language();
                $multilang['languages'] = $sitepress->get_active_languages();

            } else if (self::is_polylang_activated()) {
                // Polylang plugin is activate? use it

                global $polylang;
                $current    = pll_current_language();
                $default    = pll_default_language();
                $current    = (empty($current)) ? $default : $current;
                $poly_langs = $polylang->model->get_languages_list();
                $languages  = array();

                foreach ($poly_langs as $p_lang) {
                    $languages[$p_lang->slug] = $p_lang->slug;
                }

                $multilang['default']   = $default;
                $multilang['current']   = $current;
                $multilang['languages'] = $languages;

            } else if (self::is_qtranslate_activated()) {
                // qTranslate plugin is activate? use it

                global $q_config;
                $multilang['default']   = $q_config['default_language'];
                $multilang['current']   = $q_config['language'];
                $multilang['languages'] = array_flip(qtrans_getSortedLanguages());

            }

        } // end check language plugin activated

        // filter @hook add multilangual languages.
        $multilang = apply_filters(Config::PREFIX . 'language_defaults', $multilang);

        // return final language defaults
        return (!empty($multilang)) ? $multilang : false;

    }

    /**
     * Check if WPML plugin is activated.
     *
     * @since 2.0.0
     * @access public
     * @return boolean
     */
    public static function is_wpml_activated()
    {

        if (class_exists('\SitePress')) {
            return true;
        }
        return false;

    }

    /**
     * Check if qTranslate plugin is activated.
     *
     * @since 2.0.0
     * @access public
     * @return boolean
     */
    public static function is_qtranslate_activated()
    {

        if (function_exists('qtrans_getSortedLanguages')) {
            return true;
        }
        return false;

    }

    /**
     * Check if Polylang plugin is activated.
     *
     * @since 2.0.0
     * @access public
     * @return boolean
     */
    public static function is_polylang_activated()
    {

        if (class_exists('\Polylang')) {
            return true;
        }
        return false;

    }

}

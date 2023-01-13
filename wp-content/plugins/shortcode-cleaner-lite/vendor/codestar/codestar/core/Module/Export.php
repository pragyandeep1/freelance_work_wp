<?php

namespace Codestar\Module;

use Codestar\Helper\Helper;

/**
 * Export options backup.
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
class Export
{

    /**
     * Export options backup.
     *
     * @since 2.0.0
     * @access public
     */
    public static function options_backup()
    {
        add_action('wp_ajax_cs-export-options', array(__CLASS__, 'download_backup'));
    }

    /**
     * Actually export and download options backup.
     *
     * @since 2.0.0
     * @access public
     */
    public static function download_backup()
    {

        // get unique options id (name) inside database
        $unique = $_GET['unique'];

        // handle unique options id name for txt file name
        $unique_slug_name = str_replace('_', '-', $unique);

        // create options backup txt file
        header('Content-Type: plain/text');
        header('Content-disposition: attachment; filename=cs-backup-options' . $unique_slug_name . '-' . gmdate('d-m-Y') . '.txt');
        header('Content-Transfer-Encoding: binary');
        header('Pragma: no-cache');
        header('Expires: 0');

        // add our exported options backup into txt file
        echo Helper::encode_string(get_option($unique));

        /**
         * we must use die() at the end. If you don’t,
         * admin-ajax.php will execute it’s own die(0) code,
         * echoing an additional zero in your response.
         */
        die();

    }

}

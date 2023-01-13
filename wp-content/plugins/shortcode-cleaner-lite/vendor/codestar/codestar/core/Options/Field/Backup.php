<?php

namespace Codestar\Options\Field;

use Codestar\Helper\Helper;
use Codestar\Options\Field;

/**
 * Field: Backup
 *
 * output backup field content to import and export (download) options backup.
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
class Backup extends Field
{

    /**
     * Setup field settings.
     *
     * @since 2.0.0
     * @access public
     * @param array  $field  field settings
     * @param string $value  field value
     * @param string $unique field Unique options id (name) inside database
     */
    public function __construct($field, $value = '', $unique = '')
    {
        parent::__construct($field, $value, $unique);
    }

    /**
     * Output field element content.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function output()
    {

        echo $this->get_content_before_element();

        // we have field settings? use it
        if (isset($this->field['settings'])) {
            extract($this->field['settings']);
        }

        // get settings options
        $import_title                 = (isset($import_title)) ? $import_title : esc_html('Import a Backup');
        $import_desc                  = (isset($import_desc)) ? $import_desc : esc_html('Copy & paste your backup settings data.');
        $import_file_button_title     = (isset($import_file_button_title)) ? $import_file_button_title : esc_html('Import from File');
        $import_button_title          = (isset($import_button_title)) ? $import_button_title : 'Import a Backup';
        $warning_desc                 = (isset($warning_desc)) ? $warning_desc : esc_html('WARNING! This will overwrite all existing settings, please proceed with caution!');
        $export_title                 = (isset($export_title)) ? $export_title : esc_html('Export a Backup');
        $export_desc                  = (isset($export_desc)) ? $export_desc : esc_html('Save a backup from current settings data.');
        $export_copy_button_title     = (isset($export_copy_button_title)) ? $export_copy_button_title : esc_html('Save a backup from current settings data.');
        $export_or_text               = (isset($export_or_text)) ? $export_or_text : esc_html('OR');
        $export_download_button_title = (isset($export_download_button_title)) ? $export_download_button_title : esc_html('Download Data File');

        echo '<div class="cs-element cs-element-backup cs-field-backup-buttons import">
        <div class="cs-title"><h4>' . $import_title . '</h4><p class="cs-text-desc">' . $import_desc . '</p></div>
        <div class="cs-fieldset"><a href="#" class="button button-primary cs-import-backup-data">' . $import_file_button_title . '</a></div><div class="clear"></div>';

        // import backup
        echo '<div class="cs-show-import-backup">';
        echo '<textarea name="' . $this->unique . '[import]"' . $this->get_element_css_class() . $this->get_element_attributes() . '></textarea>';
        submit_button($import_button_title, 'primary cs-import-backup', 'backup', false);
        echo '<small class="cs-backup-warning">' . $warning_desc . '</small>';
        echo '</div>';

        echo '<div class="clear"></div></div>';

        echo '<div class="cs-element cs-element-backup cs-field-backup-buttons export">
            <div class="cs-title"><h4>' . $export_title . '</h4><p class="cs-text-desc">' . $export_desc . '</p></div>
            <div class="cs-fieldset"><a href="#" class="button button-primary cs-export-backup-data">' . $export_copy_button_title . '</a><small class="cs-or">' . $export_or_text . '</small><a href="' . admin_url('admin-ajax.php?action=cs-export-options&unique=' . $this->unique) . '" class="button button-primary cs-download-backup-data" target="_blank">' . $export_download_button_title . '</a></div><div class="clear"></div>';

        // export and download backup
        echo '<div class="cs-show-export-backup">';
        echo '<textarea name="_nonce"' . $this->get_element_css_class() . $this->get_element_attributes() . ' >' . Helper::encode_string(get_option($this->unique)) . '</textarea>';
        echo '</div>';

        echo '<div class="clear"></div></div>';

        echo $this->get_content_after_element();

    }

}

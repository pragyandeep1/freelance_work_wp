<?php

/**
 * Settings options container template.
 * included in > core/Container/Settings.php
 */

$transient  = get_transient(\Codestar\Config::SLUG . \Codestar\Config::SETTINGS . $this->unique_options_id . '-transient');
$has_nav    = (count($this->options) <= 1) ? ' cs-show-all' : '';
$section_id = (!empty($transient['section_id'])) ? $transient['section_id'] : $this->sections[0]['name'];
$section_id = \Codestar\Helper\Helper::get_var('cs-section', $section_id);
$allow_sticky_header = ($this->settings['allow_sticky_header']) ? ' allow-sticky' : '';

?>

<div class="cs-framework cs-option-framework">
<form method="post" action="options.php" enctype="multipart/form-data" id="csframework_form">
<input type="hidden" class="cs-reset" name="cs_section_id" value="<?php echo $section_id; ?>" />

<?php

// show admin notices
if (!empty($transient['errors'])) {

    global $cs_errors;

    $cs_errors = $transient['errors'];

    if (!empty($cs_errors)) {

        foreach ($cs_errors as $error) {

            if (in_array($error['setting'], array('general', 'cs-errors'))) {

            ?>

                <div class="notice cs-settings-error <?php echo $error['type']; ?> is-dismissible">
                <p><strong><?php echo $error['message']; ?></strong></p>
                </div>

			<?php
			}

        }

    } // end check !empty($cs_errors)

} // end check $this->settings['ajax_save']

settings_fields($this->unique_options_id);

// action @hook add new html content before settings page header.
do_action(\Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_before_header');

?>

<header class="cs-header<?php echo $allow_sticky_header; ?>">

<?php

// filter @hook override or update settings page header title content.
echo apply_filters(
    \Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_header_title', '<h1>' . $this->settings['title'] . '</h1>'
);

?>

<fieldset>

<?php

// action @hook add new html content before buttons in header.
do_action(\Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_before_header_buttons');

echo ($this->settings['ajax_save']) ? '<span id="cs-save-ajax">' . $this->settings['after_saved_text'] . '</span>' : '';

submit_button($this->settings['button_save_text'], 'primary cs-save', $this->unique_options_id . '[save]', false, array('data-save' => $this->settings['on_saving_text']));
submit_button($this->settings['button_reset_section_text'], 'secondary cs-restore cs-reset-confirm', $this->unique_options_id . '[reset]', false);

if ($this->settings['show_reset_all']) {
    submit_button($this->settings['button_reset_all_text'], 'secondary cs-restore cs-warning-primary cs-reset-confirm', $this->unique_options_id . '[resetall]', false);
}

// action @hook add new html content after buttons in header.
do_action(\Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_after_header_buttons');

echo (empty($has_nav) && !$this->settings['hide_show_all_options']) ? '<a href="#" class="cs-expand-all"><i class="fas fa-eye-slash"></i> ' . $this->settings['show_all_options_text'] . '</a>' : '';

?>

</fieldset>

<div class="clear"></div>
</header> <!-- end .cs-header -->

<?php

// action @hook add new html content after settings page header.
do_action(\Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_after_header');

?>

<div class="cs-body <?php echo $has_nav; ?>">
<div class="cs-nav">
<ul>

<?php

foreach ($this->options as $key => $tab) {

    if ((isset($tab['sections']))) {

        $tab_active   = \Codestar\Helper\Helper::array_search($tab['sections'], 'name', $section_id);
        $active_style = (!empty($tab_active)) ? ' style="display: block;"' : '';
        $active_list  = (!empty($tab_active)) ? ' cs-tab-active' : '';
        $tab_icon     = (!empty($tab['icon'])) ? '<i class="cs-icon ' . $tab['icon'] . '"></i>' : '';

        echo '<li class="cs-sub' . $active_list . '">';
        echo '<a href="#" class="cs-arrow">' . $tab_icon . $tab['title'] . '</a>';
        echo '<ul' . $active_style . '>';

        foreach ($tab['sections'] as $tab_section) {

            $active_tab = ($section_id == $tab_section['name']) ? ' class="cs-section-active"' : '';
            $icon       = (!empty($tab_section['icon'])) ? '<i class="cs-icon ' . $tab_section['icon'] . '"></i>' : '';
            echo '<li><a href="#"' . $active_tab . ' data-section="' . $tab_section['name'] . '">' . $icon . $tab_section['title'] . '</a></li>';

        }

        echo '</ul>';
        echo '</li>';

    } else {

        $icon = (!empty($tab['icon'])) ? '<i class="cs-icon ' . $tab['icon'] . '"></i>' : '';

        if (isset($tab['fields'])) {

            $active_list = ($section_id == $tab['name']) ? ' class="cs-section-active"' : '';
            echo '<li><a href="#"' . $active_list . ' data-section="' . $tab['name'] . '">' . $icon . $tab['title'] . '</a></li>';

        } else {

            echo '<li><div class="cs-seperator">' . $icon . $tab['title'] . '</div></li>';

        }

    }

} // end foreach $this->options

?>

</ul>
</div> <!-- end .cs-nav -->
<div class="cs-content">
<div class="cs-sections">

<?php

foreach ($this->sections as $section) {

    if (isset($section['fields'])) {

        $active_content = ($section_id == $section['name']) ? ' style="display: block;"' : '';
        echo '<div id="cs-tab-' . $section['name'] . '" class="cs-section"' . $active_content . '>';
        echo (isset($section['title']) && empty($has_nav)) ? '<div class="cs-section-title"><h3>' . $section['title'] . '</h3></div>' : '';

        foreach ($section['fields'] as $field) {
            $this->add_field($field);
        }

        echo '</div>';

    }

}

?>

</div> <!-- end .cs-sections -->
<div class="clear"></div>
</div> <!-- end .cs-content -->
<div class="cs-nav-background"></div>
</div> <!-- end .cs-body -->

<?php

// action @hook add new html content before settings page footer.
do_action(\Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_before_footer');

?>

<?php
if ($this->settings['hide_footer'] !== true) {
?>

<footer class="cs-footer">
<div class="cs-block-left">
<?php 

// filter @hook override or update footer left block content.
echo apply_filters(
    \Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_footer_left_block', $this->settings['powered_by_text']
);

?>
</div>
<div class="cs-block-right">
<?php 

// filter @hook override or update footer right block content.
echo apply_filters(
    \Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_footer_right_block', $this->settings['version_text']
);

?>
</div>
<div class="clear"></div>
</footer> <!-- end .cs-footer -->

<?php
} // end check $this->settings['hide_footer']
?>

<?php

// action @hook add new html content after settings page footer.
do_action(\Codestar\Config::PREFIX . \Codestar\Config::SETTINGS . $this->unique_options_id . '_after_footer');

?>

</form> <!-- end form -->
<div class="clear"></div>
</div> <!-- end .cs-framework -->

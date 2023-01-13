<?php

/**
 * Metaboxes options container template.
 * included in > core/Container/Metaboxes.php
 */

?>

<div class="cs-framework cs-metabox-framework">
    <input type="hidden" name="cs_section_id[<?php echo $unique; ?>]" class="cs-reset" value="<?php echo $section_id; ?>">
    <div class="cs-body<?php echo $show_all; ?>">

<?php

if ($has_nav) {

    echo '<div class="cs-nav">';
    echo '<ul>';

    $num = 0;
    foreach ($sections as $value) {

        if (!empty($value['typenow']) && $value['typenow'] !== $typenow) {
            continue;
        }

        $tab_icon = (!empty($value['icon'])) ? '<i class="cs-icon ' . $value['icon'] . '"></i>' : '';

        if (isset($value['fields'])) {

            $active_section = ((empty($section_id) && $num === 0) || $section_id == $value['name']) ? ' class="cs-section-active"' : '';
            echo '<li><a href="#"' . $active_section . ' data-section="' . $value['name'] . '">' . $tab_icon . $value['title'] . '</a></li>';

        } else {

            echo '<li><div class="cs-seperator">' . $tab_icon . $value['title'] . '</div></li>';

        }

        $num++;

    }

    echo '</ul>';
    echo '</div>';

}

?>

<div class="cs-content">
    <div class="cs-sections">

<?php

$num = 0;

foreach ($sections as $v) {

    if (!empty($v['typenow']) && $v['typenow'] !== $typenow) {
        continue;
    }

    if (isset($v['fields'])) {

        $active_content = ((empty($section_id) && $num === 0) || $section_id == $v['name']) ? ' style="display: block;"' : '';

        echo '<div id="cs-tab-' . $v['name'] . '" class="cs-section"' . $active_content . '>';
        echo (isset($v['title'])) ? '<div class="cs-section-title"><h3>' . $v['title'] . '</h3></div>' : '';

        foreach ($v['fields'] as $field_key => $field) {

            $default    = (isset($field['default'])) ? $field['default'] : '';
            $elem_id    = (isset($field['id'])) ? $field['id'] : '';
            $elem_value = (is_array($meta_value) && isset($meta_value[$elem_id])) ? $meta_value[$elem_id] : $default;
            echo \Codestar\Options\Options::add_field($field, $elem_value, $unique);

        }

        echo '</div>';

    }

    $num++;

}

?>

</div>
<div class="clear"></div>
</div>

<?php

echo ($has_nav) ? '<div class="cs-nav-background"></div>' : '';

?>

<div class="clear"></div>
</div>
</div>
<?php

/**
 * Shortcoder dialog HTML content template.
 * included in > core/Container/Shortcoder.php
 */

?>

<div id="<?php echo $this->unique_options_id;?>" class="cs-dialog cs-shortcode-dialog" title="<?php echo $this->settings['dialog_title'];?>" style="display: none;">

      <div class="cs-dialog-header">

        <select class="<?php echo (is_rtl()) ? 'chosen-rtl ' : ''; ?>cs-dialog-select" data-placeholder="<?php echo $this->settings['select_title'];?>">

          <option value=""></option>

<?php

foreach ($this->options as $group) {

    echo '<optgroup label="' . $group['title'] . '">';

    foreach ($group['shortcodes'] as $shortcode) {

        $view = (isset($shortcode['view'])) ? $shortcode['view'] : 'normal';
        echo '<option value="' . $shortcode['name'] . '" data-view="' . $view . '">' . $shortcode['title'] . '</option>';

    }

    echo '</optgroup>';

}

?>

			</select>

		</div>

	<div class="cs-dialog-load"></div>
	<div class="cs-insert-button hidden">
	<a href="#" class="button button-primary cs-dialog-insert"><?php echo $this->settings['insert_title'];?></a>
	</div>

</div>
<?php

/**
 * Add new setting under form settings
 * Hooked via filter wpforms_builder_settings_sections, priority 10
 * @param   array $sections
 * @return  array
 */
function wpforms_tracker_sheet_register_sections($sections)
{
  $sections['sheet'] = esc_html__('Google Sheet Integration', 'wpforms-tracker-sheet');
  $sections['tracker'] = esc_html__('Social Media Tracker', 'wpforms-tracker-sheet');
  $sections['kirim-email'] = esc_html__('Kirim Email Integration', 'wpforms-tracker-sheet');

  return $sections;
};

add_filter('wpforms_builder_settings_sections', 'wpforms_tracker_sheet_register_sections', 10);

/**
 * Require new setting views
 * Hooked via action wpforms_form_settings_panel_content, priority 10
 * @param   WPForms_Builder $class
 * @return  void
 */
add_action('wpforms_form_settings_panel_content', function ($class) {
  require_once(dirname(__FILE__) . '/view-setting.php');
});

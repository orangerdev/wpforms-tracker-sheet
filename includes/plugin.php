<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Load carbon fields
 * Hooked to `after_setup_theme` action, priority 10
 */
add_action('after_setup_theme', function () {
  \Carbon_Fields\Carbon_Fields::boot();
});

/**
 * Register plugin options
 * Hooked to `carbon_fields_register_fields` action, priority 10
 * @return  void
 */
add_action('carbon_fields_register_fields', function () {
  Container::make('theme_options', __("Custom Integration", " wpforms-tracker-sheet"))
    ->set_page_parent('wpforms-overview')
    ->add_fields([
      Field::make('separator', 'sep_kirim_email', __('Kirim Email', 'wpforms-tracker-sheet')),
      Field::make('text', 'kirim_email_user_api', __('User API', "wpforms-tracker-sheet")),
      Field::make('text', 'kirim_email_api_key', __('API Key', "wpforms-tracker-sheet")),
    ]);
});

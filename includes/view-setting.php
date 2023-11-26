<div class="wpforms-panel-content-section wpforms-panel-content-section-sheet" data-panel="sheet">
  <div class="wpforms-panel-content-section-title">
    <?php esc_html_e('Google Sheet Integration', 'wpforms-tracker-sheet'); ?>
  </div>
  <?php

  wpforms_panel_field(
    'textarea',
    'settings',
    'tracker_url',
    $class->form_data,
    esc_html__('Tracker URL', 'wpforms-lite'),
    [
      'default' => $class->form->tracker_url,
    ]
  );

  wpforms_panel_field(
    'text',
    'settings',
    'tracker_sheet_name',
    $class->form_data,
    esc_html__('Tracker Sheet Name', 'wpforms-lite'),
    [
      'default' => $class->form->tracker_sheet_name,
    ]
  );
  ?>
</div>
<!-- END OF GOOGLE SHEET INTEGRATION -->

<div class="wpforms-panel-content-section wpforms-panel-content-section-tracker" data-panel="tracker">
  <div class="wpforms-panel-content-section-title">
    <?php esc_html_e('Social Media Tracking', 'wpforms-tracker-sheet'); ?>
  </div>
  <?php

  wpforms_panel_field(
    'text',
    'settings',
    'tracker_pixel_id',
    $class->form_data,
    esc_html__('Pixel ID', 'wpforms-lite'),
    [
      'default' => $class->form->tracker_pixel_id,
    ]
  );

  wpforms_panel_field(
    'text',
    'settings',
    'tracker_google_conversion_id',
    $class->form_data,
    esc_html__('Google Conversion ID', 'wpforms-lite'),
    [
      'default' => $class->form->tracker_google_conversion_id,
    ]
  );

  wpforms_panel_field(
    'text',
    'settings',
    'tracker_google_conversion_label',
    $class->form_data,
    esc_html__('Google Conversion Label', 'wpforms-lite'),
    [
      'default' => $class->form->tracker_google_conversion_label,
    ]
  );

  wpforms_panel_field(
    'text',
    'settings',
    'tracker_ga_measurement_id',
    $class->form_data,
    esc_html__('GA Measurement ID', 'wpforms-lite'),
    [
      'default' => $class->form->tracker_ga_measurement_id,
    ]
  );

  wpforms_panel_field(
    'text',
    'settings',
    'tracker_ga_measurement_label',
    $class->form_data,
    esc_html__('GA Measurement Label', 'wpforms-lite'),
    [
      'default' => $class->form->tracker_ga_measurement_label,
    ]
  );
  ?>
</div>
<!-- END OF SOCIAL MEDIA TRACKING -->

<div class="wpforms-panel-content-section wpforms-panel-content-section-kirim-email" data-panel="kirim-email">
  <div class="wpforms-panel-content-section-title">
    <?php esc_html_e('Kirim Email Integration', 'wpforms-tracker-sheet'); ?>
  </div>
  <?php

  wpforms_panel_field(
    'checkbox',
    'settings',
    'kirim_email_enable',
    $class->form_data,
    esc_html__('Enable Kirim.Email Integration', 'wpforms-lite'),
    [
      'default' => $class->form->kirim_email_enable,
    ]
  );

  wpforms_panel_field(
    'text',
    'settings',
    'kirim_email_api_key',
    $class->form_data,
    esc_html__('Kirim.Email API Key', 'wpforms-lite'),
    [
      'default' => $class->form->kirim_email_,
    ]
  );

  ?>
</div>
<div class="wpforms-panel-content-section wpforms-panel-content-section-tracker" data-panel="trackers">
  <div class="wpforms-panel-content-section-title">
    <?php esc_html_e('Tracker', 'wpforms-tracker-sheet'); ?>
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
  ?>
</div>
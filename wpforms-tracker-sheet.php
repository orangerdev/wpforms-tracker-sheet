<?php

/**
 * Plugin Name:       WPForms - Tracker Sheet
 * Plugin URI:        https://ridwan-arifandi.com
 * Description:       Integrate WPForms with Google Sheets as tracker monitor
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            Ridwan Arifandi
 * Author URI:        https://ridwan-arifandi.com
 * Version:           1.0.0
 * Text Domain:       wpforms-tracker-sheet
 */

if (!defined('ABSPATH'))
  exit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

require_once(dirname(__FILE__) . '/vendor/autoload.php');

function wpforms_tracker_sheet_register_sections($sections, $form_data)
{
  $sections['tracker'] = esc_html__('Tracker', 'wpforms-tracker-sheet');
  return $sections;
}

add_filter('wpforms_builder_settings_sections', 'wpforms_tracker_sheet_register_sections', 10, 2);

add_action('wpforms_form_settings_panel_content', function ($class) {
  require_once(dirname(__FILE__) . '/view-setting.php');
});

add_filter('wpforms_process_initial_errors', function ($errors, $form_data) {
  if (!isset($_POST['wpforms']))
    return $errors;

  $post_data = wp_parse_args($_POST['wpforms'], [
    'fields' => [],
    'id' => 0,
  ]);

  if (absint($post_data['id']) === 0)
    return $errors;

  if (count($post_data['fields']) === 0)
    return $errors;

  $data = [];

  foreach ($form_data['fields'] as $id => $field) :

    switch ($field['label']):
      case "Nama":
        $key = "name";
        break;

      case "Nomor Telpon":
        $key = "phoneNumber";
        break;

      case "Umur":
        $key = "age";
        break;

      case "Jenis Kelamin":
        $key = "gender";
        break;

      case "Tinggi Badan":
        $key = "height";
        break;

      case "Berat Badan":
        $key = "weight";
        break;

    endswitch;

    $value = $post_data['fields'][$id];
    $data[$key] = $value;

  endforeach;

  $url = $form_data['settings']['tracker_url'];
  $client = new Client();
  $headers = [
    'Content-Type' => 'application/json'
  ];
  $body = json_encode($data);
  $request = new Request('POST', $url, $headers, $body);
  $res = $client->sendAsync($request)->wait();

  $response = json_decode($res->getBody()->getContents(), true);

  if ($response['status'] === 'error') :
    $errors[$post_data['id']]['footer'] = implode(PHP_EOL, explode(",", $response['message']));
  endif;


  do_action(
    "inspect",
    [
      "wpforms_process_initial_errors",
      [
        "data" => $data,
        "errors" => $errors,
        "response" => [
          "status" => $res->getStatusCode(),
          "body" => $response,
        ],
      ]
    ]
  );
  return $errors;
}, 999, 2);

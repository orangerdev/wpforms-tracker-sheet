<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Send data to kirim email
 * @param array $form_data
 * @param array $fields
 */
function wpforms_tracker_sheet_send_data_to_kirim_email(array $form_data, array $fields)
{
  $enable = $form_data['settings']['kirim_email_enable'];

  if ($enable === '' || !$enable)
    return;

  try {

    $api_key = $form_data['settings']['kirim_email_api_key'];

    if ($api_key === '')
      throw new Exception("Api key is empty");

    $username = $form_data['settings']['kirim_email_username'];

    if ($username === '')
      throw new Exception("Username is empty");

    $list_id = $form_data['settings']['kirim_email_list_id'];

    if ($list_id === '')
      throw new Exception("List ID is empty");

    $time = time();
    $generated_token = hash_hmac("sha256", $username . "::" . $api_key . "::" . $time, $api_key);

    $data = [
      'lists' => $list_id,
      'full_name' => $fields['name'],
      'email' => $fields['email'],
      'fields' => [
        'phone_number' => $fields['phoneNumber'],
        'address' => $fields['country'],
      ],
    ];

    // convert array data to params query
    $params = http_build_query($data);

    $response = wp_remote_post("https://api.kirim.email/v3/subscriber/", [
      "headers" => [
        'Auth-Id' => $username,
        'Auth-Token' => $generated_token,
        'Timestamp' => $time,
        'Content-Type' => 'application/x-www-form-urlencoded'
      ],
      "body" => $params
    ]);

    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_kirim_email",
        [
          "response" => json_decode(wp_remote_retrieve_body($response)),
          "params" => $params,
          "username" => $username,
          "generated_token" => $generated_token,
          "api_key" => $api_key
        ]
      ]
    );
  } catch (Exception $e) {
    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_kirim_email",
        [
          "error" => $e->getMessage()
        ]
      ]
    );
  } finally {
  }
}

/**
 * Send form data to google spreadsheet
 * @param array $form_data
 * @param array $fields
 */
function wpforms_tracker_sheet_send_data_to_google_sheet(array $form_data, array $fields)
{
  $url = $form_data['settings']['tracker_url'];

  if ($url === '')
    return;

  $fields['sheet'] = $form_data['settings']['tracker_sheet_name'];

  $client = new Client();

  $headers = [
    'Content-Type' => 'application/json'
  ];

  $body = json_encode($fields);

  $request = new Request('POST', $url, $headers, $body);
  $res = $client->sendAsync($request)->wait();

  $response = json_decode($res->getBody()->getContents(), true);

  do_action(
    "inspect",
    [
      "wpforms_tracker_sheet_send_data_to_google_sheet",
      $response
    ]
  );
}

/**
 * Send form data to facebook pixel
 * @param array $form_data
 * @param array $fields
 */
function wpforms_tracker_sheet_send_data_to_facebook_pixel(array $form_data, array $fields)
{
  try {
    $pixel_id = $form_data['settings']['tracker_pixel_id'];

    if ($pixel_id === '') {
      return;
    }

    $pixel_id = 'PIXEL_ID';

    $url = "https://graph.facebook.com/v13.0/$pixel_id/events";

    $data = array(
      'event_name' => 'Lead',
      'event_data' => json_encode([
        'name' => $fields['name'],
        'email' => $fields['email'],
        'phone_number' => $fields['phoneNumber'],
        'country' => $fields['country'],
      ]),
    );

    $options = array(
      CURLOPT_HTTPHEADER => array("Content-type: application/x-www-form-urlencoded"),
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => http_build_query($data),
      CURLOPT_RETURNTRANSFER => true,
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);

    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_to_facebook_pixel",
        $result
      ]
    );
  } catch (Exception $e) {
    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_to_facebook_pixel",
        [
          "error" => $e->getMessage()
        ]
      ]
    );
  }
}

/**
 * Send form data to google conversions
 * @param array $form_data
 * @param array $fields
 */
function wpforms_tracker_sheet_send_data_to_google_analytics(array $form_data, array $fields)
{
  try {
    $ga_measurement_id = $form_data['settings']['tracker_ga_measurement_id'];

    if ($ga_measurement_id === '' || !$ga_measurement_id) {
      return;
    }

    $label = $form_data['settings']['tracker_ga_measurement_label'];
    $label = $label === '' ? 'Lead Form Submission' : $label;

    $url = "https://www.google-analytics.com/mp/collect";

    $data = array(
      'v' => '1',
      'tid' => $ga_measurement_id,
      'cid' => '555',
      't' => 'event',
      'ec' => 'lead',
      'ea' => 'form_submit',
      'el' => $label,
    );

    $options = array(
      CURLOPT_HTTPHEADER => array("Content-type: application/x-www-form-urlencoded"),
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => http_build_query($data),
      CURLOPT_RETURNTRANSFER => true,
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);

    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_to_google_analytics",
        $result
      ]
    );
  } catch (Exception $e) {
    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_to_google_analytics",
        [
          "error" => $e->getMessage()
        ]
      ]
    );
  }
}

/**
 * Send form data to google conversions
 * @param array $form_data
 * @param array $fields
 */
function wpforms_tracker_sheet_send_data_to_google_conversions(array $form_data, array $fields)
{
  try {
    $google_conversion_id = $form_data['settings']['tracker_google_conversion_id'];

    if ($google_conversion_id === '' || !$google_conversion_id)
      return;

    $google_conversion_label = $form_data['settings']['tracker_google_conversion_label'];

    if ($google_conversion_label === '' || !$google_conversion_label)
      return;

    if (empty($fields['gclid']))
      return;

    $url = "https://www.googleadservices.com/pagead/conversion/app/1.0";

    $data = array(
      'v' => '1.0',
      'tid' => $google_conversion_id,
      'label' => $google_conversion_label,
      'url' => get_bloginfo('url'),
      'app_name' => 'WPForms',
      'app_version' => WPFORMS_TRACKER_SHEET_VERSION,
      'os_name' => 'WordPress',
      'os_version' => get_bloginfo('version'),
      'language' => get_bloginfo('language'),
      'sdk_version' => '1.0',
      'sdk_platform' => 'WordPress',
      'user_agent' => $_SERVER['HTTP_USER_AGENT'],
      'timestamp' => time(),
      'platform' => 'WordPress',
      'country' => $fields['country'],
      'email' => $fields['email'],
      'phone_number' => $fields['phoneNumber'],
      'name' => $fields['name'],
    );

    $options = array(
      CURLOPT_HTTPHEADER => array("Content-type: application/x-www-form-urlencoded"),
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => http_build_query($data),
      CURLOPT_RETURNTRANSFER => true,
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);

    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_to_google_conversions",
        $result
      ]
    );
  } catch (Exception $e) {
    do_action(
      "inspect",
      [
        "wpforms_tracker_sheet_send_data_to_google_conversions",
        [
          "error" => $e->getMessage()
        ]
      ]
    );
  }
}

/**
 * Trigger hook when form is submitted
 * @param   array $errors
 * @param   array $form_data
 * @return  array
 */
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

  $tracker = wpforms_tracker_sheet_get_utm_params();

  $data = [
    'name' => '',
    'phoneNumber' => '',
    'email' => '',
    'country'  => pll_current_language(),
    'source' => $tracker['source'],
    'utm_source' => $tracker['utm_source'],
    'utm_medium' => $tracker['utm_medium'],
    'utm_campaign' => $tracker['utm_campaign'],
    'fbclid' => $tracker['fbclid'],
    'gclid' => $tracker['gclid'],
    'ttclid' => $tracker['ttclid'],
  ];

  $key = '';

  foreach ($form_data['fields'] as $id => $field) :

    switch (strtoupper($field['label'])):
      case "NAME":
        $key = "name";
        break;

      case "PHONE NUMBER":
        $key = "phoneNumber";
        break;

      case "EMAIL":
        $key = "email";
        break;

    endswitch;

    if ($key === '')
      continue;

    $value = $post_data['fields'][$id];
    $data[$key] = $value;
    $key = '';

  endforeach;

  // Send data to google sheet
  wpforms_tracker_sheet_send_data_to_google_sheet($form_data, $data);

  // Send data to kirim email
  wpforms_tracker_sheet_send_data_to_kirim_email($form_data, $data);

  // Send data to facebook pixel
  wpforms_tracker_sheet_send_data_to_facebook_pixel($form_data, $data);

  // Send data to google analytics
  wpforms_tracker_sheet_send_data_to_google_analytics($form_data, $data);

  // Send data to google conversions
  wpforms_tracker_sheet_send_data_to_google_conversions($form_data, $data);

  do_action(
    "inspect",
    [
      "wpforms_process_initial_errors",
      $data
    ]
  );
  return $errors;
}, 999, 2);

add_action('template_redirect', function () {
  if (isset($_GET['action']) && $_GET['action'] === 'update-user-kirim-email') :

    $username = carbon_get_theme_option('kirim_email_user_api');
    $api_key = carbon_get_theme_option('kirim_email_api_key');

    $time = time();
    $generated_token = hash_hmac("sha256", $username . "::" . $api_key . "::" . $time, $api_key);

    $get_data = wp_parse_args($_GET, [
      'email'  => '',
      'tags'  =>  ''
    ]);

    if ($get_data['email'] === '' || $get_data['tag'] === '')
      return;

    $params = http_build_query([
      'email' => $get_data['email'],
      'tags' => $get_data['tags']
    ]);

    $response = wp_remote_request("https://api.kirim.email/v3/subscriber/email/" . $get_data['email'], [
      "headers" => [
        'Auth-Id' => $username,
        'Auth-Token' => $generated_token,
        'Timestamp' => $time,
        'Content-Type' => 'application/x-www-form-urlencoded'
      ],
      "body" => $params,
      "method" => "PUT"
    ]);

    do_action('inspect', [
      'wpforms_tracker_sheet_update_user_kirim_email',
      [
        'response' => json_decode(wp_remote_retrieve_body($response)),
        'params' => $params,
      ]
    ]);

    wp_send_json([
      'success' => true,
      'response' => json_decode(wp_remote_retrieve_body($response)),
      'params' => $params,
    ]);

    exit;

  endif;
}, -5);

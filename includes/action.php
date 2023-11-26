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

    $username = "firmanp95";
    $api_key = "gjTISaRCVNL5GmYq8lr2AsBOPhQEeFDbfirmanp95";

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

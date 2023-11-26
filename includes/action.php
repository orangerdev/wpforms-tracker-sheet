<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

function wpforms_tracker_sheet_send_data_to_google_sheet(string $url, array $data)
{
}

// add_action('wpforms_process_complete', function ($fields, $entry, $form_data) {

//   if (!isset($form_data['data']))
//     return;

//   if (!isset($form_data['data']['complete']) || !is_array($form_data['data']['complete']) || count($form_data['data']['complete']) === 0)
//     return;

//   $post_data = [
//     'name' => '',
//     'phoneNumber' => '',
//     'email' => '',
//     'country'  => '',
//     'source' => ''
//   ];

//   foreach ($form_data['data']['complete'] as $field) :
//     switch ($field['name']):
//       case "Name":
//         $key = "name";
//         break;

//       case "Phone Number":
//         $key = "phoneNumber";
//         break;

//       case "Email":
//         $key = "email";
//         break;

//     endswitch;

//     $post_data[$key] = $field['value'];;
//   endforeach;


//   $url = $form_data['settings']['tracker_url'];
//   // $client = new Client();
//   // $headers = [
//   //   'Content-Type' => 'application/json'
//   // ];
//   // $body = json_encode($fields);
//   // $request = new Request('POST', $url, $headers, $body);
//   // $res = $client->sendAsync($request)->wait();

//   // $response = json_decode($res->getBody()->getContents(), true);

//   do_action(
//     "inspect",
//     [
//       "wpforms_process_complete",
//       [
//         "data" => $post_data,
//         "language" => pll_current_language()
//       ]
//     ]
//   );
// }, 999, 3);

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
    'source' => $tracker['source']
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

  do_action(
    "inspect",
    [
      "wpforms_process_initial_errors",
      $data
    ]
  );
  return $errors;
}, 999, 2);

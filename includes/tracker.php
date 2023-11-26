<?php

/**
 * Check if current url has UTM params
 * Hooked via action template_redirect, priority 10
 * http://juarakonversi.local/?utm_source=ab1&utm_medium=cd2&utm_campaign=ef3&utm_term=gh4&utm_content=0929&fbclid=19191919191
 * @return  void 
 */
add_action('template_redirect', function () {

  // check if url params has utm query
  if (isset($_GET['utm_source'])) :

    $params = wp_parse_args($_GET, [
      'utm_source' => '',
      'utm_medium' => '',
      'utm_campaign' => '',
      'utm_term' => '',
      'utm_content' => '',
      'fbclid' => '',
      'gclid' => '',
      'ttclid' => '',
    ]);

    //set params to cookie with unlimited time
    setcookie(WPFORM_TRACKER_COOKIE_KEY, json_encode($params), time() + (86400 * 365), COOKIEPATH, COOKIE_DOMAIN, false);
  endif;
}, -1);

/**
 * Get UTM params from cookie
 * @return  array
 */
function wpforms_tracker_sheet_get_utm_params()
{
  $params = [
    'utm_source' => '',
    'utm_medium' => '',
    'utm_campaign' => '',
    'utm_term' => '',
    'utm_content' => '',
    'fbclid' => '',
    'gclid' => '',
    'ttclid' => '',
    'source' => ''
  ];

  if (isset($_COOKIE[WPFORM_TRACKER_COOKIE_KEY])) :
    $params = json_decode(
      stripslashes($_COOKIE[WPFORM_TRACKER_COOKIE_KEY]),
      true
    );

    $params = wp_parse_args($params, [
      'utm_source' => '',
      'utm_medium' => '',
      'utm_campaign' => '',
      'utm_term' => '',
      'utm_content' => '',
      'fbclid' => '',
      'gclid' => '',
      'ttclid' => '',
    ]);

  endif;

  if (!empty($params['fbclid']))
    $params['source'] = 'facebook';

  if (!empty($params['gclid']))
    $params['source'] = 'google';

  if (!empty($params['ttclid']))
    $params['source'] = 'tiktok';

  return $params;
}

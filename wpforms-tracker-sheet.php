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

define('WPFORMS_TRACKER_SHEET_VERSION', '1.0.0');
define('WPFORM_TRACKER_COOKIE_KEY', 'wts-utm');

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/includes/plugin.php');
require_once(dirname(__FILE__) . '/includes/form.php');
require_once(dirname(__FILE__) . '/includes/tracker.php');
require_once(dirname(__FILE__) . '/includes/action.php');

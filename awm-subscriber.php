<?php
/**
 * @package awm-subscriber
 */

/**
 * Plugin Name: AWM Subscriber
 * Description: Plugin subscribes a visitor to multiple AWeber lists once he subscribes to a AWeber list of your choice and confirms the subscription. Learn more on the Settings page.
 * Version:     0.9.1-alpha
 * Author:      Arkady Titenko
 * Author URI:  http://arkadyt.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') or die('Access denied.');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}

function activate_awm_subscriber_plugin() {
  inc\hooks\Activator::execute();
}

function deactivate_awm_subscriber_plugin() {
  inc\hooks\Deactivator::execute();
}

register_activation_hook(__FILE__, 'activate_awm_subscriber_plugin');
register_deactivation_hook(__FILE__, 'deactivate_awm_subscriber_plugin');

if (class_exists('inc\\Init')) {
  inc\Init::register_services();
}

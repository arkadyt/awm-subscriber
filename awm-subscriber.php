<?php
/**
 * @package awm-subscriber
 */

/**
 * Plugin Name: AWeber Multi Subscriber
 * Plugin URI:  https://example.com/plugins/the-basics/
 * Description: Plugin subscribes a user to multiple AWeber lists once he subscribes to a single list of your choice and confirms the subscription. Learn more on the Settings page.
 * Version:     0.0.0
 * Author:      Andrew Hendrix
 * Author URI:  http://arkadyt.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/* X Works only on /thank-you/ page, which is configured.
 * -> make it simpler, provide generated route.
 *
 * - AWeber API key
 *
 * read GET url properties
 * send AWeber requests
 *
 */

defined('ABSPATH') or die('Access denied.');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}

define('PLUGIN_ROOT', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));
define('PLUGIN_BASENAME', plugin_basename(__FILE__));

use inc\base\Activator;
use inc\base\Deactivator;

function activate_awm_subscriber_plugin() {
  Activator::execute();
}

function deactivate_awm_subscriber_plugin() {
  Deactivator::execute();
}

register_activation_hook(__FILE__, 'activate_awm_subscriber_plugin');
register_deactivation_hook(__FILE__, 'deactivate_awm_subscriber_plugin');

if (class_exists('inc\\Init')) {
  inc\Init::register_services();
}

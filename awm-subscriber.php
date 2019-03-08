<?php
/**
 * @package awm-subscriber
 */

/**
 * Plugin Name: AWM Subscriber
 * Description: Plugin subscribes a user to multiple AWeber lists once he subscribes to a single list of your choice and confirms the subscription. Learn more on the Settings page.
 * Version:     0.0.0
 * Author:      Arkady Titenko
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
 * KEYWORDS:
 * wp_query, 
 * the loop,
 * is_page, 
 * get_query_url,
 * get_permalink,
 * 'wp' hook
 * 'template_redirect' hook
 * get_template_page_slug
 * template_redirect | template_include hooks
 */

defined('ABSPATH') or die('Access denied.');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}

function activate_awm_subscriber_plugin() {
  inc\base\Activator::execute();
}

function deactivate_awm_subscriber_plugin() {
  inc\base\Deactivator::execute();
}

register_activation_hook(__FILE__, 'activate_awm_subscriber_plugin');
register_deactivation_hook(__FILE__, 'deactivate_awm_subscriber_plugin');

if (class_exists('inc\\Init')) {
  inc\Init::register_services();
}

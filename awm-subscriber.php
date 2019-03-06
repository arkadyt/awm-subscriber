<?php
/**
 * @package awm-subscriber
 * @version 0.0.0
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

if (!defined('ABSPATH')) {
  echo 'WARNING: Attempt to access plugin code outside of Wordpress environment. Access denied.';
  exit;
}

class AWMSubscriber {
  public $pluginBasename;

  function __construct() {
    $this->pluginBasename = plugin_basename(__FILE__);
  }

  public function register() {
    add_action('admin_menu', array($this, 'add_admin_page'));
    add_filter("plugin_action_links_$this->pluginBasename", array($this, 'inject_settings_link'));
  }

  public function inject_settings_link($links) {
    $settings_link = '<a href="http://localhost:5002/wp-admin/admin.php?page=awm_subscriber">Settings</a>';
    array_push($links, $settings_link);
    return $links;
  }

  public function add_admin_page() {
    add_menu_page(
      'AWeber Multi Subscriber',
      'AWM Subscriber',
      'manage_options',
      'awm_subscriber',
      array($this, 'admin_index'),
      'dashicons-admin-tools',
      100
    );
  }

  public function admin_index() {
    require_once plugin_dir_path(__FILE__) . 'pages/settings.php';
  }

  function activate() {
    // setup options with dummy values:
    // - aweber_key
    // - thank_you_page_url
    // Don't forget to prefix var names!
  }

  function deactivate() {
    // just don't get triggered any more
    // don't have to do anything for this
  }

  static function uninstall() {
    // delete all options from the database
  }
}

if (class_exists('AWMSubscriber')) {
  $awmSubscriber = new AWMSubscriber();
  $awmSubscriber->register();
}

register_activation_hook(__FILE__, array($awmSubscriber, 'activate'));
register_deactivation_hook(__FILE__, array($awmSubscriber, 'deactivate'));
register_uninstall_hook(__FILE__, array($awmSubscriber, 'uninstall'));

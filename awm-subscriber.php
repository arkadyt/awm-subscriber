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
  function activate() {
    // setup options with dummy values:
    // - aweber_key
    // - thank_you_page_url
    // Don't forget to prefix var names!

    add_action('admin_menu', array($this, 'add_admin_section'));
  }

  function add_admin_section() {
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
    // req template
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
  $awmSubscriber = new AWMSubscriber('2PAC IS BAK');
}

register_activation_hook(__FILE__, array($awmSubscriber, 'awm_subscriber_activate'));
register_deactivation_hook(__FILE__, array($awmSubscriber, 'awm_subscriber_deactivate'));
register_uninstall_hook(__FILE__, array($awmSubscriber, 'awm_subscriber_uninstall'));

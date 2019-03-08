<?php
/**
 * @package awm-subscriber
 */

namespace inc\pages;

use inc\base\BaseController;

final class Settings extends BaseController {
  /**
   * Triggers everything that this class is responsible for.
   * Do not rename the method. inc\Init is expecting to find $this->register() here.
   */
  public function register() {
    add_action('admin_menu', array($this, 'add_settings_page'));
  }

  /**
   * Adds new 'Admin' page
   */
  public function add_settings_page() {
    add_options_page(
      'AWM Subscriber',
      'AWM Subscriber',
      'manage_options',
      'awm_subscriber_settings',
      array($this, 'get_settings_page_template')
    );
  }

  /**
   * Returns path to Admin page template.
   */
  public function get_settings_page_template() {
    require_once $this->plugin_root . 'templates/settings.php';
  }
}

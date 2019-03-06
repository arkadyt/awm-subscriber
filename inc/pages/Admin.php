<?php
/**
 * @package awm-subscriber
 */

namespace inc\pages;

final class Admin {
  /**
   * Triggers everything that this class is responsible for.
   * Do not rename the method. inc\Init is expecting to find $this->register() here.
   */
  public function register() {
    add_action('admin_menu', array($this, 'add_admin_page'));
  }

  /**
   * Adds new 'Admin' page
   */
  public function add_admin_page() {
    add_menu_page(
      'AWeber Multi Subscriber',
      'AWM Subscriber',
      'manage_options',
      'awm_subscriber',
      array($this, 'get_admin_page_template'),
      'dashicons-admin-tools',
      100
    );
  }

  /**
   * Returns path to Admin page template.
   */
  public function get_admin_page_template() {
    require_once PLUGIN_ROOT . 'templates/admin.php';
  }
}

<?php
/**
 * @package awm-subscriber
 */

namespace inc;

use inc\base\BaseController;

final class PluginLinks extends BaseController {
  /**
   * Triggers everything that this class is responsible for.
   * Do not rename the method. inc\Init is expecting to find $this->register() here.
   */
  public function register() {
    add_filter('plugin_action_links_' . $this->plugin_basename, array($this, 'inject_settings_link'));
  }

  /**
   * Inserts 'Settings' link at the 'Installed plugins' page for this plugin.
   */
  public function inject_settings_link($links) {
    $settings_link = '<a href="' . site_url() . '/wp-admin/admin.php?page=awm_subscriber">Settings</a>';
    array_push($links, $settings_link);
    return $links;
  }
}



<?php
/**
 * @package awm-subscriber
 */

namespace inc\base;

class BaseController {
  protected $plugin_root;
  protected $plugin_url;
  protected $plugin_basename;

  protected $groupname_plugin_settings;
  protected $optname_response_code;

  protected $app_id;

  public function __construct() {
    $this->plugin_root = plugin_dir_path(dirname(__FILE__, 2));
    $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
    $this->plugin_basename = plugin_basename(dirname(__FILE__, 3)) . '/awm-subscriber.php';

    $this->groupname_plugin_settings = 'awm_subscriber_settings';
    $this->optname_response_code = 'awm_subscriber_response_code';

    $this->app_id = 'dadb3b05';
  }
}

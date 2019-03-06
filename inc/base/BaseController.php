<?php
/**
 * @package awm-subscriber
 */

namespace inc\base;

class BaseController {
  public $plugin_root;
  public $plugin_url;
  public $plugin_basename;

  public function __construct() {
    $this->plugin_root = plugin_dir_path(dirname(__FILE__, 2));
    $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
    $this->plugin_basename = plugin_basename(dirname(__FILE__, 3)) . '/awm-subscriber.php';
  }
}

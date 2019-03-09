<?php
/**
 * @package awm-subscriber
 */

namespace inc\api\interfaces;

interface Integration {
  public function get_authorize_url();
  public function initialize($response_str);
  public function get($path);
  public function post($path, $payload);
}

<?php
/**
 * @package awm-subscriber
 */

namespace inc\api;

interface Integration {
  public function get_authorize_url();
  public function initialize($response_str);
  public function get($path);
  public function post($path, $payload);
}

<?php
/**
 * @package awm-subscriber
 */

/**
 * AWM Subscriber
 * Copyright (C) 2020 Andrew Titenko
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.html.
 */

namespace inc\base;

class BaseController {
  protected $plugin_root;
  protected $plugin_url;
  protected $plugin_basename;

  protected $groupname_plugin_settings;
  protected $optname_aweber_response_code;
  protected $optname_aweber_consumer_key;
  protected $optname_aweber_consumer_secret;
  protected $optname_aweber_token;
  protected $optname_aweber_token_secret;
  protected $optname_aweber_customer_id;

  protected $aweber_dev_app_id;

  public function __construct() {
    $this->plugin_root          = plugin_dir_path(dirname(__FILE__, 2));
    $this->plugin_url           = plugin_dir_url(dirname(__FILE__, 2));
    $this->plugin_basename      = plugin_basename(dirname(__FILE__, 3)) . '/awm-subscriber.php';

    $this->groupname_plugin_settings           = 'awm_subscriber_settings';
    $this->optname_aweber_response_code        = 'awm_subscriber_response_code';
    $this->optname_aweber_consumer_key         = 'awm_subscriber_consumer_key';
    $this->optname_aweber_consumer_secret      = 'awm_subscriber_consumer_secret';
    $this->optname_aweber_token                = 'awm_subscriber_token';
    $this->optname_aweber_token_secret         = 'awm_subscriber_token_secret';
    $this->optname_aweber_customer_id          = 'awm_subscriber_customer_id';

    $this->aweber_dev_app_id = 'dadb3b05';
  }
}

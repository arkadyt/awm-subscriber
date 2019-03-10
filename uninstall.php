<?php
/**
 * @package awm-subscriber
 */

/**
 * AWM Subscriber
 * Copyright (C) 2019 Arkady Titenko
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

defined('WP_UNINSTALL_PLUGIN') or die('Access denied.');

use inc\base\BaseController;

final class Uninstall extends BaseController {
  public function uninstall() {
    delete_option($this->optname_aweber_response_code);
    delete_option($this->optname_aweber_consumer_key);
    delete_option($this->optname_aweber_consumer_secret);
    delete_option($this->optname_aweber_token);
    delete_option($this->optname_aweber_token_secret);
    delete_option($this->optname_aweber_customer_id);

    unregister_setting(
      $this->groupname_plugin_settings,
      $this->optname_aweber_response_code
    );
  }
}

new Uninstall().uninstall();

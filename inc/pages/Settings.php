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

namespace inc\pages;

use inc\base\BaseController;

final class Settings extends BaseController {
  /**
   * Triggers everything that this class is responsible for.
   * Do not rename the method. inc\Init is expecting to find $this->register() here.
   */
  public function register() {
    add_action('admin_menu', array($this, 'add_settings_page'));
    add_action('admin_init', array($this, 'register_settings'));
  }

  /**
   * Adds new 'Settings' page
   */
  public function add_settings_page() {
    add_options_page(
      'AWM Subscriber',
      'AWM Subscriber',
      'manage_options',
      $this->groupname_plugin_settings,
      array($this, 'get_settings_page_template')
    );
  }

  /**
   * Returns path to Settings page template.
   */
  public function get_settings_page_template() {
    require_once $this->plugin_root . 'templates/settings.php';
  }

  /**
   * Registers settings that form on the template will use.
   */
  public function register_settings() {
    register_setting(
      $this->groupname_plugin_settings,
      $this->optname_aweber_response_code
    );
  }
}

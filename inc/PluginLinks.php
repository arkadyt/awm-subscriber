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
   * Inserts 'Settings' link in the plugin row at the 'Installed plugins' page.
   */
  public function inject_settings_link($links) {
    $settings_link = '<a href="' 
      . site_url() 
      . '/wp-admin/options-general.php?page=' 
      . $this->groupname_plugin_settings 
      . '">Settings</a>';
    array_push($links, $settings_link);
    return $links;
  }
}



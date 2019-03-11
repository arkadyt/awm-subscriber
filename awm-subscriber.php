<?php
/**
 * @package awm-subscriber
 */

/**
 * Plugin Name: AWM Subscriber
 * Description: Plugin subscribes a visitor to multiple AWeber lists once he subscribes to a AWeber list of your choice and confirms the subscription. Learn more on the Settings page.
 * Version:     1.0.2
 * Author:      Andrew Titenko
 * Author URI:  http://arkadyt.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
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

defined('ABSPATH') or die('Access denied.');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
  require_once dirname(__FILE__) . '/vendor/autoload.php';
}

function activate_awm_subscriber_plugin() {
  inc\hooks\Activator::execute();
}

function deactivate_awm_subscriber_plugin() {
  inc\hooks\Deactivator::execute();
}

register_activation_hook(__FILE__, 'activate_awm_subscriber_plugin');
register_deactivation_hook(__FILE__, 'deactivate_awm_subscriber_plugin');

if (class_exists('inc\\Init')) {
  inc\Init::register_services();
}

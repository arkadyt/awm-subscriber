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

final class Init {
  private static $instances = array();

  /**
   * Store all the classes inside an array
   * @return array Full list of classes
   */
  private static function get_services() {
    return array(
      PluginLinks::class,
      pages\Settings::class,
      core\Subscriber::class
    );
  }

  /**
   * Loop through the classes, initialize them 
   * and call the register() method if it exists
   */
  public static function register_services() {
    foreach(self::get_services() as $class) {
      $service = self::instantiate($class);

      if (!isset(self::$instances[$class]) && method_exists($service, 'register')) {
        self::$instances[$class] = $service;
        $service->register();
      }
    }
  }

  /**
   * Returns a particular class instance.
   */
  public static function get_instance($class) {
    return self::$instances[$class];
  }
  
  /**
   * Initialize the class
   * @param class $class -- class from the services array to instantiate
   * @return class instance -- new instance of the $class
   */
  private static function instantiate($class) {
    return new $class();
  }
}

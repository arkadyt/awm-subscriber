<?php
/**
 * @package awm-subscriber
 */

namespace inc;

final class Init {
  /**
   * Store all the classes inside an array
   * @return array Full list of classes
   */
  public static function get_services() {
    return array(
      pages\Admin::class,
      base\SettingsLink::class,
      base\Subscriber::class
    );
  }

  /**
   * Loop through the classes, initialize them 
   * and call the register() method if it exists
   */
  public static function register_services() {
    foreach(self::get_services() as $class) {
      $service = self::instantiate($class);
      if (method_exists($service, 'register')) {
        $service->register();
      }
    }
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

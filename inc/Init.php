<?php
/**
 * @package awm-subscriber
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

<?php
/**
 * @package awm-subscriber
 * @version 0.0.0
 */

/**
 * Plugin Name: AWeber Multi Subscriber
 * Plugin URI:  https://example.com/plugins/the-basics/
 * Description: TODO
 * Version:     0.0.0
 * Author:      Andrew Hendrix
 * Author URI:  http://arkadyt.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wporg
 * Domain Path: /languages
 */

/* X Works only on /thank-you/ page, which is configured.
 * -> make it simpler, provide generated route.
 *
 * - AWeber API key
 *
 * read GET url properties
 * send AWeber requests
 *
 */

class AWM_Subscriber {
  function activate() {

  }

  function deactivate() {

  }

  function uninstall() {

  }
}

register_activation_hook(__FILE__, 'awm_subscriber_activate');
register_deactivation_hook(__FILE__, 'awm_subscriber_deactivate');
register_uninstall_hook(__FILE__, 'awm_subscriber_uninstall');

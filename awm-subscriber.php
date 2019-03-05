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

register_activation_hook(__FILE__, 'awm_subscriber_activate');
register_deactivation_hook(__FILE__, 'awm_subscriber_deactivate');
register_uninstall_hook(__FILE__, 'awm_subscriber_uninstall');

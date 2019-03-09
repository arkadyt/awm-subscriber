<?php
/**
 * @package awm-subscriber
 */

/**
 * example AWeber request url (all joined):
 *
 * ?email=wp-testing%40arkadyt.com&from=wp-testing%40arkadyt.com&meta_adtracking=my%20web%20form&meta_message=1001
 * &name=Thug&unit=awlist5279237&add_url=http%3A%2F%2Fwp-testing.arkadyt.com%2Fthank-you%2F&add_notes=255.255.255.255
 * &custom%20awlist5279237=yes&custom%20awlist5000207=yes&custom%20awlist01290129=yes
 */

namespace inc\core;

use inc\api\AWeberIntegration;
use inc\base\BaseController;

/**
 * Core service of this plugin.
 */
final class Subscriber extends BaseController {
  private const OPTNAME_AWEBER_CUSTOMER_ID = 'awm_subscriber_customer_id';

  public $authorize_url;

  private $aweber_client;

  /**
   * Spawns AWeber client and prepares authorization url.
   */
  public function __construct() {
    parent::__construct();
    $this->aweber_client = new AWeberIntegration($this->app_id);
    $this->authorize_url = $this->aweber_client->get_authorize_url();
  }

  /**
   * Initializes the Subscriber service.
   * Can't merge it with register() function since we depend on
   * the response code supplied by user upon plugin authorization.
   */
  public function initialize($response_code) {
    $this->aweber_client->initialize($response_code);

    // AWeber does not provide a way to get the current user.
    // That means that every user of this plugin would have to use
    // a separate developer app.
    $id = $this->aweber_client->get('accounts')['entries'][0]['id'];
    update_option(self::OPTNAME_AWEBER_CUSTOMER_ID, $id);
  } 

  /**
   * Installs various hooks.
   * Do not change the name of the method.
   */
  public function register() {
    add_filter('query_vars', array($this, 'add_query_vars_filter'));
    add_action('init', array($this, 'intercept_get_request'));
    add_action('wp', array($this, 'attempt_to_subscribe'));
  }

  /**
   * Unsets query variables forbidden by Wordpress.
   * Like 'name', AWeber sends it in the GET request.
   *
   * Presence of those in the query string causes 
   * Wordpress to respond with 404 page.
   */
  public function intercept_get_request() {
    if (isset($_GET['name'])) {
      $_GET['subscriberName'] = $_GET['name'];
      unset($_GET['name']);
    }
  }

  /**
   * Teaches WP_Query to recognize custom variables from the request url.
   */
  public function add_query_vars_filter($vars) {
    $vars[] = "unit";
    $vars[] = "meta_adtracking";
    $vars[] = "meta_message";
    $vars[] = "email";
    $vars[] = "add_url";
    $vars[] = "add_notes";
    return $vars;
  }

  /**
   * Though WP_Query does not work with dynamic parms.
   * Extracts dynamic aweber list id's from the AWeber request url.
   */
  public function extract_awlists_from_url($current_page_fullurl) {
    // splits url into chunks: protocol, user, password, host, port, path, query etc.
    $parsed_url = parse_url($current_page_fullurl);
    // decodes query string, will create $query_str var (an associative array with query parms)
    parse_str($parsed_url['query'], $query_str);

    $aweber_lists = array();
    foreach ($query_str as $key => $value) {
      if ($value === 'yes' && strpos($key, 'awlist') !== false) {
        $aweber_lists[] = str_replace('custom_awlist', '', $key);
      }
    }
    return $aweber_lists;
  }

  /**
   * Calls the subscribe function if user clicked on the confirmation link from email.
   */
  public function attempt_to_subscribe() {
    global $wp;

    // AWeber specific query parameters.
    if (
      isset($wp->query_vars['unit']) &&
      isset($wp->query_vars['meta_adtracking']) &&
      isset($wp->query_vars['meta_message']) &&
      isset($wp->query_vars['email']) &&
      isset($wp->query_vars['add_url']) &&
      isset($wp->query_vars['add_notes'])
    ) {
      $current_page_fullurl = home_url(add_query_arg(array($_GET), $wp->request));
      $aweber_lists = $this->extract_awlists_from_url($current_page_fullurl);
      $this->subscribe($wp->query_vars['email'], $aweber_lists);
    }
  }

  /**
   * Subscribes user to multiple AWeber lists of his choice.
   */
  public function subscribe($subscriberEmail, $aweber_lists) {
    $aweber_customer_id = get_option(self::OPTNAME_AWEBER_CUSTOMER_ID);
    foreach ($aweber_lists as $listId) {
      $payload = array(
        'email' => $subscriberEmail
      );
      $res = $this->aweber_client->post(
        "accounts/$aweber_customer_id/lists/$listId/subscribers",
        $payload
      );
    }
    return $res;
  }
}

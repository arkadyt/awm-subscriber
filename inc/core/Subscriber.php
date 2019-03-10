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

/**
 * Example of AWeber subscription confirmation URL (all joined):
 *
 * ?email=subdomain%40domain.com
 * &from=subdomain%40domain.com
 * &meta_adtracking=my%20web%20form
 * &meta_message=1001
 * &name=Bob
 * &unit=awlist5279237
 * &add_url=http%3A%2F%2Fsubdomain.domain.com%2F
 * &add_notes=255.255.255.255
 * &custom%20awlist5279237=yes
 * &custom%20awlist5000207=yes
 * &custom%20awlist01290129=yes
 *
 * add_url is not a reliable way of detecting if somebody
 * requested the confirmation page.
 * -> AWeber's unique combination of query parms is used.
 *
 * if confirmation page URL supplied to AWeber already
 * contained some parameters, AWeber would just append his own
 * parameters to the end.
 * If some parm names were already used, AWeber would not care,
 * it would just add everything on top.
 */

namespace inc\core;

use inc\api\AWeberIntegration;
use inc\base\BaseController;

/**
 * Core service of this plugin.
 */
final class Subscriber extends BaseController {
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
    $result = $this->aweber_client->initialize($response_code);
    if ($result === false) return $result;

    // AWeber does not provide a way to get the current user.
    // That means that every user of this plugin would have to use
    // a separate developer app.
    $id = $this->aweber_client->get('accounts')['entries'][0]['id'];
    update_option($this->optname_aweber_customer_id, $id);
    return $result;
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
    $aweber_customer_id = get_option($this->optname_aweber_customer_id);
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

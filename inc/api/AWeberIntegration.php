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

namespace inc\api;

use inc\api\interfaces\Integration;
use inc\base\BaseController;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

/**
 * Example usage.
 *
 * 1. Spawn the new Integration:
 *   $aweber_int = new AWeberIntegration('c7cb7baa');
 *   $url_for_user_to_visit = $aweber_int->get_authorize_url();
 *
 * 2. Get the response string and initialize the Integration:
 *   $response = ... // user visits the url, logs in, gets the response code
 *   $aweber_int->initialize($response);
 *
 * 3. Use it:
 *   $response = $aweber_int->get(...);
 *   $response = $aweber_int->post(...);
 *
 */
final class AWeberIntegration extends BaseController implements Integration {
  public const URL_API = 'https://api.aweber.com/1.0';
  public const URL_ACCESS_TOKEN = 'https://auth.aweber.com/1.0/oauth/access_token';

  private $stack, $client;
  private $app_id, $authorize_url;

  /**
   * Requires AWeber developer app id.
   * Learn more at https://labs.aweber.com
   */
  public function __construct($app_id) {
    parent::__construct();

    $headers = array(
      'Content-Type'    => 'application/json',
      'Accept'          => 'application/json'
    );
    $this->stack = HandlerStack::create();
    $this->client = new Client([
      'base_uri' => self::URL_API,
      'handler' => $this->stack,
      'headers' => $headers,
      'auth' => 'oauth'
    ]);

    $this->app_id = $app_id;
    $this->authorize_url = "https://auth.aweber.com/1.0/oauth/authorize_app/$this->app_id";
  }

  /**
   * Returns the authorization url that Integration users
   * would visit to get the response code that then will be used
   * to get the permanent API keys.
   */
  public function get_authorize_url() {
    return $this->authorize_url;
  }

  /**
   * Initializes the integration.
   * Gets permanent tokens, saves them to database (overwrites).
   */
  public function initialize($response_str) {
    $tokens = $this->authorize($response_str);
    if (!$tokens) return false;

    update_option($this->optname_aweber_consumer_key, $tokens['consumer_key']);
    update_option($this->optname_aweber_consumer_secret, $tokens['consumer_secret']);
    update_option($this->optname_aweber_token, $tokens['token']);
    update_option($this->optname_aweber_token_secret, $tokens['token_secret']);
    return true;
  }

  /**
   * Retrieves permanent API keys from AWeber.
   */
  private function authorize($response_str) {
    $response_keys = explode('|', $response_str);
    $request_middleware = new Oauth1(array(
      'consumer_key'    => $response_keys[0],
      'consumer_secret' => $response_keys[1],
      'token'           => $response_keys[2],
      'token_secret'    => $response_keys[3],
      'verifier'        => $response_keys[4]
    ));
    $this->stack->push($request_middleware);
    try {
      $response = $this->client->post(self::URL_ACCESS_TOKEN);
      $keys = array();
      parse_str($response->getBody(), $keys);

      return array(
        'consumer_key'    => $response_keys[0],
        'consumer_secret' => $response_keys[1],
        'token'           => $keys['oauth_token'],
        'token_secret'    => $keys['oauth_token_secret']
      );
    } catch (\Exception $e) {
      return false;
    } finally {
      $this->stack->remove($request_middleware);
    }
  }

  /**
   * GET action.
   */
  public function get($path) {
    $request_middleware = new Oauth1(array(
      'consumer_key'    => get_option($this->optname_aweber_consumer_key),
      'consumer_secret' => get_option($this->optname_aweber_consumer_secret),
      'token'           => get_option($this->optname_aweber_token),
      'token_secret'    => get_option($this->optname_aweber_token_secret)
    ));
    $this->stack->push($request_middleware);
    try {
      $res = $this->client->get(self::URL_API . '/' . $path);
      return json_decode($res->getBody(), true);
    } catch (\Exception $e) {
      return false;
    } finally {
      $this->stack->remove($request_middleware);
    }
  }

  /**
   * POST action.
   */
  public function post($path, $payload) {
    $request_middleware = new Oauth1(array(
      'consumer_key'    => get_option($this->optname_aweber_consumer_key),
      'consumer_secret' => get_option($this->optname_aweber_consumer_secret),
      'token'           => get_option($this->optname_aweber_token),
      'token_secret'    => get_option($this->optname_aweber_token_secret)
    ));
    $this->stack->push($request_middleware);
    try {
      $res = $this->client->post(self::URL_API . '/' . $path, array( 'json' => $payload ));
      return json_decode($res->getBody(), true);
    } catch (\Exception $e) {
      return false;
    } finally {
      $this->stack->remove($request_middleware);
    }
  }
}

<?php
/**
 * @package awm-subscriber
 */

namespace inc\api;

use inc\api\interfaces\Integration;
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
final class AWeberIntegration implements Integration {
  public const URL_API = 'https://api.aweber.com/1.0';
  public const URL_ACCESS_TOKEN = 'https://auth.aweber.com/1.0/oauth/access_token';

  private const OPTNAME_CONSUMER_KEY = 'awm_subscriber_consumer_key';
  private const OPTNAME_CONSUMER_SECRET = 'awm_subscriber_consumer_secret';
  private const OPTNAME_TOKEN = 'awm_subscriber_token';
  private const OPTNAME_TOKEN_SECRET = 'awm_subscriber_token_secret';

  private $stack, $client;
  private $app_id, $authorize_url;

  /**
   * Requires AWeber developer app id.
   * Learn more at https://labs.aweber.com
   */
  public function __construct($app_id) {
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
    update_option(self::OPTNAME_CONSUMER_KEY, $tokens['consumer_key']);
    update_option(self::OPTNAME_CONSUMER_SECRET, $tokens['consumer_secret']);
    update_option(self::OPTNAME_TOKEN, $tokens['token']);
    update_option(self::OPTNAME_TOKEN_SECRET, $tokens['token_secret']);
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
    $response = $this->client->post(self::URL_ACCESS_TOKEN);

    $keys = array();
    parse_str($response->getBody(), $keys);

    $this->stack->remove($request_middleware);
    return array(
      'consumer_key'    => $response_keys[0],
      'consumer_secret' => $response_keys[1],
      'token'           => $keys['oauth_token'],
      'token_secret'    => $keys['oauth_token_secret']
    );
  }

  /**
   * GET action.
   */
  public function get($path) {
    $request_middleware = new Oauth1(array(
      'consumer_key'    => get_option(self::OPTNAME_CONSUMER_KEY),
      'consumer_secret' => get_option(self::OPTNAME_CONSUMER_SECRET),
      'token'           => get_option(self::OPTNAME_TOKEN),
      'token_secret'    => get_option(self::OPTNAME_TOKEN_SECRET)
    ));
    $this->stack->push($request_middleware);
    $res = $this->client->get(self::URL_API . '/' . $path);
    $this->stack->remove($request_middleware);

    if ($res->getStatusCode() === 200) {
      return json_decode($res->getBody(), true);
    } else {
      return false;
    }
  }

  /**
   * POST action.
   */
  public function post($path, $payload) {
    $request_middleware = new Oauth1(array(
      'consumer_key'    => get_option(self::OPTNAME_CONSUMER_KEY),
      'consumer_secret' => get_option(self::OPTNAME_CONSUMER_SECRET),
      'token'           => get_option(self::OPTNAME_TOKEN),
      'token_secret'    => get_option(self::OPTNAME_TOKEN_SECRET)
    ));
    $this->stack->push($request_middleware);
    $res = $this->client->post(self::URL_API . '/' . $path, array( 'json' => $payload ));
    $this->stack->remove($request_middleware);

    if ($res->getStatusCode() === 200) {
      return json_decode($res->getBody(), true);
    } else {
      return false;
    }
  }
}

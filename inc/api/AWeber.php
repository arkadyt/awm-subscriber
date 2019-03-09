<?php
/**
 * @package awm-subscriber
 */

namespace inc\api;

/**
 * Example usage:
 *
 * $aweber_int = new AWeberIntegration('c7cb7baa');
 * $url_for_user_to_visit = $aweber_int->get_authorize_url();
 *
 * $response = ... // user visits the url, logs in, gets the response code
 * $aweber_int->initialize($response);
 *
 * $aweber_int->get(...);
 * $aweber_int->post(...);
 */
final class AWeberIntegration implements API {
  public const URL_API = 'https://api.aweber.com/1.0';
  public const URL_ACCESS_TOKEN = 'https://auth.aweber.com/1.0/oauth/access_token';

  private const OPTNAME_CONSUMER_KEY = 'awm_subscriber_consumer_key';
  private const OPTNAME_CONSUMER_SECRET = 'awm_subscriber_consumer_secret';
  private const OPTNAME_TOKEN = 'awm_subscriber_token';
  private const OPTNAME_TOKEN_SECRET = 'awm_subscriber_token_secret';

  private $stack, $client;
  private $app_id, $authorize_url;

  public function __construct($app_id) {
    $this->stack = HandlerStack::create();
    $this->client = new Client([
      'base_uri' => self::URL_API,
      'handler' => $this->stack,
      'auth' => 'oauth'
    ]);

    $this->app_id = $app_id;
    $this->authorize_url = "https://auth.aweber.com/1.0/oauth/authorize_app/$this->app_id";
  }

  public function get_authorize_url() {
    return $this->authorize_url;
  }

  public function initialize($response) {
    $tokens = $this->get_permanent_tokens($this->authorize_url);
    update_option(self::OPTNAME_CONSUMER_KEY, $tokens['consumer_key']);
    update_option(self::OPTNAME_CONSUMER_SECRET, $tokens['consumer_secret']);
    update_option(self::OPTNAME_TOKEN, $tokens['oauth_token']);
    update_option(self::OPTNAME_TOKEN_SECRET, $tokens['oauth_token_secret']);
  }

  private function get_permanent_tokens($authorize_url) {
    $response_keys = explode('|', $authorize_url);
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
    parse_str($response->getBody, $keys);

    $stack->remove($request_middleware);
    return array_merge(array_slice($response_keys, 0, 2), $keys);
  }

  public function get($path) {
    $request_middleware = new Oauth1(array(
      'consumer_key'    => get_option(self::OPTNAME_CONSUMER_KEY),
      'consumer_secret' => get_option(self::OPTNAME_CONSUMER_SECRET),
      'token'           => get_option(self::OPTNAME_TOKEN),
      'token_secret'    => get_option(self::OPTNAME_TOKEN_SECRET)
    ));
    $stack->push($request_middleware);
    $res = $client->get(self::URL_API . '/' . $path);
    $stack->remove($request_middleware);

    if ($res->getStatusCode() === 200) {
      return $res->getBody();
    } else {
      return false;
    }
  }
}

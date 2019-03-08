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

namespace inc\base;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

/**
 * Core service of this plugin.
 */
final class Subscriber extends BaseController {
  /**
   * Installs various hooks.
   * Do not change the name of the method.
   */
  public function register() {
    add_filter('query_vars', array($this, 'add_query_vars_filter'));
    add_action('init', array($this, 'intercept_get_request'));
    add_action('wp', array($this, 'subscribe'));
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
    $vars[] = "email";
    $vars[] = "add_url";
    return $vars;
  }

  /**
   * Extracts aweber list id's from the AWeber request url.
   * Since those are dynamic, I can't use query vars filter hook.
   *
   * The dynamic nature comes from the way users of this plugin
   * will setup their custom checkboxes on the form.
   * 
   * AWeber sends custom_field_names=yes per every checked checkbox,
   * and at the moment of writing this is the only way of receiving
   * the information from AWeber.
   *
   * Giving checkbox fields names corresponding to their respective
   * AWeber lists allows me to use this information here.
   *
   * Though with one drawback, I have to use manual parser.
   */
  public function extract_awlists_from_url($current_page_fullurl) {
    // splits url into chunks: protocol, user, password, host, port, path, query etc.
    $parsed_url = parse_url($current_page_fullurl);
    // decodes query string, will create $query_str var (an associative array with query parms)
    parse_str($parsed_url['query'], $query_str);

    $aweber_lists = array();
    foreach ($query_str as $key => $value) {
      if ($value === 'yes' && strpos($key, 'awlist') !== false) {
        $aweber_lists[] = str_replace('custom_', '', $key);
      }
    }
    return $aweber_lists;
  }

  /**
   * Will try to subscribe user to multiple AWeber lists of his choice
   * once he visits the confirmation page.
   * AWeber supplies chosen list id-s in the GET url.
   */
  public function subscribe() {
    global $wp;

    $current_page_slug = $wp->query_vars['pagename'];
    $current_page_url = home_url() . $current_page_slug;

    $confirm_page_url = $wp->query_vars['add_url'];
    $confirm_page_slug = str_replace('/', '', parse_url($confirm_page_url)['path']);

    // full url contains (raw) query parms string
    $current_page_fullurl = home_url(add_query_arg(array($_GET), $wp->request));
    $aweber_lists = $this->extract_awlists_from_url($current_page_fullurl);

    if ($current_page_slug === $confirm_page_slug) {
      echo 'Subscribing user...<br/>';
      // printf('<pre>%s</pre>', var_export($aweber_lists, true));
      // send post requests
      $this->test_posting();
    } else {
      echo 'Doing nothing on this page. Checks done: ' . $current_page_slug . ' === ' . $confirm_page_slug;
    }
  }

  public function test_posting() {
    $stack = HandlerStack::create();
    $client = new Client([
      'base_uri' => 'https://api.aweber.com/1.0/',
      'handler' => $stack,
      'auth' => 'oauth'
    ]);
    
    $app_id = 'dadb3b05';
    $access_token_url = 'https://auth.aweber.com/1.0/oauth/access_token';
    $authorize_url = "https://auth.aweber.com/1.0/oauth/authorize_app/$app_id";
    $api_url = 'https://api.aweber.com/1.0/accounts';

    // if authenticating through app_id remember that you get only one
    // attempt per $authorization_code. debugging it is painful.
    $authorization_code = 'AzPHI215Es40C0Eagn2yrZEI|uL4RDPanw8q8i87uPdnMG4rN1i3zF2L68PHdamgL|AqXAQPlVTGg6Y7QLxO1AXf4X|wFOJQlWOXoGXig7lIwuLBURol0hDTj0QwoSc1SV8|osvry8|';
    $authorization_code_exploded = explode('|', $authorization_code);
    $auth = new Oauth1([
      'consumer_key'    => $authorization_code_exploded[0],
      'consumer_secret' => $authorization_code_exploded[1],
      'token'           => $authorization_code_exploded[2],
      'token_secret'    => $authorization_code_exploded[3],
      'verifier'        => $authorization_code_exploded[4]
    ]);
    $stack->push($auth);
    // $res = $client->post($access_token_url);

    // $res_body = $res->getBody();
    // echo "RES->BODY: $res_body";
    // Save the decoded response ($keys) in database along with 
    // consumer_secret and consumer_key generated in the previous step.
    $res_body = 'oauth_token_secret=9eAI3hUW5ByMibtJFm6Lt04YLB7dshw175hLAj4M&oauth_token=AgsqKpz3YaErM5z5s40qPrcV';
    $keys = array();
    parse_str($res_body, $keys);
    $stack->remove($auth);

    $auth = new Oauth1(array(
      'consumer_key'    => $authorization_code_exploded[0],
      'consumer_secret' => $authorization_code_exploded[1],
      'token'           => $keys['oauth_token'],
      'token_secret'    => $keys['oauth_token_secret']
    ));
    $stack->push($auth);
    $res = $client->get('accounts');
    $res_body = $res->getBody();
    echo "RES? $res_body";
    
    return $auth;
  }
}


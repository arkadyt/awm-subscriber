<?php
/**
 * @package awm-subscriber
 */
// example AWeber request url (all joined):
// ?email=wp-testing%40arkadyt.com&from=wp-testing%40arkadyt.com&meta_adtracking=my%20web%20form&meta_message=1001
// &name=Thug&unit=awlist5279237&add_url=http%3A%2F%2Fwp-testing.arkadyt.com%2Fthank-you%2F&add_notes=255.255.255.255
// &custom%20awlist5279237=yes&custom%20awlist5000207=yes&custom%20awlist01290129=yes

namespace inc\base;

/**
 * Core service of this plugin.
 */
final class Subscriber extends BaseController {
  public function register() {
    add_filter('query_vars', array($this, 'add_query_vars_filter'));
    add_action('init', array($this, 'intercept_get_request'));
    add_action('wp', array($this, 'subscribe'));
  }

  /**
   * Unsets query variables forbidden by Wordpress.
   * Like 'name', AWeber sends it in the GET request.
   */
  public function intercept_get_request() {
    if (isset($_GET['name'])) {
      $_GET['subscriberName'] = $_GET['name'];
      unset($_GET['name']);
    }
  }

  /**
   * Allows WP_Query to recognize custom variables from the request url.
   */
  public function add_query_vars_filter($vars) {
    $vars[] = "email";
    $vars[] = "add_url";
    return $vars;
  }

  /**
   * Extracts aweber list id's from the AWeber request url.
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

    // full url contains query parms string
    $current_page_fullurl = home_url(add_query_arg(array($_GET), $wp->request));
    $aweber_lists = $this->extract_awlists_from_url($current_page_fullurl);

    if ($current_page_slug === $confirm_page_slug) {
      echo 'Subscribing user...<br/>';
      printf('<pre>%s</pre>', var_export($aweber_lists, true));
      // send post requests
    } else {
      echo 'Doing nothing on this page. Checks done: ' . $current_page_slug . ' === ' . $confirm_page_slug;
    }
  }
}


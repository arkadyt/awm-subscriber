<?php
/**
 * @package awm-subscriber
 */

namespace inc\base;

/**
 * Core service of this plugin.
 */
final class Subscriber extends BaseController {
  public function register() {
    add_filter('query_vars', array($this, 'add_query_vars_filter'));
    add_action('wp', array($this, 'subscribe'));
  }

  /**
   * Allows WP_Query to recognize custom variables from the request url.
   */
  public function add_query_vars_filter($vars) {
    $vars[] = "kaka";
    return $vars;
  }

  /**
   * Extracts aweber list id's from the AWeber request url.
   */
  public function get_aweber_lists($full_url) {
    // example AWeber request url (all joined):
    // ?email=wp-testing%40arkadyt.com&from=wp-testing%40arkadyt.com&meta_adtracking=my%20web%20form&meta_message=1001
    // &name=Thug&unit=awlist5279237&add_url=http%3A%2F%2Fwp-testing.arkadyt.com%2Fthank-you%2F&add_notes=255.255.255.255
    // &custom%20awlist5279237=yes&custom%20awlist5000207=yes&custom%20awlist01290129=yes

    // splits url into chunks
    $parsed_url = parse_url($full_url);
    // decode query string
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
   * once he visits 'confirmation_page_url'.
   *
   * AWeber supplies this data in the GET url which is parsed by WP_Query.
   *
   * Finally user specifies lists that he wants to subscribe to in the
   * initial form submission.
   */
  public function subscribe() {
    global $wp;

    $current_page_slug = $wp->query_vars['pagename'];
    $confirm_page_slug = 'thank-you';

    $full_url = home_url(add_query_arg(array($_GET), $wp->request));
    $aweber_lists = $this->get_aweber_lists($full_url);

    if ($current_page_slug === $confirm_page_slug) {
      echo 'Subscribing user...<br/>';
      printf('<pre>%s</pre>', var_export($aweber_lists, true));
    } else {
      echo 'Doing nothing on this page.';
    }

    // GET url that long/complicated causes wordpress to shoot 404 back.
    // What you can do:
    // - send POST requests from JavaScript (where to get AWeber API key from then? It's supposed to come from MySQL)
    // - redirect user to the normal version of the thank you page: /thank-you/ (will create loop condition)
  }
}


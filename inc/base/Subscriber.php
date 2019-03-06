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
   * Will try to subscribe user to multiple AWeber lists of his choice
   * once he visits 'confirmation_page_url'.
   *
   * AWeber supplies this data in the GET url which is parsed by WP_Query.
   *
   * Finally user specifies lists that he wants to subscribe to in the
   * initial form submission.
   */
  public function subscribe() {
    global $wp_query;
    $confirmation_page_url = 'thank-you';
    if ($wp_query->query['pagename'] === $confirmation_page_url) {
      echo 'Subscribing user...';
    } else {
      echo 'Doing nothing on this page.';
    }

    // echo 'SSSSSSSSSSSSSSSSSSSSSSSSSSSSSS Visited:' . $wp_query->query['pagename'] . 'ZZZ';
    // printf('<pre>%s</pre>', var_export( $wp_query, true ));
  }
}


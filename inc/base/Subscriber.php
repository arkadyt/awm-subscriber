<?php
/**
 * @package awm-subscriber
 */

namespace inc\base;

final class Subscriber extends BaseController {
  public function register() {
    add_action('wp', array($this, 'subscribe'));
  }

  public function subscribe() {
    // echo 'SSSSSSSSSSSSSSSSSSSSSSSSSSSSSS Visited:' . $wp_query->query['pagename'] . 'ZZZ';
    // printf('<pre>%s</pre>', var_export( $wp_query, true ));

    global $wp_query;
    $confirmation_page_url = 'thank-you';
    if ($wp_query->query['pagename'] === $confirmation_page_url) {
      echo 'Subscribing user...';
    } else {
      echo 'Doing nothing on this page.';
    }
  }
}


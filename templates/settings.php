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

use inc\Init;
use inc\core\Subscriber;
use inc\base\BaseController;

final class SettingsTemplate extends BaseController {
  public $subscriber;
  public $authorize_url;

  public function __construct() {
    parent::__construct();

    $this->subscriber = Init::get_instance(Subscriber::class);
    $this->authorize_url = $this->subscriber->authorize_url;
  }

  /**
   * Will return false/true if operation failed or succeeded respectively.
   */
  public function initialize() {
    $success = $this->subscriber->initialize($this->get_response_code());
    if ($success) {
      update_option($this->optname_aweber_response_code, 'initialized');
    } else {
      update_option($this->optname_aweber_response_code, 'failed');
    }
    return $success;
  }

  /**
   * The response code from AWeber dev app authorization page.
   */
  public function get_response_code() {
    return get_option($this->optname_aweber_response_code);
  }

  /**
   * Has the user authorized plugin yet?
   */
  public function is_authorized() {
    return !!$this->get_response_code();
  }
  
  /**
   * Have permanent keys been fetched?
   */
  public function is_initialized() {
    return $this->get_response_code() === 'initialized';
  }
}

$__awmss = new SettingsTemplate();
?>

<div class="wrap">
  <h1>AWM Subscriber Settings</h1>
  <?php if (!$__awmss->is_authorized()): ?>
    <p>Connect this plugin to your AWeber account by following few simple steps:</p>

    <form method="post" action="options.php">
      <?php settings_fields($__awmss->groupname_plugin_settings); ?>
      <?php do_settings_sections($__awmss->groupname_plugin_settings); ?>

      <h2>Step 1</h2>
      <p style="margin-left: 10px;">Visit AWeber website by following this link:
        <a target="_blank" rel="noopener noreferrer" href="<?php echo $__awmss->authorize_url ?>">
          <?php echo $__awmss->authorize_url ?>
        </a><br/>
        Login into your account and copy the authorization code.
      </p>

      <h2>Step 2</h2>
      <div style="margin-left: 10px;">
        <label for="<?php echo $__awmss->optname_aweber_response_code ?>">
          Then paste that code here:
        </label><br/>
        <input type="text" name="<?php echo $__awmss->optname_aweber_response_code ?>" value="" />
      </div>

      <h2>Step 3</h2>
      <p style="margin-left: 10px;">Finish configuration by clicking the button below:</p>
      <?php submit_button('Authorize plugin'); ?>
    </form>
  <?php else: ?>
    <?php if (!$__awmss->is_initialized()) $__awmss->initialize(); ?>
    <?php if ($__awmss->is_initialized()): ?>
      <form method="post" action="options.php">
        <?php settings_fields($__awmss->groupname_plugin_settings); ?>
        <?php do_settings_sections($__awmss->groupname_plugin_settings); ?>

        <h2>Plugin was successfully authorized!</h2>
        <p style="margin-left: 10px;">
          If you have any problems with the plugin you may try to <b>reauthorize it</b> by clicking the <b>Remove authorization</b> button below.<br/>
          If that does not help, try reactivating the plugin through <b>Sidebar > Plugins > Installed plugins</b> page or reinstalling it.
        </p>
        <input type="hidden" name="<?php echo $__awmss->optname_aweber_response_code ?>" value="" />
        <?php submit_button('Remove authorization'); ?>
      </form>
    <?php else: ?>
      <form method="post" action="options.php">
        <?php settings_fields($__awmss->groupname_plugin_settings); ?>
        <?php do_settings_sections($__awmss->groupname_plugin_settings); ?>

        <h2>Something went wrong!</h2>
        <p style="margin-left: 10px;">
          Please make sure that you copy pasted the code correctly. DO NOT modify it or alter in any way.<br/>
          Also keep in mind that you <b>can't reuse</b> authorization codes. You'll have to generate a new one.<br/>
          Click on a button below to try again:
        </p>
        <input type="hidden" name="<?php echo $__awmss->optname_aweber_response_code ?>" value="" />
        <?php submit_button('Try again'); ?>
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <h1>How this plugin works</h1>
  <h2>1. Visitor subscribes to your main AWeber list.</h2>
  <p style="margin-left: 10px;">
    Bob comes to your website, decides he'd like to receive news from you in his email inbox.<br/>
    He fills out the form and discovers that he can subscribe to additional lists as well.<br/>
    He makes a selection and proceeds by clicking on <b>'Submit'</b>.
  </p>
  <h2>2. Visitor receives the confirmation email.</h2>
  <p style="margin-left: 10px;">
    Bob receives confirmation email in his inbox, opens it up and clicks on <b>'Confirm'</b> button.<br/>
    He is immediately taken to your website <b>where this plugin subscribes him to additional lists</b> of his choice.
  </p>
  <h2>3. Visitor receives additional confirmation emails.</h2>
  <p style="margin-left: 10px;">
    Bob receives additional confirmation emails from every list he decided to subscribe to <b>additionally.</b><br/><br/>
    Please keep in mind: AWeber does not allow subscribing users to mailing lists without their permission.<br/>
    As they say, "That's done to prevent malicious subscriptions and false spam allegations".<br/><br/>
    In the end, though, you don't have to create the whole myriad of forms and user doesn't have to fill them all out!<br/>
    User would just need to spend 10-30 seconds on confirming the additional subscriptions in his email inbox!
  </p><br/>

  <h1>Here's what's required from you:</h1>
  <p>
    Good news! You still manage everything related to email campaigns at AWeber website.<br/>
    This plugin does not require any additional configuration on the Wordpress side.<br/><br/>
    There are only two rules you have to follow at AWeber in order to enable automatic subscriptions:<br/>
  </p>
  <h2>1. Make sure you use proper 'Field Names' for your custom fields on your AWeber form.</h2>
  <p style="margin-left: 10px;">
    When you create a custom field for your AWeber form (<b>List Options > Custom Fields</b>),<br/>
    it first asks you which '<b>Field Name</b>' you want to use, then on the next slide,<br/>
    it asks for 'Field Label' and some other options that are not relevant for the plugin.<br/><br/>
    Make sure that for every new field you create, you set the '<b>Field Name</b>' value to<br/>
    the '<b>ID</b>' of the corresponding AWeber list (like <b>awlist5279237</b>).<br/><br/>
    Automatic subscriptions are not possible without an association system like this.<br/>
    You can get a list ID at <b>List Options > List Settings > (look under the list name)</b> 
  </p>
  <h2>2. Make sure your main AWeber list redirects people to your website upon subscription confirmation.</h2>
  <p style="margin-left: 10px;">
    By saying main list, I mean list that your AWeber form on your website is bound to.<br/>
    You can set this setting in <b>List Options > List Settings > Confirmation Message > Confirmation Success Page</b>.<br/><br/>
    Redirection to your website is important because <b>that's where this plugin does it's job</b>, on your website.<br/>
    I recommend setting up a separate page for that. It doesn't have to be nothing fancy, but just being a <b>separate</b> page.
  </p><br/>
</div>

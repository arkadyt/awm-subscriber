<?php 
use inc\Init;
use inc\core\Subscriber;

$awm_sub_sett_optname_rcode = 'awm_subscriber_response_code';
$awm_sub_sett_subs_instance = Init::get_instance(Subscriber::class);

$awm_sub_sett_authorize_url = $awm_sub_sett_subs_instance->authorize_url;
$awm_sub_sett_response_code = get_option($awm_sub_sett_optname_rcode);

$awm_sub_sett_is_first_time = !$awm_sub_sett_response_code;
$awm_sub_sett_is_initialized = $awm_sub_sett_response_code === 'initialized';
?>

<div class="wrap">
  <h1>AWM Subscriber Settings</h1>
  <?php if ($awm_sub_sett_is_first_time): ?>
    <p>Connect this plugin to your AWeber account by following few simple steps:</p>

    <form method="post" action="options.php">
      <?php settings_fields('awm_subscriber_settings'); ?>
      <?php do_settings_sections('awm_subscriber_settings'); ?>

      <h2>Step 1</h2>
      <p style="margin-left: 10px;">Visit AWeber website by following this link:
        <a target="_blank" rel="noopener noreferrer" href="<?php echo $awm_sub_sett_authorize_url ?>">
          <?php echo $awm_sub_sett_authorize_url ?>
        </a><br/>
        Login into your account and copy the authorization code.
      </p>

      <h2>Step 2</h2>
      <div style="margin-left: 10px;">
        <label for="<?php echo $awm_sub_sett_optname_rcode ?>">
          Then paste that code here:
        </label><br/>
        <input type="text" name="<?php echo $awm_sub_sett_optname_rcode ?>" value="" />
      </div>

      <h2>Step 3</h2>
      <p style="margin-left: 10px;">Finish configuration by clicking the button below:</p>
      <?php submit_button(); ?>
    </form>
  <?php else: ?>
    <?php 
      if (!$awm_sub_sett_is_initialized) { 
        $result = $awm_sub_sett_subs_instance->initialize($awm_sub_sett_response_code);
        if ($result) {
          update_option($awm_sub_sett_optname_rcode, "initialized");
        }
      } 
    ?>
    <form method="post" action="options.php">
      <?php settings_fields('awm_subscriber_settings'); ?>
      <?php do_settings_sections('awm_subscriber_settings'); ?>

      <h2>Plugin was successfully authorized!</h2>
      <p>
        If you have any problems with the plugin you may try to <b>reauthorize it</b> by clicking the <b>Remove authorization</b> button below.<br/>
        If that does not help, try reactivating the plugin through <b>Sidebar > Plugins > Installed plugins</b> page or reinstalling it.
      </p>
      <input type="hidden" name="<?php echo $awm_sub_sett_optname_rcode ?>" value="" />
      <?php submit_button('Remove authorization'); ?>
    </form>
  <?php endif; ?>

  <h1>How this plugin works:</h1>
  <h2>1. Visitor subscribes to your main AWeber list.</h2>
  <p>
    Bob comes to your website, decides he'd like to receive news from you in his email inbox.<br/>
    He fills out the form and discovers that he can subscribe to additional lists as well.<br/>
    He makes a selection and proceeds by clicking on <b>'Submit'</b>.
  </p>
  <h2>2. Visitor receives the confirmation email.</h2>
  <p>
    Bob receives confirmation email in his inbox, opens it up and clicks on <b>'Confirm'</b> button.<br/>
    He is immediately taken to your website <b>where this plugin subscribes him to additional lists</b> of his choice.
  </p>
  <h2>3. Visitor receives additional confirmation emails.</h2>
  <p>
    Bob receives additional confirmation emails from every list he decided to subscribe to <b>additionally.</b></br></br>
    Please keep in mind: AWeber does not allow subscribing users to mailing lists without their permission.</br>
    As they say, "That's done to prevent malicious subscriptions and false spam allegations".</br></br>
    In the end, though, you don't have to create the whole myriad of forms and user doesn't have to fill them all out!</br>
    User would just need to spend 10-30 seconds on confirming the additional subscriptions in his email inbox!
  </p></br>

  <h1>Here's what's required from you:</h1>
  <p>
    Good news! You still manage everything related to email campaigns at AWeber website.<br/>
    This plugin does not require any additional configuration on the Wordpress side.<br/><br/>

    There are only two rules you have to follow at AWeber in order to enable automatic subscriptions:</br>
  </p>
  <h2>1. Make sure you use proper 'Field Names' for your custom fields on your AWeber form.</h2>
  <p>
    When you create a custom field for your AWeber form (<b>List Options > Custom Fields</b>),<br/>
    it first asks you which '<b>Field Name</b>' you want to use, then on the next slide,<br/>
    it asks for 'Field Label' and some other options that are not relevant for the plugin.<br/><br/>

    Make sure that for every new field you create, you set the '<b>Field Name</b>' value to<br/>
    the '<b>ID</b>' of the corresponding AWeber list (like <b>awlist5279237</b>).<br/><br/>

    Automatic subscriptions are not possible without an association system like this.<br/>
    You can get a list ID at <b>List Options > List Settings > (look under the list name)</b> 
  </p>
  <h2>2. Make sure your main AWeber list redirects people to your website upon subscription confirmation.</h2>
  <p>
    By saying main list, I mean list that your AWeber form on your website is bound to.<br/>
    You can set this setting in <b>List Options > List Settings > Confirmation Message > Confirmation Success Page</b>.<br/><br/>
    Redirection to your website is important because <b>that's where this plugin does it's job</b>, on your website.</br>
    I recommend setting up a separate page for that. It doesn't have to be nothing fancy, but just being a <b>separate</b> page.
  </p>
</div>

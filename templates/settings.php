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
        $awm_sub_sett_subs_instance->initialize($awm_sub_sett_response_code);
        update_option($awm_sub_sett_optname_rcode, "initialized");
      } 
    ?>
    <form method="post" action="options.php">
      <?php settings_fields('awm_subscriber_settings'); ?>
      <?php do_settings_sections('awm_subscriber_settings'); ?>

      <h2>Plugin was successfully authorized!</h2>
      <p>If you have any problems with the plugin you may try to <b>reauthorize it</b> by clicking the button below:</p>
      <input type="hidden" name="<?php echo $awm_sub_sett_optname_rcode ?>" value="" />
      <input type="submit" class="button-primary" value="Remove authorization" />
      <p>If that does not help, try reactivating the plugin through <b>Sidebar > Plugins > Installed plugins</b> page or reinstalling it.</p>
    </form>

    <h1>How this plugin works:</h1>
    <p>Lorem ipsum!</p>
  <?php endif; ?>
</div>

<?php 
use inc\Init;
use inc\core\Subscriber;

$awm_subscriber_settings_optname_rcode = 'awm_subscriber_response_code';
$awm_subscriber_settings_authorize_url = Init::get_instance(Subscriber::class)->authorize_url;
$awm_subscriber_settings_is_first_time = !get_option($awm_subscriber_settings_optname_rcode);
?>

<div class="wrap">
  <h1>AWM Subscriber</h1>
  <?php if ($awm_subscriber_settings_is_first_time): ?>
    <p>Configure this plugin by following few simple steps:</p>

    <form method="post" action="options.php">
      <?php settings_fields('awm_subscriber_settings'); ?>
      <?php do_settings_sections('awm_subscriber_settings'); ?>

      <h2>Step 1</h2>
      <p style="margin-left: 10px;">Visit AWeber website by following this link:
        <a target="_blank" rel="noopener noreferrer" href="<?php echo $authorize_url ?>">
          <?php echo $authorize_url ?>
        </a><br/>
        Login into your account and copy the AWeber authorization code.
      </p>

      <h2>Step 2</h2>
      <div style="margin-left: 10px;">
        <label for="<?php echo $awm_subscriber_settings_optname_rcode ?>">
          Then paste that code here:
        </label><br/>
        <input type="text" name="<?php echo $awm_subscriber_settings_optname_rcode ?>" value="" />
      </div>

      <h2>Step 3</h2>
      <p style="margin-left: 10px;">Finish configuration by clicking the button below:</p>
      <?php submit_button(); ?>
    </form>
  <?php else: ?>
    <h2>Plugin was successfully configured!</h2>
    <h2>How this plugin works:</h2>
    <p>Lorem ipsum!</p>
  <?php endif; ?>
</div>

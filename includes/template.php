<form name="wpmailauthform" id="wpmailauthform" action="<?php echo $url;?>" method="GET">
<input type="hidden" name="wpmailauth_step" value="verify" />
<input type="hidden" name="user_id" value="<?php echo $id;?>" />
<p>
<label for="user_login">
<?php _e('Verification code')?><br />
    <input type="text" name="wpmailauth_token" class="input" value="" size="20" />
</label>
</p>
<p class="forgetmenot">
<a href="<?php echo $url;?>">Log in as another user</a>
</p>
<p class="submit">
<input
    type="submit"
    name="wp-submit"
    id="wp-submit"
    class="button button-primary button-large"
    value="<?php esc_attr_e('Verify');?>" />
</p>
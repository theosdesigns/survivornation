<div class="barc-main">
    <div class="barc-title-area">
        <div class="barc-title">
            <img src="<?php echo $this->plugin_url; ?>/images/title-barc-for-wp.png" />
        </div>
        <div class="barc-phone">
            <p><?php _e('Need help? Give us a call:', self::ld); ?></p>
            <img src="<?php echo $this->plugin_url; ?>/images/ico-phone.png" /><span>(800) 633-4029</span>
        </div>
        <br class="clear" />
    </div>
    <div id="verification-wrapper" class="content-wrapper<?php echo ($activated?' verified':''); ?>">
        <p class="content-header">
            <img src="<?php echo $this->plugin_url; ?>/images/title-verification.png" />
        </p>
        <p class="content-description"><?php _e('Barc is already on your page! Now you just need to verify your installation to gain complete control, as well as additional features and benefits as the account owner.', self::ld); ?></p>
        <div id="verification-form"> <span><?php _e('<b>Enter</b> your username:', self::ld); ?></span>
            <input type="text" placeholder="<?php esc_attr_e('Your Barc username', self::ld); ?>" name="barc_code" value="<?php echo $username; ?>" ></input>
            <button name="verify_button" class="button-barc button button-primary" value="1"><?php _e('Verify', self::ld); ?></button> <span><?php _e('or', self::ld); ?> <a href="http://barc.com/app#signup" target="_blank"><?php _e('Sign Up', self::ld); ?></a> <?php _e('now.', self::ld); ?></span>
            <span class="spinner" id="verify_loader"></span>
            <div id="barc_submit_message_failed"><?php _e('Please try again. Activation was not successful.', self::ld); ?></div>
        </div>
        <div id="verification-completed"> <span> <?php _e('Hello', self::ld); ?>, </span>
            <span class="verified-username"><?php echo $username; ?></span>!
            <div class="button button-primary button-green"><?php _e('Verified!', self::ld); ?></div> <span><?php _e('Click', self::ld); ?>
            <a href="#" onclick="return false;" id="unverify-link"><?php _e('here', self::ld); ?></a>
            <?php _e('to change the account associated with', self::ld); ?>
            <span id="verified-url"><?php echo $host; ?></span>
        </span>
    </div>
</div>
<br/>
<div id="settings-wrapper" class="content-wrapper <?php echo ($activated?' verified':''); ?>">
    <p class="content-header">
        <img src="<?php echo $this->plugin_url; ?>/images/title-settings.png" />
    </p>
    <p class="content-description"><?php _e("After you verify your account you can access additional settings.", self::ld); ?></p>
    <div id="external-settings">
        <span> <?php _e('Configure your other <b>Settings</b> with Barc:', self::ld); ?></span>
        <a<?php echo (!$activated?' disabled="1"':''); ?> onclick="return !this.getAttribute('disabled');" class="button button-primary button-barc button-barc-settings" href="http://barc.com/?__sess=%7B%22url%22%3A%22http%3A%2F%2Fbarc.com%2Finstall%22%2C%22mode%22%3A%22inline%22%7D#admin" target="_blank"><div class="ico-gear"></div><?php _e('Settings', self::ld); ?></a>
    </div>
    <hr/>
    <p> <?php _e('For use with the <b>inline</b> option (Change in Barc Settings):', self::ld); ?></p>
    <img src="<?php echo $this->plugin_url; ?>/images/inline-help.jpg" />
    <div id="page-allocation"> <span><?php _e("Allocate Barc to a <b>specific page?</b>", self::ld); ?></span>
        <div class="select-wrapper">
            <?php
            $page_id = get_option($class.'_page', 0);
            ?>
            <select name="barc_post_id"<?php echo (!$activated?' disabled':''); ?>>
              <option value="0"><?php _e('Please select', self::ld); ?></option>
              <option value="-1"<?php echo ($page_id == -1?' selected':''); ?>><?php _e('Replace commenting area', self::ld); ?></option>
              <?php
              foreach($pages as $page)
                  echo '<option value="'.$page->ID.'"'.($page_id == $page->ID?' selected':'').'>'.$page->post_title.'</option>';
              ?>
          </select>
          <span class="spinner barc-loader" id="select_page_loader"></span>
          <br class="clear" /><br />
          <label for="content_position_1"><input type="radio" id="content_position_1"<?php echo $position == 0?' checked':''; ?> name="content_position" value="0" /> <?php _e('Show above the content', self::ld); ?></label><br />
          <label for="content_position_2"><input type="radio" id="content_position_2"<?php echo $position == 1?' checked':''; ?> name="content_position" value="1" /> <?php _e('Show below the content', self::ld); ?></label>
      </div>
  </div>
</div>

<br/>
<div id="subscription-wrapper" class="content-wrapper">
    <p class="content-header">
        <img src="<?php echo $this->plugin_url; ?>/images/title-premium.png" />
    </p>
    <p class="content-description"><?php _e("Upgrade to premium to gain control of your websites chat!", self::ld); ?></p>
    <div id="premium-features">
        <p id="feature-header"><?php _e('Buy <span>Barc Premium</span> to unlock additional options:', self::ld); ?></p>
        <ul id="feature-list">
            <li>
                <img src="<?php echo $this->plugin_url; ?>/images/ico-check.png" /> <span> <?php _e('Delete any comment', self::ld); ?> </span>
            </li>
            <li>
                <img src="<?php echo $this->plugin_url; ?>/images/ico-check.png" /> <span> <?php _e('Ban Users', self::ld); ?> </span>
            </li>
            <li>
                <img src="<?php echo $this->plugin_url; ?>/images/ico-check.png" /> <span> <?php _e('Add additional Moderators', self::ld); ?> </span>
            </li>
            <li>
                <img src="<?php echo $this->plugin_url; ?>/images/ico-check.png" /> <span> <?php _e('Remove the global room', self::ld); ?> </span>
            </li>
        </ul>
    </div>
    <div id="purchase-subscription">
        <div id="monthly" class="subscription-plan <?php echo ($paymentPlan == 'monthly'?' unlocked':($paymentPlan == 'yearly'? 'disabled':'')); ?>">
            <span class="subscription-price">$5.00</span><span class="subscription-period ">/<?php _e('mo', self::ld); ?></span>

            <div class="unlocked-button"><?php _e('Unlocked', self::ld); ?></div>
            <div class="unlock-button ">
                <img src="<?php echo $this->plugin_url; ?>/images/ico-lock.png" /><span>Unlock!</span>
            </div>
        </div>
        <div id="yearly" class="subscription-plan <?php echo ($paymentPlan == 'yearly'?' unlocked':($paymentPlan == 'monthly'? 'disabled':'')); ?>">
            <span class="subscription-price">$50.00</span><span class="subscription-period ">/<?php _e('yr', self::ld); ?></span>

            <div class="unlocked-button"><?php _e('Unlocked', self::ld); ?></div>
            <div class="unlock-button ">
                <img src="<?php echo $this->plugin_url; ?>/images/ico-lock.png" /><span><?php _e('Unlock!', self::ld); ?></span>
            </div>
        </div>
    </div>
    <div id="unsubscribe-info">
        <span> <?php _e('*You can unsubscribe within Barc\'s settings.', self::ld); ?> </span>
    </div>
</div>
<div id="barc-iframe-wrapper">
  <iframe id="barc-iframe" src="https://barc.com/payment" scrolling='no'></iframe>
</div>

<div class="rate-plugin">
    <span class="title"><?php _e('RATE OUR PLUGIN', self::ld); ?></span>
    <img src="<?php echo $this->plugin_url; ?>/images/stars.png" width="92" height="17" />
    <br /><br />
    <span><?php echo sprintf(__("Please rate our plugin if you have time by clicking %shere%s!", self::ld), '<a href="http://wordpress.org/plugins/barc-chat/" target="_blank">', '</a>'); ?></span>
</div>
</div>
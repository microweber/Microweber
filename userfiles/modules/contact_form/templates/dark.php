<?php

/*

  type: layout

  name: Dark

  description: Dark theme for your website

  icon: dark.png

*/

?>




<script>



mw.require("<?php print $config['url_to_module']; ?>css/style.css", true);


</script>

<div class="contact-form-container contact-form-template-dark">
    <div class="contact-form">
        <div class="edit" data-field="contact_form_title" rel="newsletter_module" data-id="<?php print $params['id'] ?>">
          <h3 class="element contact-form-title"><?php _e("Leave a Message"); ?></h3>
        </div>
        <form class="mw_form" data-form-id="<?php print $form_id ?>" name="<?php print $form_id ?>" method="post" >
  <module type="custom_fields" data-id="<?php print $params['id'] ?>" data-for="module"  default-fields="name,email,text"   />
            <div class="control-group form-group">
                <?php if(get_option('disable_captcha', $params['id']) !='y'): ?>
                    <label class="custom-field-title"><?php _e("Enter Security code"); ?></label>
                    <div class="captcha-holder">
                      <input name="captcha" type="text" required class="mw-captcha-input"/>
                      <img onclick="mw.tools.refresh_image(this);" class="mw-captcha-img" id="captcha-<?php print $form_id; ?>" src="<?php print api_link('captcha') ?>" />
                      <span class="ico irefresh" onclick="mw.tools.refresh_image(mwd.getElementById('captcha-<?php print $form_id; ?>'));"></span>
                    </div>
                    <input type="submit" class="cft-submit pull-right" style="margin-left: 12px;"  value="<?php _e("Send Message"); ?>" />
                <?php else:  ?>
                    <input type="submit" class="cft-submit pull-right"  value="<?php _e("Send Message"); ?>" />
                <?php endif; ?>
            </div>
        </form>
    </div>
    <div class="message-sent" id="msg<?php print $form_id ?>">
        <span class="message-sent-icon message-sent-icon-orange"></span>
        <p><?php _e("Your Email was sent successfully"); ?> </p>
    </div>
</div>

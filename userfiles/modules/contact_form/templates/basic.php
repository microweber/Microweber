<?php

/*

type: layout

name: Basic

description: Basic contact form

*/

?>
<div class="mw-contact-form-basic">
  <div class="edit" data-field="contact_form_title" rel="newsletter_module" data-id="<?php print $params['id'] ?>">
      <h3>Write us a letter</h3>
      <hr>
  </div>
  <form class="mw_form" data-form-id="<?php print $form_id ?>" name="<?php print $form_id ?>" method="post" >
    <module type="custom_fields" data-id="<?php print $params['id'] ?>" data-for="module"  default-fields="name,email,text"   />
    <?php if(get_option('disable_captcha', $params['id']) !='y'): ?>
      <div class="control-group form-group">
        <label><?php _e("Security code"); ?></label>
        <div class="input-prepend">
          <span class="add-on" style="width: 100px;background: white"><img width="100" class="mw-captcha-img" src="<?php print api_link('captcha') ?>" /></span>
          <input name="captcha" type="text"  class="mw-captcha-input"/>
        </div>
      </div>
    <?php  endif;?>
    <input type="submit" class="btn btn-default"  value="<?php _e("Submit"); ?>" />
  </form>

</div>




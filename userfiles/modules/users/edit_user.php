<? if(is_admin() == false) { error("Must be admin"); }

$uid = 0;
$rand = uniqid();

$user_params = array();
$user_params['id'] = 0;
if(isset($params['edit-user'])){
$user_params['id'] =intval($params['edit-user']);
}
 
 
 $user_params['limit'] = 1;
$data = get_users($user_params);
if(isset($data[0]) == false){
$data = array();	
$data['id'] = 0;
$data['username'] = '';
$data['password'] = '';
$data['email'] = '';
$data['first_name'] = '';
$data['last_name'] = '';
$data['is_active'] = 'y';
$data['is_admin'] = 'n';
} else {
$data = $data[0];	
}
 
 ?>
<? if(isarr($data )): ?>

<script  type="text/javascript">
mw.require('forms.js');
</script>


<script  type="text/javascript">
_mw_admin_save_user_form<?  print $data['id']; ?> = function(){


 mw.form.post(mw.$('#users_edit_<? print $rand ?>') , '<? print site_url('api/save_user') ?>', function(){
	 
      UserId = this;
	  // mw.reload_module('[data-type="categories"]');
	  mw.reload_module('[data-type="users/manage"]', function(){

	    mw.url.windowDeleteHashParam('edit-user');

        mw.notification.success('<?php _e("All changes saved"); ?>');
        setTimeout(function(){
            mw.tools.highlight(mwd.getElementById('mw-admin-user-'+UserId));
        }, 300);
	  });
	 });


 
 
}



</script>

<div class="mw-o-box <? print $config['module_class'] ?> user-id-<?  print $data['id']; ?>" id="users_edit_<? print $rand ?>">

 <div class="mw-o-box-header" style="margin-bottom: 0;">
    <span class="ico iusers"></span>
    <?php if($data['id'] != 0): ?>

    <span><?php _e("Edit user"); ?> &laquo;<?  print $data['username']; ?>&raquo;</span>

    <?php else: ?>

    <span>Add new user</span>

    <?php endif; ?>
 </div>


 <input type="hidden" class="mw-ui-field" name="id" value="<?  print $data['id']; ?>">
  <div>
      <table border="0" cellpadding="0" cellspacing="0" class="mw-ui-admin-table mw-edit-user-table" width="100%">
      <col width="150px" />
        <tr>
          <td><label class="mw-ui-label">Username</label></td>
          <td><input type="text" class="mw-ui-field" name="username" value="<?  print $data['username']; ?>"></td>
        </tr>
        <tr>
          <td><label class="mw-ui-label">Password</label></td>
          <td>
            <input type="password" class="mw-ui-field" name="password" value="<?  print $data['password']; ?>">
          </td>
        </tr>
        <tr>
          <td><label class="mw-ui-label">Email</label></td>
          <td><input type="text" class="mw-ui-field" name="email" value="<?  print $data['email']; ?>"></td>
        </tr>
        <tr>
          <td><label class="mw-ui-label">First Name</label></td>
          <td><input type="text" class="mw-ui-field" name="first_name" value="<?  print $data['first_name']; ?>"></td>
        </tr>
        <tr>
          <td><label class="mw-ui-label">Last Name</label></td>
          <td><input type="text" class="mw-ui-field" name="last_name" value="<?  print $data['last_name']; ?>"></td>
        </tr>
        <tr>
          <td><label class="mw-ui-label">Is Active?</label></td>
          <td>
            <div onmousedown="mw.switcher._switch(this);" class="mw-switcher unselectable<? if($data['is_active'] == 'n'): ?> mw-switcher-off<? endif; ?>">
                <span class="mw-switch-handle"></span>
                <label>Yes<input type="radio" value="y" name="is_active" <? if($data['is_active'] == 'y'): ?> checked="checked" <? endif; ?>></label>
                <label>No<input type="radio" value="n" name="is_active" <? if($data['is_active'] == 'n'): ?> checked="checked" <? endif; ?>></label>
            </div>
          </td>
        </tr>
        <tr>
          <td><label class="mw-ui-label">Is Admin?</label></td>
          <td>



           <div onmousedown="mw.switcher._switch(this);" class="mw-switcher unselectable<? if($data['is_admin'] == 'n'): ?> mw-switcher-off<? endif; ?>">
                <span class="mw-switch-handle"></span>
                <label>Yes<input type="radio" value="y" name="is_admin" <? if($data['is_admin'] == 'y'): ?> checked="checked" <? endif; ?>></label>
                <label>No<input type="radio" value="n" name="is_admin" <? if($data['is_admin'] == 'n'): ?> checked="checked" <? endif; ?>></label>
            </div>

          </td>
        </tr>
        <tr class="no-hover">
          <td>&nbsp;</td>
          <td>
            <span class="mw-ui-btn mw-ui-btn-medium mw-ui-btn-green right" onclick="_mw_admin_save_user_form<?  print $data['id']; ?>()"><?php _e("Save"); ?></span>
            <span class="mw-ui-btn mw-ui-btn-medium right" style="margin-right: 10px;" onclick="mw.url.windowDeleteHashParam('edit-user');"><?php _e("Cancel"); ?></span>
          </td>
        </tr>
      </table>
  </div>

</div>
<? endif; ?>

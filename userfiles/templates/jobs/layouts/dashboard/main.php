



<? 
if($dashboard_user == false){
$dashboard_user = user_id();
}
 

?>

  <? if($dashboard_user != $user_id) : ?>

  <? 	$is_friend = get_instance()->users_model->realtionsCheckIfUserIsConfirmedFriendWithUser($user_id, $dashboard_user, $is_special = false); 
  $is_admin =  get_instance()->core_model->is_admin();
  if( $is_admin  == true){
	  
	$is_friend = true;  
  }
  ?>

  

  <? else:  ?>

  

  <? $is_friend = true; ?>

   <? endif; ?>

   

   

<script type="text/javascript">

    $(document).ready(function(){

    //  $('div.embedly').embedly({maxWidth: 200, wrapElement: 'div' });

	 // refesh_dashboard(<? print $dashboard_user ?>);
	 
	 refesh_dashboard(<? print $dashboard_user ?>,0,0,1);

    });

	

	
function mark_active_dash_tab(sel){
	$('.dash_tabs li').removeClass('active');
	$(sel).addClass('active');
	
}



function refesh_dashboard($user_id, $hide_friends, $page, $show_all_users){

 

 

 <? if($is_friend  == true): ?>

 if($user_id == false){

	  $user_id = '<? print user_id() ?>';

  }

if(($hide_friends === undefined) || ($hide_friends == 'false')){
$hide_friends = false;	
}

if(($show_all_users === undefined) || ($show_all_users == 'false')){
$show_all_users = false;	
}


if(($page === undefined)){
	$page = 0;
	
} else {
$page = parseInt($page);	
}
$new_page = $page+1;

if($new_page == 1){
$new_page = 2;	
}

//alert($hide_friends);


  	  $("#dashboard_more_link").attr("href", "javascript:refesh_dashboard('"+$user_id+"', '"+$hide_friends+"', '"+$new_page+"', '"+$show_all_users+"');")
  


  $.ajax({

  url: '<? print site_url('api/module') ?>',

  type: "POST",

  data: ({module : 'users/dashboard' ,user_id : $user_id <? if($dashboard_user != user_id()) : ?>,hide_friends : true  <? endif; ?>, hide_friends:$hide_friends, page:$page , show_all_users : $show_all_users }),

  async:true,

	success: function(resp) {


	$('#dashboard_more_link').fadeIn();
	$('#dashboard_content').fadeIn();
if($page == 0){
	$('#dashboard_content').html(resp);
} else {
	$('#dashboard_content').append(resp);
}
	$('div.embedly').embedly({maxWidth: 400, wrapElement: 'div' });

	

	

	}

  });

  <? else: ?>

  $('#dashboard_content').fadeIn();

  <? endif; ?>

}

	 

	

  </script>



<div class="dashboard-main">

 




<?php $has_fr_req = (get_instance()->users_model->realtionsCheckIfUserHasFriendRequestToUser(user_id(),$dashboard_user )) ;

//p($has_fr_req );

?> 



  <? if(($dashboard_user == $user_id) or !$dashboard_user ): ?>

  <? 
  $page = get_instance()->load->model ( 'Statuses_model', 'statuses_model' );

  
  $statusRow = get_instance()->statuses_model->statusesLastByUserId ($dashboard_user);

//p($statusRow);

?>

  <form method="post" action="#" id="update-status">

    <input type="text" name="status" value="<? print $statusRow['status']; ?>" onfocus="this.value=='<? print $statusRow['status']; ?>'?this.value='':''" onblur="this.value==''?this.value='<? print $statusRow['status']; ?>':''" />

    <a onclick="mw.users.User.statusUpdate('#update-status', function() {refesh_dashboard()});" class="xsubmit">Say</a> <small id="update-status-done" style="display:none">Status updated...</small>

  </form>

  <br />

  <? endif; ?>

  <? if($dashboard_user != false) : ?>

  <? if($dashboard_user != $user_id) : ?>

  <?php 

  



$to_user = $dashboard_user;

$author  = get_instance()->users_model->getUserById($to_user); ?>

  <a href="javascript:mw.users.UserMessage.compose(<?php echo $author['id']; ?>);" class="mw_btn_x"><span class="box-ico box-ico-msg title-tip" title="Send new message to <?php echo $author['first_name']; ?>" >Send a message</span></a>

  <?php if (get_instance()->users_model->realtionsCheckIfUserIsFollowedByUser(false,$to_user ) == false) : ?>

  <a href="javascript:mw.users.FollowingSystem.follow(<?php echo $to_user?>);" class="mw_btn_x_orange"><span class="box-ico box-ico-follow title-tip"  title="Add as friend <?php print get_instance()->users_model->getPrintableName($to_user, 'first'); ?>">Add as friend</span></a>

  <?php  else : ?>

  

  

  <a href="javascript:mw.users.FollowingSystem.unfollow(<?php echo $to_user?>);" class="mw_btn_x"><span class="box-ico box-ico-unfollow title-tip"  title="Remove friend <?php print get_instance()->users_model->getPrintableName($to_user, 'first'); ?>"><? if($has_fr_req == false): ?>Remove friend<? else: ?>Cancel friend request<? endif; ?></span></a>

  

  

  

  <? endif; ?>

  <? endif; ?>

  <? endif; ?>

  <? if($dashboard_user != $user_id) : ?>

  <h2><? print user_name($dashboard_user) ?>'s dashboard</h2>

  <?php  else : ?>

  <?

$data = array();

$data['parent_id'] = $dashboard_user;

$subusers = get_instance()->users_model->getUsers($data, $limit = false, $count_only = false);

 $subusers_ids =  get_instance()->core_model->dbExtractIdsFromArray($subusers); 



 ?>



  <div class="whatis_tabs">

    <ul class="tabnav dash_tabs">

      <li class="dash_tab_1"><a href="javascript:refesh_dashboard(<? print $dashboard_user ?>); mark_active_dash_tab('.dash_tab_1')" style="width: 120px">My dashboard</a></li>
      
      
      
      
 
      
        <?  if(empty($subusers)): ?>

  

  <?php  else : ?> 

      <li><a href="javascript:refesh_dashboard('<? print implode(',',  $subusers_ids) ?>',1);" style="width: 90px">My users</a></li>
      
      
        <? endif; ?>
         
        <li class="active dash_tab_2"><a href="javascript:refesh_dashboard(<? print $dashboard_user ?>,0,0,1);  mark_active_dash_tab('.dash_tab_2')" style="width: 90px">News feed</a></li>


    </ul>

  </div>


  <? endif; ?>

  <br />
 
  <div id="dashboard_content" style="display:none">
  

  <h3>You must be friend with <? print user_name($dashboard_user) ?> in order to view its dashboard.</h3>

  

  

  <? if($has_fr_req == false): ?><? else: ?>

  <br />



  <h3 class="green">You have sent a friend request that needs to be approved by <? print user_name($dashboard_user) ?>.</h3>

  <? endif; ?>

  

  </div>
  
  
  <br />

  <div id="dashboard_more"><a  id="dashboard_more_link" style="display:none" class="mw_blue_link" href="">Show more</a></div>



  

  

</div>
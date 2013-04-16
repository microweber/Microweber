<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>Microweber Configuration</title>
<meta charset="utf-8">
<META HTTP-EQUIV="Content-Language" Content="en">
<link type="text/css" rel="stylesheet" media="all" href="<? print INCLUDES_URL; ?>api/api.css"/>
<link type="text/css" rel="stylesheet" media="all" href="<? print INCLUDES_URL; ?>css/liveadmin.css"/>
<link type="text/css" rel="stylesheet" media="all" href="<? print INCLUDES_URL; ?>css/admin.css"/>
<link type="text/css" rel="stylesheet" media="all" href="<? print INCLUDES_URL; ?>css/mw_framework.css"/>
<script type="text/javascript" src="<? print INCLUDES_URL; ?>js/jquery.js"></script>
<?  $rand = uniqid(); ?>
<script  type="text/javascript">




$(document).ready(function(){



	 $('#form_<? print $rand; ?>').submit(function() {


  mw_start_progress();
   $('.mw-install-holder').fadeOut();

  $data = $('#form_<? print $rand; ?>').serialize();
//  alert($data);
  //alert('<? print url_string() ?>');

  $.post("<? print url_string() ?>", $data,
   function(data) {

	    $('.mw_log').html('');
	   if(data != undefined){
		 if(data == 'done'){
			 window.location.href= '<? print site_url('admin') ?>'
		 } else {
		  $('.mw_log').html(data);
		  $('.mw-install-holder').fadeIn();
		 }

	   }
 $('.mw_install_progress').fadeOut();
   });


   return false;

	 });



 });



function mw_start_progress(){


	$('.mw_install_progress').fadeIn();

 var interval = 2, //How much to increase the progressbar per frame
        updatesPerSecond = 1000/60, //Set the nr of updates per second (fps)
        progress =  $('#mw_install_progress_bar'),
        animator = function(){
            progress.val(progress.val()+interval);
          //  $('#val').text(progress.val());
            if ( progress.val()+interval < progress.attr('max')){
               setTimeout(animator, updatesPerSecond);
            } else {
              //  $('#val').text('Done');
                progress.val(progress.attr('max'));
            }
        }

    setTimeout(animator, updatesPerSecond);



}
</script>
<style>
body {
	background: #f4f4f4;
}
.mw-o-box {
	background: white;
	box-shadow:0px 20px 14px -23px #CCCCCC;
}
input[type='text'], input[type='password'] {
	width: 200px;
}
.mw-ui-label {
	display: block;
	float: left;
	width: 155px;
	padding:6px 12px 0 0;
}
.mw_install_progress {
 display: none;
}

</style>
</head>
<body>
<div class="wrapper">
  <div class="page">
    <div class="mw-o-box" style="width: 400px;margin: 100px auto;padding: 20px;">
      <header class="header">
        <h1>Microweber Setup</h1>
        <small class="version">version <? print MW_VERSION ?></small>

        <p><br>Welcome to the Microweber configuration panel, here you can setup your website quickly.</p>
        <div class="custom-nav"></div>
      </header>
      <div class="sep"><span class="left-arrow arrow"></span><span class="right-arrow arrow"></span></div>
      <div class="demo" id="demo-one">
        <div class="description">
          <div class="mw_log"> </div>
          <div class="mw_install_progress">
          <progress max="5000" value="1" id="mw_install_progress_bar"></progress>


          </div>


          <div class="mw-install-holder">


          <? if ($done == false): ?>
          <form method="post" id="form_<? print $rand; ?>">
            <h2>Database setup</h2>
            <div class="hr"></div>

            <div class="mw-ui-field-holder">
              <label class="mw-ui-label">MySQL hostname <span class="mw-help" data-help="The address where your database is hosted.">?</span></label>
              <input type="text" class="mw-ui-field" autofocus="" name="DB_HOST" <? if(isset($data['db'])== true and isset($data['db']['host'])== true and $data['db']['host'] != '{DB_HOST}'): ?> value="<? print $data['db']['host'] ?>" <? endif; ?> />
            </div>
            <div class="mw-ui-field-holder">
              <label class="mw-ui-label">MySQL username <span class="mw-help" data-help="The username of your database.">?</span></label>
              <input type="text" class="mw-ui-field" name="DB_USER" <? if(isset($data['db'])== true and isset($data['db']['user'])== true and $data['db']['user'] != '{DB_USER}'): ?> value="<? print $data['db']['user'] ?>" <? endif; ?> />
            </div>
            <div class="mw-ui-field-holder">
              <label class="mw-ui-label">MySQL password</label>
              <input type="text" class="mw-ui-field" name="DB_PASS" <? if(isset($data['db'])== true and isset($data['db']['pass'])== true  and $data['db']['pass'] != '{DB_PASS}' ): ?> value="<? print $data['db']['pass'] ?>" <? endif; ?> />
            </div>

            <div class="mw-ui-field-holder">
              <label class="mw-ui-label">Database name <span class="mw-help" data-help="The name of your database.">?</span></label>
              <input type="text" class="mw-ui-field" name="dbname" <? if(isset($data['db'])== true and isset($data['db']['dbname'])== true   and $data['db']['dbname'] != '{dbname}'): ?> value="<? print $data['db']['dbname'] ?>" <? endif; ?> />
            </div>

            <div class="mw-ui-field-holder">
              <label class="mw-ui-label">Table prefix <span class="mw-help" data-help="Change this If you want to install multiple instances of Microweber to this database.">?</span></label>
              <input type="text" class="mw-ui-field" name="table_prefix" <? if(isset($data['table_prefix'])== true and isset($data['table_prefix'])!= ''): ?> value="<? print $data['table_prefix'] ?>" <? endif; ?> />
            </div>

            <!-- <div class="mw-ui-field-holder">
              <label class="mw-ui-label">Database type</label>
              <input type="hidden" class="mw-ui-field" name="DB_TYPE" <? if(isset($data['db'])== true and isset($data['db']['type'])== true): ?> value="<? print $data['db']['type'] ?>" <? endif; ?> />
            </div>-->



            <h2>Admin user setup</h2>
            <div class="hr"></div>
            <div class="mw-ui-field-holder">
              <label class="mw-ui-label">Admin username</label>
              <input type="text" class="mw-ui-field" name="admin_username" <? if(isset($data['admin_username'])== true and isset($data['admin_username'])!= ''): ?> value="<? print $data['admin_username'] ?>" <? endif; ?> />
            </div>
            <div class="mw-ui-field-holder">
              <label class="mw-ui-label">Admin password</label>
              <input type="password" class="mw-ui-field" name="admin_password" <? if(isset($data['admin_password'])== true and isset($data['admin_password'])!= ''): ?> value="<? print $data['admin_password'] ?>" <? endif; ?> />
            </div>
            <div class="mw-ui-field-holder">
              <input type="submit" name="submit" class="mw-ui-btn-action right"  value="Install">
            </div>
            <div class="mw_clear"></div>
            <input name="IS_INSTALLED" type="hidden" value="no" id="is_installed_<? print $rand; ?>">
            <input type="hidden" value="UTC" name="default_timezone" />
          </form>
          <? else: ?>
          <h2>Done, </h2>
          <a href="<? print site_url('admin') ?>">click here to to to admin</a> <a href="<? print site_url() ?>">click here to to to site</a>
          <? endif; ?>
           </div>
        </div>
        <!-- .description -->

      </div>
      <!-- .demo -->

    </div>
  </div>
  <!-- .page -->

</div>
<!-- .wrapper -->

</body>
</html>
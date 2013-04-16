<?

 only_admin_access();
 $update_api = new \mw\Update();
 $forced = false;
 if(isset($params['force'])){
	  $forced = 1;
 }
	$iudates = $update_api -> check($forced);




?>


<script>


$(document).ready(function(){

  mw.$("#select_all").commuter(function(){
     mw.check.all('#mw-update-table');
  }, function(){
     mw.check.none('#mw-update-table');
  });

  mw.$(".update-items input:checkbox").commuter(function(){

  }, function(){
     mw.$("#select_all")[0].checked = false;
  });


});

</script>



<form class="mw-select-updates-list" name="form1" method="post">
<table cellspacing="0" cellpadding="0" class="mw-ui-admin-table mw-ui-admin-table-large" id="mw-update-table"  width="100%">
  <colgroup>
    <col width="40">
    <col width="140">
    <col width="777">
  </colgroup>
  <tr class="mw-table-head">
     <td>
        <label class="mw-ui-check"><input type="checkbox" id="select_all" /><span></span></label>
     </td>
     <td colspan="2">
        <span class="posts-selector">
            <span onclick="mw.check.all('#mw-update-table')">Select All</span>/<span onclick="mw.check.none('#mw-update-table')">Unselect All</span>
        </span>
        <input type="submit" value="Install" class="mw-ui-btn mw-ui-btn-blue" />
     </td>
  </tr>


  <? if(isset($iudates["core_update"])): ?>

  <tr class="mw-table-head">
     <td colspan="3">New Microweber version available</td>
  </tr>
  <tr class="update-items">
    <td><label class="mw-ui-check"><input type="checkbox" name="mw_version" value="<? print $iudates["version"] ?>"  /><span></span></label></td>
    <td>
        Microweber
        <? print MW_VERSION ?>
    </td>
    <td>
       <? print $iudates["version"] ?>
       <span class="mw-ui-btn mw-ui-btn-blue mw-ui-admin-table-show-on-hover">Inslall Update</span>
    </td>
  </tr>





  <? endif; ?>
  <? if(isset($iudates["modules"]) and !empty($iudates["modules"])): ?>
  <tr class="mw-table-head">
    <td colspan="3">New module updates are available</td>
  </tr>
  <? foreach($iudates["modules"] as $k => $item): ?>
  <tr class="update-items">
    <td><label class="mw-ui-check"><input type="checkbox" name="modules[]" value="<? print $item["module"] ?>"  /><span></span></label></td>
    <td>
      <label>
       <? if(isset($item["icon"])) : ?>
                <img src="<? print $item["icon"] ?>" alt="" /> <br>

      <?php else: ?>
            <img src="<?php print INCLUDES_URL; ?>img/module_no_icon.png" alt="" /><br>
       <? endif ?>
        <? print $item["name"] ?> <? print $item["version"] ?>
      </label>
    </td>
    <td>

        <span class="mw-ui-btn mw-ui-btn-blue mw-ui-admin-table-show-on-hover">Inslall Update</span>

    </td>
  </tr>
<? endforeach; ?>

<? endif; ?>

<? if(isset($iudates["module_templates"]) and !empty($iudates["module_templates"])): ?>
  <tr class="mw-table-head">
    <td colspan="3">New module templates</td>
  </tr>

  <? foreach($iudates["module_templates"] as $k => $item): ?>
  <tr class="update-items">
    <td><label class="mw-ui-check"><input type="checkbox" name="module_templates[<? print $item["module"] ?>][]" value="<? print $item["layout_file"] ?>"  /><span></span></label></td>
    <td>
      <label>
         <? if(isset($item["icon"])) : ?>
                <img src="<? print $item["icon"] ?>" alt="" /> <br>
                <?php else: ?>
            <img src="<?php print INCLUDES_URL; ?>img/module_no_icon.png" alt="" /><br>
       <? endif ?>

        <? print $item["name"] ?> <? print $item["version"] ?> <em>(<? print $item["module"] ?>)</em>
      </label>
    </td>
    <td>

    <span class="mw-ui-btn mw-ui-btn-blue mw-ui-admin-table-show-on-hover">Inslall Update</span>

    </td>
  </tr>



<? endforeach; ?>

<? endif; ?>

  <? if(isset($iudates["elements"]) and !empty($iudates["elements"])): ?>
  <tr class="mw-table-head">
    <td colspan="3">New layouts updates are available</td>
  </tr>

  <? foreach($iudates["elements"] as $k => $item): ?>
  <tr class="update-items">
    <td><label class="mw-ui-check"><input type="checkbox" name="elements[]" value="<? print $item["module"] ?>"  /><span></span></label></td>
    <td>   <? if(isset($item["icon"])) : ?>
                <img src="<? print $item["icon"] ?>" alt="" /> <br>
                <?php else: ?>
            <img src="<?php print INCLUDES_URL; ?>img/module_no_icon.png" alt="" /><br>
       <? endif ?>
        <label><? print $item["name"] ?> <? print $item["version"] ?></label>
    </td>
    <td>

    <span class="mw-ui-btn mw-ui-btn-blue mw-ui-admin-table-show-on-hover">Inslall Update</span>

    </td>
  </tr>
  <? endforeach; ?>
  <? endif; ?>




</table>

</form>